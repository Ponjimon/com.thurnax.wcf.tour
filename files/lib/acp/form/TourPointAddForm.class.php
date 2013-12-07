<?php
namespace wcf\acp\form;
use wcf\data\package\PackageCache;
use wcf\data\tour\point\TourPoint;
use wcf\data\tour\point\TourPointAction;
use wcf\data\tour\point\TourPointEditor;
use wcf\form\AbstractForm;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

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
	 * @see	\wcf\acp\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.user.tour.point.add';
	
	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.user.canEditTour');
	
	/**
	 * @see	\wcf\page\AbstractPage::$neededModules
	 */
	public $neededModules = array('MODULE_TOUR');
	
	/**
	 * step
	 * @var integer
	 */
	public $step = 1;
	
	/**
	 * element name
	 * @var	string
	 */
	public $elementName = '';
	
	/**
	 * position
	 * @var	string
	 */
	public $position =  'left';
	
	/**
	 * valid positions
	 * @var	string
	 */
	public $validPositions = array('top', 'bottom', 'left', 'right');
	
	/**
	 * point text
	 * @var	string
	 */
	public $pointText = '';
	
	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		I18nHandler::getInstance()->register('pointText');
	}
	
	/**
	 * @see	\wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		I18nHandler::getInstance()->readValues();
		if (isset($_POST['step'])) $this->step = intval($_POST['step']);
		if (isset($_POST['elementName'])) $this->elementName = $_POST['elementName'];
		if (isset($_POST['position'])) $this->position = $_POST['position'];
		if (isset($_POST['pointText'])) $this->pointText = StringUtil::trim($_POST['pointText']);
	}
	
	/**
	 * @see	\wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		$this->validateStep();
		
		// validate point text
		if (!I18nHandler::getInstance()->validateValue('pointText')) {
			if (I18nHandler::getInstance()->isPlainValue('pointText')) {
				throw new UserInputException('pointText');
			} else {
				throw new UserInputException('pointText', 'multilingual');
			}
		}
		
		// validate position
		if (empty($this->position) || !in_array($this->position, $this->validPositions)) {
			throw new UserInputException('position');
		}
		
		// validate element name
		if (empty($this->elementName)) {
			throw new UserInputException('elementName');
		}
	}
	
	/**
	 * Validates the step
	 */
	protected function validateStep() {
		if (empty($this->step)) {
			throw new UserInputException('step');
		} else if ($this->step < 1) {
			throw new UserInputException('step', 'greaterThan');
		} else if ($this->step > 8388607) {
			throw new UserInputException('step', 'lessThan');
		}
		
		// check for collusion
		$sql = "SELECT	*
			FROM	".TourPoint::getDatabaseTableName()."
			WHERE	step = ?";
		$statement = WCF::getDB()->prepareStatement($sql, 1);
		$statement->execute(array($this->step));
		
		if ($statement->fetchArray()) {
			throw new UserInputException('step', 'notUnique');
		}
	}
	
	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();
		
		// save tour point
		$this->objectAction = new TourPointAction(array(), 'create', array('data' => array(
			'step' => $this->step,
			'elementName' => $this->elementName,
			'pointText' => $this->pointText,
			'position' => $this->position
		)));
		$this->objectAction->executeAction();
		$this->saved();

		if (!I18nHandler::getInstance()->isPlainValue('pointText')) {
			$returnValues = $this->objectAction->getReturnValues();
			$tourPointID = $returnValues['returnValues']->tourPointID;
			I18nHandler::getInstance()->save('pointText', 'wcf.acp.tour.point.pointText'.$tourPointID, 'wcf.acp.tour', PackageCache::getInstance()->getPackageID('com.thurnax.wcf.tour'));

			// update tracking goal description
			$pointEditor = new TourPointEditor($returnValues['returnValues']);
			$pointEditor->update(array(
				'pointText' => 'wcf.acp.tour.point.pointText'.$tourPointID
			));
		}
		
		// reset values
		$this->step = 1;
		$this->elementName = $this->pointText = '';
		$this->position = 'left';
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
			'step' => $this->step,
			'elementName' => $this->elementName,
			'pointText' => $this->pointText,
			'position' => $this->position,
			'validPositions' => $this->validPositions
		));
	}
}
