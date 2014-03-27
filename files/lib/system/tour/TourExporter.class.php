<?php
namespace wcf\system\tour;
use wcf\data\tour\step\TourStepList;
use wcf\data\tour\Tour;
use wcf\system\exception\SystemException;
use wcf\system\WCF;
use wcf\util\DateUtil;
use wcf\util\FileUtil;
use wcf\util\I18nXMLWriter;

/**
 * Creates tour xml files.
 * 
 * @author	Magnus Kühn
 * @copyright	2013-2014 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class TourExporter {
	/**
	 * xml writer
	 * @var	\wcf\util\I18nXMLWriter
	 */
	protected $xml = null;
	
	/**
	 * Creates a new instance of TourExporter
	 */
	public function __construct() {
		$this->xml = new I18nXMLWriter();
		$this->xml->beginDocument('data', 'https://www.thurnax.com', 'https://www.thurnax.com/XSD/tour.xsd'); // @todo fix schema location
		$this->xml->startElement('import');
	}
	
	/**
	 * Writes a tour to a XMLWriter
	 * 
	 * @param	\wcf\data\tour\Tour	$tour
	 * @return	boolean
	 */
	public function writeTour(Tour $tour) {
		if (!$tour->identifier) {
			return false;
		}
		
		// write tour data
		$this->xml->startElement('tour', array('identifier' => $tour->identifier));
		$this->xml->writeElement('isDisabled', $tour->isDisabled, array(), true);
		$this->xml->writeI18nElement('visibleName', $tour->visibleName);
		$this->xml->writeElement('tourTrigger', $tour->tourTrigger);
		$this->xml->writeElement('className', $tour->className);
		
		// get steps
		$tourStepList = new TourStepList();
		$tourStepList->getConditionBuilder()->add('tourID = ?', array($tour->tourID));
		$tourStepList->readObjects();
		
		// write steps
		$this->xml->startElement('steps');
		/** @var $tourStep \wcf\data\tour\step\TourStep */
		foreach ($tourStepList->getObjects() as $tourStep) {
			$this->xml->startElement('step');
			$this->xml->writeElement('showOrder', $tourStep->showOrder);
			$this->xml->writeElement('target', $tourStep->target);
			$this->xml->writeElement('placement', $tourStep->placement);
			$this->xml->writeI18nElement('content', $tourStep->content, array(), true);
			
			// optionals
			$this->xml->writeI18nElement('title', $tourStep->title);
			$this->xml->writeElement('showPrevButton', $tourStep->showPrevButton);
			$this->xml->writeElement('xOffset', $tourStep->xOffset);
			$this->xml->writeElement('yOffset', $tourStep->yOffset);
			$this->xml->writeElement('url', $tourStep->url);
			$this->xml->writeI18nElement('ctaLabel', $tourStep->ctaLabel);
			
			// callbacks
			$this->xml->writeElement('onPrev', $tourStep->onPrev);
			$this->xml->writeElement('onNext', $tourStep->onNext);
			$this->xml->writeElement('onShow', $tourStep->onShow);
			$this->xml->writeElement('onCTA', $tourStep->onCTA);
			$this->xml->endElement();
		}
		
		$this->xml->endElement(); // steps
		$this->xml->endElement(); // tour
		return true;
	}
	
	/**
	 * Saves the tour xml.
	 * 
	 * @param	string	$tourFileName
	 */
	public function save($tourFileName) {
		$this->xml->endElement();
		$this->xml->endDocument($tourFileName);
	}
	
	/**
	 * Sends the tour xml to the client.
	 * 
	 * @param	string	$filename
	 */
	public function send($filename) {
		// write file
		$tourFilename = FileUtil::getTemporaryFilename('tour_', '.xml');
		$this->save($tourFilename);
		
		// format current time
		$dateTimeObject = DateUtil::getDateTimeByTimestamp(TIME_NOW);
		$date = DateUtil::format($dateTimeObject, DateUtil::DATE_FORMAT);
		$time = DateUtil::format($dateTimeObject, DateUtil::TIME_FORMAT);
		
		// send file
		header('Content-Disposition: attachment; filename="'.$filename.' - '.$date.' - '.$time.'.xml"');
		readfile($tourFilename);
	}
}
