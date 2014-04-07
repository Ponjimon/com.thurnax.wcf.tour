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
 * @author	Magnus Kühn
 * @copyright	2013-2014 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class TourPackageInstallationPlugin extends AbstractXMLPackageInstallationPlugin {
	/**
	 * @see	\wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::$className
	 */
	public $className = 'wcf\data\tour\TourEditor';
	
	/**
	 * tour steps per tour id
	 * @var array<array>
	 */
	protected $tourSteps = array();
	
	/**
	 * @see	\wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::handleDelete()
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
	 * @see	\wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::prepareImport()
	 */
	protected function prepareImport(array $data) {
		$tourData = array(
			'isDisabled' => (!isset($data['elements']['className']) ? 0 : intval($data['elements']['className'])),
			'visibleName' => $data['elements']['visibleName'],
			'tourTrigger' => $data['elements']['tourTrigger'],
			'identifier' => $data['attributes']['identifier'],
			'steps' => array()
		);
		
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
	 * @param	array<mixed>	$data
	 * @return	array<mixed>
	 */
	protected function prepareStepImport(array $data) {
		$stepData = array(
			'showOrder' => (isset($data['elements']['showOrder']) ? $data['elements']['showOrder'] : null),
			'isDisabled' => (!isset($data['elements']['className']) ? 0 : intval($data['elements']['className'])),
			'target' => $data['elements']['target'],
			'content' => $data['elements']['content']
		);
		
		// get optional values
		if (isset($data['elements']['orientation'])) $stepData['orientation'] = $data['elements']['orientation'];
		if (isset($data['elements']['title'])) $stepData['title'] = $data['elements']['title'];
		if (isset($data['elements']['xOffset'])) $stepData['xOffset'] = $data['elements']['xOffset'];
		if (isset($data['elements']['yOffset'])) $stepData['yOffset'] = $data['elements']['yOffset'];
		if (isset($data['elements']['url'])) $stepData['url'] = $data['elements']['url'];
		if (isset($data['elements']['callbackBefore'])) $stepData['callbackBefore'] = $data['elements']['callbackBefore'];
		if (isset($data['elements']['callbackAfter'])) $stepData['callbackAfter'] = $data['elements']['callbackAfter'];
		
		return $stepData;
	}
	
	/**
	 * @see	\wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::getElement()
	 */
	protected function getElement(\DOMXpath $xpath, array &$elements, \DOMElement $element) {
		switch ($element->tagName) {
			case 'title': // i18n values
			case 'content':
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
	 * @todo Remove after merge of https://github.com/WoltLab/WCF/pull/1636
	 * 
	 * @param	\DOMXPath	$xpath
	 * @param	\DOMElement	$element
	 * @return	array<string>
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
	 * @see	\wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::findExistingItem()
	 */
	protected function findExistingItem(array $data) {
		$sql = "SELECT	*
			FROM	".Tour::getDatabaseTableName()."
			WHERE	identifier = ? AND packageID = ?";
		return array('sql' => $sql, 'parameters' => array($data['identifier'], $this->installation->getPackageID()));
	}
	
	/**
	 * @see	\wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::import()
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
	}
	
	/**
	 * @see	\wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::postImport()
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
	 * @param	string	$fileName
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
