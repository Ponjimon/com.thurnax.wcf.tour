<?php
namespace wcf\acp\action;
use wcf\action\AbstractAction;
use wcf\data\tour\step\TourStepList;
use wcf\data\tour\Tour;
use wcf\system\exception\IllegalLinkException;
use wcf\util\FileUtil;
use wcf\util\I18nXMLWriter;

/**
 * Exports a tour.
 * 
 * @author	Magnus Kühn
 * @copyright	2013-2014 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class TourExportAction extends AbstractAction {
	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.user.canManageTour');
	
	/**
	 * @see	\wcf\page\AbstractPage::$neededModules
	 */
	public $neededModules = array('MODULE_TOUR');
	
	/**
	 * tour to export
	 * @var	\wcf\data\tour\Tour
	 */
	public $tour = null;
	
	/**
	 * @see	\wcf\action\IAction::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// read tour
		if (isset($_REQUEST['id'])) {
			$this->tour = new Tour($_REQUEST['id']);
		}
		if (!$this->tour || !$this->tour->tourID) {
			throw new IllegalLinkException();
		}
	}
	
	public function execute() {
		parent::execute(); 
		
		// create tour xml
		$xml = new I18nXMLWriter();
		$xml->beginDocument('data', 'https://www.thurnax.com', 'https://www.thurnax.com/XSD/tour.xsd'); // @todo fix schema location
		$xml->startElement('import');
		$this->writeTour($this->tour, $xml);
		$xml->endElement();
		
		// write file
		$tourFileName = FileUtil::getTemporaryFilename('tour_', '.xml');
		$xml->endDocument($tourFileName);
		
		// send file
		header('Content-Disposition: attachment; filename="tour-'.$this->tour->getTitle().'.xml"');
		readfile($tourFileName);
		$this->executed();
		exit;
	}
	
	/**
	 * Writes a tour to a XMLWriter
	 * 
	 * @param	\wcf\data\tour\Tour	$tour
	 * @param	\wcf\util\I18nXMLWriter	$xml
	 */
	protected function writeTour(Tour $tour, I18nXMLWriter $xml) {
		$xml->startElement('tour', array('className' => $tour->className)); // @todo this is not ideal
		$xml->writeElement('isDisabled', $tour->isDisabled);
		$xml->writeI18nElement('visibleName', $tour->visibleName);
		$xml->writeElement('tourTrigger', $tour->tourTrigger);
		
		// get steps
		$tourStepList = new TourStepList();
		$tourStepList->getConditionBuilder()->add('tourID = ?', array($tour->tourID));
		$tourStepList->readObjects();
		
		// write steps
		$xml->startElement('steps');
		/** @var $tourStep \wcf\data\tour\step\TourStep */
		foreach ($tourStepList->getObjects() as $tourStep) {
			$xml->startElement('step');
			$xml->writeElement('showOrder', $tourStep->showOrder);
			$xml->writeElement('target', $tourStep->target);
			$xml->writeElement('placement', $tourStep->placement);
			$xml->writeI18nElement('content', $tourStep->content);
			
			// optionals
			$xml->writeI18nElement('title', $tourStep->title);
			$xml->writeElement('showPrevButton', $tourStep->showPrevButton);
			$xml->writeElement('xOffset', $tourStep->xOffset);
			$xml->writeElement('yOffset', $tourStep->yOffset);
			$xml->writeElement('url', $tourStep->url);
			$xml->writeI18nElement('ctaLabel', $tourStep->ctaLabel);
			
			// callbacks
			$xml->writeElement('onPrev', $tourStep->onPrev);
			$xml->writeElement('onNext', $tourStep->onNext);
			$xml->writeElement('onShow', $tourStep->onShow);
			$xml->writeElement('onCTA', $tourStep->onCTA);
			$xml->endElement();
		}
		$xml->endElement();
	}
}
