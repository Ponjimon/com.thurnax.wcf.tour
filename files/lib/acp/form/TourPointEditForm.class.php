<?php
namespace wcf\acp\form;
use wcf\data\tour\step\TourStep;
use wcf\data\tour\step\TourStepAction;
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
	 * point id
	 * @var	integer
	 */
	public $tourPointID = 0;
	
	/**
	 * point object
	 * @var	\wcf\data\tour\step\TourStep
	 */
	public $point = null;
	
	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (isset($_REQUEST['id'])) $this->tourPointID = intval($_REQUEST['id']);
		$this->point = new TourStep($this->tourPointID);
		if (!$this->point->tourPointID) {
			throw new IllegalLinkException();
		}
	}
	
	/**
	 * @see	\wcf\acp\form\TourPointAddForm::validateStep()
	 */
	protected function validateStep() {
		if ($this->step != $this->point->step) { // only validate if the value has changed
			parent::validateStep();
		}
	}
	
	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		AbstractForm::save();
		
		$this->pointText = 'wcf.acp.tour.point.pointText'.$this->point->tourPointID;
		if (I18nHandler::getInstance()->isPlainValue('pointText')) {
			I18nHandler::getInstance()->remove($this->pointText);
			$this->pointText = I18nHandler::getInstance()->getValue('pointText');
		} else {
			I18nHandler::getInstance()->save('pointText', $this->pointText, 'wcf.acp.tour', $this->point->tourPointID);
		}
		
		// update tour point
		$this->objectAction = new TourStepAction(array($this->tourPointID), 'update', array('data' => array(
			'step' => $this->step,
			'elementName' => $this->elementName,
			'pointText' => $this->pointText,
			'position' => $this->position
		)));
		$this->objectAction->executeAction();
		$this->saved();
		
		// show success
		WCF::getTPL()->assign('success', true);
	}
	
	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		
		if (empty($_POST)) {
			I18nHandler::getInstance()->setOptions('pointText', $this->point->tourPointID, $this->point->pointText, 'wcf.acp.tour.point.pointText\d+');			
			
			$this->tourID = 0; //to be changed in future!
			$this->step = $this->point->step;
			$this->elementName = $this->point->elementName;
			$this->pointText = $this->point->pointText;
			$this->position = $this->point->position;
		}
	}
	
	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		I18nHandler::getInstance()->assignVariables(!empty($_POST));
		WCF::getTPL()->assign(array(
			'tourPointID' => $this->tourPointID,
			'point' => $this->point,
			'action' => 'edit'
		));
	}
}
