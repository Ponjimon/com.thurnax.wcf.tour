<?php
namespace wcf\acp\form;
use wcf\data\package\PackageCache;
use wcf\data\tour\Tour;
use wcf\data\tour\TourAction;
use wcf\data\tour\TourEditor;
use wcf\form\AbstractForm;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the tour add form.
 *
 * @author	Magnus Kühn
 * @copyright	2013 Thurnax.com
 * @package	com.thurnax.wcf.tour
 */
class TourAddForm extends AbstractForm {
	/**
	 * @see	\wcf\acp\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.user.tour.add';
	
	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.user.canEditTour');
	
	/**
	 * @see	\wcf\page\AbstractPage::$neededModules
	 */
	public $neededModules = array('MODULE_TOUR');
	
	/**
	 * tour name
	 * @var	string
	 */
	public $tourName = '';
	
	/**
	 * visibleName
	 * @var	string
	 */
	public $visibleName = '';
	
	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		I18nHandler::getInstance()->register('visibleName');
	}
	
	/**
	 * @see	\wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		I18nHandler::getInstance()->readValues();
		if (isset($_POST['tourName'])) $this->tourName = StringUtil::trim($_POST['tourName']);
		if (isset($_POST['visibleName'])) $this->visibleName = StringUtil::trim($_POST['visibleName']);
	}
	
	/**
	 * @see	\wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		$this->validateTourName();
		
		// validate visible name
		if (!I18nHandler::getInstance()->validateValue('visibleName')) {
			if (I18nHandler::getInstance()->isPlainValue('visibleName')) {
				throw new UserInputException('visibleName');
			} else {
				throw new UserInputException('visibleName', 'multilingual');
			}
		}
	}
	
	/**
	 * Validates the tour name
	 */
	protected function validateTourName() {
		if (empty($this->tourName)) {
			throw new UserInputException('tourName');
		}
		
		// check for collusion
		if (Tour::getByName($this->tourName)) {
			throw new UserInputException('tourName', 'notUnique');
		}
	}
	
	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();
		
		// save tour point
		$this->objectAction = new TourAction(array(), 'create', array('data' => array(
			'tourName' => $this->tourName,
			'visibleName' => $this->visibleName,
			'objectTypeID' => 1
		)));
		$this->objectAction->executeAction();
		$this->saved();
		
		if (!I18nHandler::getInstance()->isPlainValue('visibleName')) {
			$returnValues = $this->objectAction->getReturnValues();
			$tourID = $returnValues['returnValues']->tourID;
			I18nHandler::getInstance()->save('visibleName', 'wcf.acp.tour.visibleName'.$tourID, 'wcf.acp.tour', PackageCache::getInstance()->getPackageID('com.thurnax.wcf.tour'));
			
			// update tour description
			$tourEditor = new TourEditor($returnValues['returnValues']);
			$tourEditor->update(array(
				'visibleName' => 'wcf.acp.tour.visibleName'.$tourID
			));
		}
		
		// reset values
		$this->tourName = $this->visibleName = '';
		I18nHandler::getInstance()->reset();
		
		// show success
		WCF::getTPL()->assign('success', true);
	}
	
	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		I18nHandler::getInstance()->assignVariables();
		WCF::getTPL()->assign(array(
			'action' => 'add',
			'tourName' => $this->tourName,
			'visibleName' => $this->visibleName
		));
	}
}
