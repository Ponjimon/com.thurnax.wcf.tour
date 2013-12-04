<?php
namespace wcf\acp\form;
use wcf\data\tour\tourPoint\TourPointAction;
use wcf\data\tour\tourPoint\TourPointEditor;
use wcf\data\package\PackageCache;
use wcf\form\AbstractForm;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;

/**
 * Shows the tour point add form.
 * 
 * @author	Simon Nußbaumer
 * @copyright	2013 Thurnax.com
 * @package	com.thurnax.wcf.tour
 * @subpackage	acp.form
 * @category	Community Framework (commercial)
 */
class TourPointAddForm extends AbstractForm {
	/**
	 * @see	\wcf\page\AbstractPage::$templateName
	 */
	public $templateName = 'tourPointAdd';
	
	/**
	 * @see	\wcf\acp\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.user.tour';
	
	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.tour.canEdit');
	
	/**
	 * @see	\wcf\page\AbstractPage::$neededModules
	 */
	public $neededModules = array('MODULE_TOUR');
	
	/**
	 * step value
	 * @var integer
	 */
	public $step = 0;
	/**
	 * elementName value
	 * @var	string
	 */
	public $elementName = '';
	
	/**
	 * pointText value
	 * @var	string
	 */
	public $pointText = '';
	
	/**
	 * position value
	 * @var	string
	 */
	public $position =  '';
	
	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		I18nHandler::getInstance()->register('step');
		I18nHandler::getInstance()->register('elementName');
		I18nHandler::getInstance()->register('pointText');
		I18nHandler::getInstance()->register('position');
	}
	
	/**
	 * @see	\wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		I18nHandler::getInstance()->readValues();
		
		if (I18nHandler::getInstance()->isPlainValue('step')) $this->step = I18nHandler::getInstance()->getValue('step');
		if (I18nHandler::getInstance()->isPlainValue('elementName')) $this->elementName = I18nHandler::getInstance()->getValue('elementName');
		if (I18nHandler::getInstance()->isPlainValue('pointText')) $this->pointText = I18nHandler::getInstance()->getValue('pointText');
		if (I18nHandler::getInstance()->isPlainValue('position')) $this->position = I18nHandler::getInstance()->getValue('position');
		if (isset($_POST['step'])) $this->step = intval($_POST['step']);
		if (isset($_POST['elementName'])) $this->elementName = $_POST['elementName'];
		if (isset($_POST['pointText'])) $this->pointText = $_POST['pointText'];
		if (isset($_POST['position'])) $this->position = $_POST['position'];
	}
	
	/**
	 * @see	\wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		// validate elementName
		if (!I18nHandler::getInstance()->validateValue('elementName')) {
			throw new UserInputException('elementName');
		}
		// validate elementName
		if (!I18nHandler::getInstance()->validateValue('pointText')) {
			throw new UserInputException('pointText');
		}
		
		if ($this->step < 1) {
			throw new UserInputException('step', 'greaterThan');
		}
		if ($this->step > 8388607) {
			throw new UserInputException('step', 'lessThan');
		}
	}
	
	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();
		
		// save warning
		$this->objectAction = new TourPointAction(array(), 'create', array('data' => array(
			'step' => $this->step,
			'elementName' => $this->elementName,
			'pointText' => $this->pointText,
			'position' => $this->position
		)));
		$this->objectAction->executeAction();
		$returnValues = $this->objectAction->getReturnValues();
		$tourPointEditor = new TourPointEditor($returnValues['returnValues']);
		$tourPointID = $returnValues['returnValues']->tourPointID;
		
		$packageID = PackageCache::getInstance()->getPackageID('com.thurnax.wcf.tour');
		if (!I18nHandler::getInstance()->isPlainValue('elementName')) {
			I18nHandler::getInstance()->save('elementName', 'wcf.tour.tourPoint.elementName'.$tourPointID, 'wcf.tour.tourPoint', $packageID);
			
			// update title
			$tourPointEditor->update(array(
				'title' => 'wcf.tour.tourPoint.elementName'.$tourPointID
			));
		}
		if (!I18nHandler::getInstance()->isPlainValue('pointText')) {
			I18nHandler::getInstance()->save('pointText', 'wcf.tour.tourPoint.pointText'.$tourPointID, 'wcf.tour.tourPoint', $packageID);
			
			// update title
			$tourPointEditor->update(array(
				'pointText' => 'wcf.tour.tourPoint.pointText'.$tourPointID
			));
		}
		if (!I18nHandler::getInstance()->isPlainValue('position')) {
			I18nHandler::getInstance()->save('position', 'wcf.tour.tourPoint.position'.$tourPointID, 'wcf.tour.tourPoint', $packageID);
			
			// update title
			$tourPointEditor->update(array(
				'position' => 'wcf.tour.tourPoint.position'.$tourPointID
			));
		}
		
		$this->saved();
		
		// reset values
		$this->step = 0;
		$this->elementName = '';
		$this->pointText = '';
		$this->position = '';
		
		I18nHandler::getInstance()->reset();
		
		// show success
		WCF::getTPL()->assign(array(
			'success' => true
		));
	}
	
	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		I18nHandler::getInstance()->assignVariables();
		
		WCF::getTPL()->assign(array(
			'action' => 'add',
			'step' => $this->step,
			'elementName' => $this->elementName,
			'pointText' => $this->pointText,
			'position' => $this->position
		));
	}
}
