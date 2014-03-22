<?php
namespace wcf\acp\form;
use wcf\data\package\PackageCache;
use wcf\data\tour\Tour;
use wcf\data\tour\TourAction;
use wcf\data\tour\TourEditor;
use wcf\form\AbstractForm;
use wcf\system\acl\ACLHandler;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;
use wcf\util\ClassUtil;
use wcf\util\StringUtil;

/**
 * Shows the tour add form.
 *
 * @author	Magnus Kühn
 * @copyright	2013-2014 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
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
	public $neededPermissions = array('admin.user.canManageTour');
	
	/**
	 * @see	\wcf\page\AbstractPage::$neededModules
	 */
	public $neededModules = array('MODULE_TOUR');
	
	/**
	 * visibleName
	 * @var	string
	 */
	public $visibleName = '';
	
	/**
	 * trigger
	 * @var	string
	 */
	public $tourTrigger = 'firstSite';
	
	/**
	 * class name
	 * @var	string
	 */
	public $className = null;
	
	/**
	 * list of label group to object type relations
	 * @var	array<array>
	 */
	public $objectTypes = array();
	
	/**
	 * object type id
	 * @var	integer
	 */
	public $objectTypeID = 0;
	
	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// setup form
		I18nHandler::getInstance()->register('visibleName');
		$this->objectTypeID = ACLHandler::getInstance()->getObjectTypeID('com.thurnax.wcf.tour');
	}
	
	/**
	 * @see	\wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		I18nHandler::getInstance()->readValues();
		if (isset($_POST['visibleName'])) $this->visibleName = StringUtil::trim($_POST['visibleName']);
		if (isset($_POST['tourTrigger'])) $this->tourTrigger = $_POST['tourTrigger'];
		if (isset($_POST['className'])) $this->className = $_POST['className'];
		if (isset($_POST['objectTypes']) && is_array($_POST['objectTypes'])) $this->objectTypes = $_POST['objectTypes'];
	}
	
	/**
	 * @see	\wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		// validate visible name
		if (!I18nHandler::getInstance()->validateValue('visibleName')) {
			if (I18nHandler::getInstance()->isPlainValue('visibleName')) {
				throw new UserInputException('visibleName');
			} else {
				throw new UserInputException('visibleName', 'multilingual');
			}
		}
		
		// validate tour trigger
		switch ($this->tourTrigger) {
			case 'specificSite': // validate class name
				if (empty($this->className)) {
					throw new UserInputException('className');
				}
				if (!class_exists($this->className) || !ClassUtil::isInstanceOf($this->className, 'wcf\page\IPage')) {
					throw new UserInputException('className', 'invalid');
				}
			case 'firstSite': // no additional parameters
			case 'manual':
				break;
			default:
				throw new UserInputException('tourTrigger');
		}
	}
	
	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();
		$packageID = PackageCache::getInstance()->getPackageID('com.thurnax.wcf.tour');
		
		// save tour point
		$this->objectAction = new TourAction(array(), 'create', array('data' => array(
			'visibleName' => $this->visibleName,
			'isDisabled' => 0,
			'packageID' => $packageID, 
			'tourTrigger' => $this->tourTrigger,
			'className' => ($this->className ?: null)
		)));
		$this->objectAction->executeAction();
		$this->saved();
		
		// save ACL
		$returnValues = $this->objectAction->getReturnValues();
		$tourID = $returnValues['returnValues']->tourID;
		ACLHandler::getInstance()->save($tourID, $this->objectTypeID);
		ACLHandler::getInstance()->disableAssignVariables();
		
		if (!I18nHandler::getInstance()->isPlainValue('visibleName')) {
			$returnValues = $this->objectAction->getReturnValues();
			$tourID = $returnValues['returnValues']->tourID;
			I18nHandler::getInstance()->save('visibleName', 'wcf.acp.tour.visibleName'.$tourID, 'wcf.acp.tour', $packageID);
			
			// update tour description
			$tourEditor = new TourEditor($returnValues['returnValues']);
			$tourEditor->update(array(
				'visibleName' => 'wcf.acp.tour.visibleName'.$tourID
			));
		}
		
		// reset values
		$this->visibleName = $this->className = '';
		$this->tourTrigger = 'firstSite';
		I18nHandler::getInstance()->reset();
		
		// show success
		WCF::getTPL()->assign('success', true);
	}
	
	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		ACLHandler::getInstance()->assignVariables($this->objectTypeID);
		I18nHandler::getInstance()->assignVariables();
		WCF::getTPL()->assign(array(
			'action' => 'add',
			'visibleName' => $this->visibleName,
			'tourTrigger' => $this->tourTrigger,
			'className' => $this->className,
			'objectTypeID' => $this->objectTypeID
		));
	}
}
