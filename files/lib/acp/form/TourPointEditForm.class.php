<?php
namespace wcf\acp\form;
use wcf\data\tour\point\TourPoint;
use wcf\data\tour\point\TourPointAction;
use wcf\data\package\PackageCache;
use wcf\form\AbstractForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;

/**
 * Shows the tour point edit form.
 * 
 * @author	Simon Nußbaumer
 * @copyright	2013 Thurnax.com
 * @package	com.thurnax.wcf.tour
 * @subpackage	acp.form
 * @category	Community Framework (commercial)
 */
class TourPointEditForm extends TourPointAddForm {
	/**
	 * @see	\wcf\acp\form\ACPForm::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.user.tour';
	
	/**
	 * tourPoint id
	 * @var	integer
	 */
	public $tourPointID = 0;
	
	/**
	 * tourPoint object
	 * 
*@var	\wcf\data\tour\point\TourPoint
	 */
	public $tourPointObj = null;
	
	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['id'])) $this->tourPointID = intval($_REQUEST['id']);
		$this->tourPointObj = new TourPoint($this->tourPointID);
		if (!$this->tourPointObj->tourPointID) {
			throw new IllegalLinkException();
		}
	}
	
	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		AbstractForm::save();
		
		$packageID = PackageCache::getInstance()->getPackageID('com.thurnax.wcf.tour');
		$this->step = 'wcf.tour.tourPoint.step'.$this->tourPointObj->tourPointID;
		$this->elementName = 'wcf.tour.tourPoint.elementName'.$this->tourPointObj->tourPointID;
		$this->pointText = 'wcf.tour.tourPoint.pointText'.$this->tourPointObj->tourPointID;
		$this->position = 'wcf.tour.tourPoint.position'.$this->tourPointObj->tourPointID;
		if (I18nHandler::getInstance()->isPlainValue('step')) {
			I18nHandler::getInstance()->remove($this->step, $packageID);
			$this->step = I18nHandler::getInstance()->getValue('step');
		}
		else {
			I18nHandler::getInstance()->save('elementName', $this->elementName, 'wcf.tour.tourPoint', $packageID);
		}
		if (I18nHandler::getInstance()->isPlainValue('elementName')) {
			I18nHandler::getInstance()->remove($this->elementName, $packageID);
			$this->elementName = I18nHandler::getInstance()->getValue('elementName');
		}
		else {
			I18nHandler::getInstance()->save('elementName', $this->elementName, 'wcf.tour.tourPoint', $packageID);
		}
		if (I18nHandler::getInstance()->isPlainValue('pointText')) {
			I18nHandler::getInstance()->remove($this->pointText, $packageID);
			$this->pointText = I18nHandler::getInstance()->getValue('pointText');
		}
		else {
			I18nHandler::getInstance()->save('pointText', $this->pointText, 'wcf.tour.tourPoint', $packageID);
		}
		if (I18nHandler::getInstance()->isPlainValue('position')) {
			I18nHandler::getInstance()->remove($this->position, $packageID);
			$this->position = I18nHandler::getInstance()->getValue('position');
		}
		else {
			I18nHandler::getInstance()->save('position', $this->position, 'wcf.tour.tourPoint', $packageID);
		}
		
		// update warning
		$this->objectAction = new TourPointAction(array($this->tourPointID), 'update', array('data' => array(
			'step' => $this->step,
			'elementName' => $this->elementName,
			'pointText' => $this->pointText,
			'position' => $this->position
		)));
		$this->objectAction->executeAction();
		
		$this->saved();
		
		// show success
		WCF::getTPL()->assign(array(
			'success' => true
		));
	}
	
	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		
		if (empty($_POST)) {
			I18nHandler::getInstance()->setOptions('step', PackageCache::getInstance()->getPackageID('com.thurnax.wcf.step'), $this->tourPointObj->step, 'wcf.tour.tourPoint.step\d+');
			I18nHandler::getInstance()->setOptions('elementName', PackageCache::getInstance()->getPackageID('com.thurnax.wcf.tour'), $this->tourPointObj->elementName, 'wcf.tour.tourPoint.elementName\d+');
			I18nHandler::getInstance()->setOptions('pointText', PackageCache::getInstance()->getPackageID('com.thurnax.wcf.tour'), $this->tourPointObj->pointText, 'wcf.tour.tourPoint.pointText\d+');			
			I18nHandler::getInstance()->setOptions('position', PackageCache::getInstance()->getPackageID('com.thurnax.wcf.tour'), $this->tourPointObj->position, 'wcf.tour.tourPoint.position\d+');
			$this->step = $this->tourPointObj->step;
			$this->elementName = $this->tourPointObj->elementName;
			$this->pointText = $this->tourPointObj->pointText;
			$this->position = $this->tourPointObj->position;
		}
	}
	
	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		I18nHandler::getInstance()->assignVariables((bool) count($_POST));
		
		WCF::getTPL()->assign(array(
			'tourPointID' => $this->tourPointID,
			'action' => 'edit'
		));
	}
}
