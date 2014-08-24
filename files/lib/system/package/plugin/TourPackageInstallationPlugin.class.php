<?php
namespace wcf\system\package\plugin;
use DOMXPath;
use wcf\data\tour\step\TourStep;
use wcf\data\tour\step\TourStepEditor;
use wcf\data\tour\Tour;
use wcf\data\tour\TourEditor;
use wcf\system\package\DummyPackageInstallationDispatcher;
use wcf\system\WCF;
use wcf\util\XML;

/**
 * Installs, updates and deletes tours.
 *
 * @author    Magnus Kühn
 * @copyright 2013-2014 Thurnax.com
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package   com.thurnax.wcf.tour
 */
class TourPackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin {
	/**
	 * object editor class name
	 *
	 * @var string
	 */
	public $className = 'wcf\data\tour\TourEditor';

	/**
	 * tour steps per tour id
	 *
	 * @var \wcf\data\tour\step\TourStep[]
	 */
	protected $tourSteps = array();

	/**
	 * Deletes the given items.
	 *
	 * @param string[] $items
	 */
	protected function handleDelete(array $items) {
		$sql = "DELETE FROM	".Tour::getDatabaseTableName()."
			WHERE		identifier = ? AND packageID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		foreach ($items as $item) {
			$statement->execute(array($item['value'], $this->installation->getPackageID()));
		}
	}

	/**
	 * Prepares import, use this to map xml tags and attributes
	 * to their corresponding database fields.
	 *
	 * @param string[] $data
	 * @return string[]
	 */
	protected function prepareImport(array $data) {
		$tourData = array('isDisabled' => (!isset($data['elements']['className']) ? 0 : intval($data['elements']['className'])),
			'visibleName' => $data['elements']['visibleName'],
			'tourTrigger' => $data['elements']['tourTrigger'],
			'identifier' => $data['attributes']['identifier'],
			'steps' => array());

		// get optional value
		if (isset($data['elements']['className'])) $tourData['className'] = $data['elements']['className'];

		// prepare steps
		foreach ($data['elements']['steps'] as $stepData) {
			$tourData['steps'][] = $this->prepareStepImport($stepData);
		}

		return $tourData;
	}

	/**
	 * Prepares import, use this to map xml tags and attributes
	 * of tour steps to their corresponding database fields.
	 *
	 * @param string[] data
	 * @return string[]
	 */
	protected function prepareStepImport(array $data) {
		$stepData = array('showOrder' => (isset($data['elements']['showOrder']) ? $data['elements']['showOrder'] : null),
			'isDisabled' => (!isset($data['elements']['className']) ? 0 : intval($data['elements']['className'])),
			'target' => $data['elements']['target'],
			'content' => $data['elements']['content'],
			'showPrevButton' => (!isset($data['elements']['showPrevButton']) ? 1 : intval($data['elements']['showPrevButton'])),);

		// get optional values
		if (isset($data['elements']['placement'])) $stepData['placement'] = $data['elements']['placement'];
		if (isset($data['elements']['title'])) $stepData['title'] = $data['elements']['title'];
		if (isset($data['elements']['xOffset'])) $stepData['xOffset'] = $data['elements']['xOffset'];
		if (isset($data['elements']['yOffset'])) $stepData['yOffset'] = $data['elements']['yOffset'];
		if (isset($data['elements']['url'])) $stepData['url'] = $data['elements']['url'];
		if (isset($data['elements']['ctaLabel'])) $stepData['ctaLabel'] = $data['elements']['ctaLabel'];

		// get callbacks
		if (isset($data['elements']['onPrev'])) $stepData['onPrev'] = $data['elements']['onPrev'];
		if (isset($data['elements']['onNext'])) $stepData['onNext'] = $data['elements']['onNext'];
		if (isset($data['elements']['onShow'])) $stepData['onShow'] = $data['elements']['onShow'];
		if (isset($data['elements']['onCTA'])) $stepData['onCTA'] = $data['elements']['onCTA'];

		return $stepData;
	}

	/**
	 * Sets element value from XPath.
	 *
	 * @param \DOMXPath   $xpath
	 * @param array       $elements
	 * @param \DOMElement $element
	 */
	protected function getElement(\DOMXpath $xpath, array &$elements, \DOMElement $element) {
		switch ($element->tagName) {
			case 'title': // i18n values
			case 'content':
			case 'ctaLabel':
				if (!isset($elements[$element->tagName])) {
					$elements[$element->tagName] = array();
				}

				$elements[$element->tagName][$element->getAttribute('language')] = $element->nodeValue;
				break;
			case 'steps':
				$elements[$element->tagName] = array();
				$steps = $xpath->query('child::*', $element);
				foreach ($steps as $step) {
					$elements[$element->tagName][] = $this->getElementData($xpath, $step);
				}

				break;
			default:
				parent::getElement($xpath, $elements, $element);
		}
	}

	/**
	 * Reads the element data
	 *
	 * @todo Remove after merge of https://github.com/WoltLab/WCF/pull/1636
	 *
	 * @param \DOMXPath   $xpath
	 * @param \DOMElement $element
	 * @return array<string>
	 */
	protected function getElementData(\DOMXPath $xpath, \DOMElement $element) {
		$data = array('attributes' => array(), 'elements' => array(), 'nodeValue' => '');

		// fetch attributes
		$attributes = $xpath->query('attribute::*', $element);
		foreach ($attributes as $attribute) {
			$data['attributes'][$attribute->name] = $attribute->value;
		}

		// fetch child elements
		$items = $xpath->query('child::*', $element);
		foreach ($items as $item) {
			$this->getElement($xpath, $data['elements'], $item);
		}

		// include node value if item does not contain any child elements (eg. pip)
		if (empty($data['elements'])) {
			$data['nodeValue'] = $element->nodeValue;
		}

		return $data;
	}

	/**
	 * Find an existing item for updating, should return sql query.
	 *
	 * @param string[] $data
	 * @return string
	 */
	protected function findExistingItem(array $data) {
		$sql = "SELECT	*
			FROM	".Tour::getDatabaseTableName()."
			WHERE	identifier = ? AND packageID = ?";
		return array('sql' => $sql, 'parameters' => array($data['identifier'], $this->installation->getPackageID()));
	}

	/**
	 * Inserts or updates new items.
	 *
	 * @param string[] $row
	 * @param string[] $data
	 * @return \wcf\datat\tour\Tour
	 */
	protected function import(array $row, array $data) {
		// extract pages
		$steps = $data['steps'];
		unset($data['steps']);

		// import or update action
		/** @var $tour \wcf\data\tour\Tour */
		$tour = parent::import($row, $data);

		// store steps for later import
		if ($steps) {
			$this->tourSteps[$tour->tourID] = $steps;
		}

		return $tour;
	}

	/**
	 * Executed after all items would have been imported, use this hook if you've
	 * overwritten import() to disable insert/update.
	 */
	protected function postImport() {
		// clear tour steps
		$sql = "DELETE FROM	".TourStep::getDatabaseTableName()."
			WHERE		tourID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);

		// import tour steps
		/** @var $tourSteps array<array> */
		foreach ($this->tourSteps as $tourID => $tourSteps) {
			$statement->execute(array($tourID));

			/** @var $stepData array<mixed> */
			foreach ($tourSteps as $stepData) {
				$stepData['tourID'] = $tourID;
				$stepData['showOrder'] = $this->getShowOrder($stepData['showOrder'], $tourID, 'tourID', '_step');

				$this->prepareCreate($stepData);
				TourStepEditor::create($stepData);
			}
		}

		// show notification about imported tours
		WCF::getSession()->register(TourEditor::TOUR_IMPORTED_NOTICE, true);
	}

	/**
	 * Imports a file
	 *
	 * @param string $fileName
	 */
	public static function importFile($fileName) {
		// prepare xml document
		$xml = new XML();
		$xml->load($fileName);

		// import items
		/** @var $pip \wcf\system\package\plugin\TourPackageInstallationPlugin */
		$pip = new static(new DummyPackageInstallationDispatcher('com.thurnax.wcf.tour'));
		$pip->deleteItems($xml->xpath());
		$pip->importItems($xml->xpath());
		$pip->cleanup();
	}
}
