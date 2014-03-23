<?php
namespace wcf\system\package\plugin;
use DOMXPath;
use wcf\data\tour\step\TourStep;
use wcf\data\tour\step\TourStepEditor;
use wcf\data\tour\Tour;
use wcf\data\tour\TourEditor;
use wcf\system\WCF;

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
			WHERE		className = ? AND packageID = ?";
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
			'visibleName' => $data['elements']['visibleName'],
			'tourTrigger' => $data['elements']['tourTrigger'],
			'className' => $data['attributes']['className'],
			'steps' => array()
		);
		
		// prepare steps
		foreach ($data['elements']['steps'] as $stepData) {
			$tourData['steps'][] = $this->prepareStepImport($stepData);
		}
		
		// get optional value
		if (isset($data['elements']['isDisabled'])) $tourData['isDisabled'] = intval($data['elements']['isDisabled']);
		
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
			'target' => $data['elements']['target'],
			'content' => $data['elements']['content'],
			'showOrder' => (isset($data['elements']['showOrder']) ? $data['elements']['showOrder'] : null)
		);
		
		// get optional values
		if (isset($data['elements']['placement'])) $stepData['placement'] = $data['elements']['placement'];
		if (isset($data['elements']['title'])) $stepData['title'] = $data['elements']['title'];
		if (isset($data['elements']['showPrevButton'])) $stepData['showPrevButton'] = (bool) $data['elements']['showPrevButton'];
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
	 * @see	\wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::getElement()
	 */
	protected function getElement(\DOMXpath $xpath, array &$elements, \DOMElement $element) {
		switch ($element->tagName) {
			case 'visibleName': // I18n values
			case 'title':
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
			WHERE	className = ? AND packageID = ?";
		return array('sql' => $sql, 'parameters' => array($data['className'], $this->installation->getPackageID()));
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
		$this->tourSteps[$tour->tourID] = $steps;
	}
	
	/**
	 * @see	\wcf\system\package\plugin\AbstractXMLPackageInstallationPlugin::postImport()
	 */
	protected function postImport() {
		// clear tour steps
		$sql = "DELETE FROM	".TourStep::getDatabaseTableName()."
			WHERE		packageID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->installation->getPackageID()));
		
		// import tour steps
		/** @var $tourSteps array<array> */
		foreach ($this->tourSteps as $tourID => $tourSteps) {
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
}
