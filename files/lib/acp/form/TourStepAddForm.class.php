<?php
namespace wcf\acp\form;
use wcf\data\package\PackageCache;
use wcf\data\tour\step\TourStepAction;
use wcf\data\tour\step\TourStepEditor;
use wcf\data\tour\TourList;
use wcf\form\AbstractForm;
use wcf\system\cache\builder\TourStepCacheBuilder;
use wcf\system\exception\NamedUserException;
use wcf\system\exception\UserInputException;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the tour step add form.
 * 
 * @author	Magnus Kühn
 * @copyright	2013 Thurnax.com
 * @package	com.thurnax.wcf.tour
 */
class TourStepAddForm extends AbstractForm {
	/**
	 * @see	\wcf\acp\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.user.tour.step.add';
	
	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.user.canEditTour');
	
	/**
	 * @see	\wcf\page\AbstractPage::$neededModules
	 */
	public $neededModules = array('MODULE_TOUR');
	
	/**
	 * tour id
	 * @var	integer
	 */
	public $tourID = null;
	
	/**
	 * list of all tours
	 * @var	array<\wcf\data\tour\Tour>
	 */
	public $tours = null;
	
	/**
	 * target
	 * @var	string
	 */
	public $target = '';
	
	/**
	 * placement
	 * @var	string
	 */
	public $placement =  'left';
	
	/**
	 * valid placements
	 * @var	string
	 */
	public $validPlacements = array('top', 'bottom', 'left', 'right');
	
	/**
	 * title
	 * @var	string
	 */
	public $title = '';
	
	/**
	 * content
	 * @var	string
	 */
	public $stepContent = '';
	
	/**
	 * x offset
	 * @var	integer
	 */
	public $xOffset = 0;
	
	/**
	 * y offset
	 * @var	integer
	 */
	public $yOffset = 0;
	
	/**
	 * url to redirect to
	 * @var	string
	 */
	public $url = null;
	
	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// read id => tour id
		if (isset($_REQUEST['id'])) {
			$this->tourID = intval($_REQUEST['id']);
		}
		
		// register I18n-items
		I18nHandler::getInstance()->register('title');
		I18nHandler::getInstance()->register('stepContent');
	}
	
	/**
	 * @see	\wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		I18nHandler::getInstance()->readValues();
		if (isset($_POST['tourID'])) $this->tourID = intval($_POST['tourID']);
		if (isset($_POST['target'])) $this->target = $_POST['target'];
		if (isset($_POST['placement'])) $this->placement = $_POST['placement'];
		if (isset($_POST['title'])) $this->title = StringUtil::trim($_POST['title']);
		if (isset($_POST['stepContent'])) $this->stepContent = StringUtil::trim($_POST['stepContent']);
		if (isset($_POST['xOffset'])) $this->xOffset = intval($_POST['xOffset']);
		if (isset($_POST['yOffset'])) $this->xOffset = intval($_POST['yOffset']);
		if (isset($_POST['url'])) $this->url = $_POST['url'];
	}
	
	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		
		// read available tours
		$tourList = new TourList();
		$tourList->sqlOrderBy = 'tourName';
		$tourList->readObjects();
		$this->tours = $tourList->getObjects();
		
		if (empty($this->tours)) {
			throw new NamedUserException(WCF::getLanguage()->getDynamicVariable('wcf.acp.tour.step.noTours'));
		}
	}
	
	/**
	 * @see	\wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		// validate target
		if (empty($this->target)) {
			throw new UserInputException('target');
		}
		
		// validate placement
		if (empty($this->placement) || !in_array($this->placement, $this->validPlacements)) {
			throw new UserInputException('placement');
		}
		
		// validate title
		if (!I18nHandler::getInstance()->validateValue('title')) { // optional
			if (!I18nHandler::getInstance()->isPlainValue('title')) {
				throw new UserInputException('title', 'multilingual');
			}
		}
		
		// validate content
		if (!I18nHandler::getInstance()->validateValue('stepContent')) {
			if (I18nHandler::getInstance()->isPlainValue('stepContent')) {
				throw new UserInputException('stepContent');
			} else {
				throw new UserInputException('stepContent', 'multilingual');
			}
		}
	}
	
	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();
		
		// save tour point
		$this->objectAction = new TourStepAction(array(), 'create', array('data' => array(
			'tourID' => $this->tourID,
			'showOrder' => $this->getShowOrder(),
			'target' => $this->target,
			'placement' => $this->placement,
			'title' => $this->title,
			'content' => $this->stepContent,
			'xOffset' => $this->xOffset,
			'yOffset' => $this->yOffset,
			'url' => $this->url
		)));
		$this->objectAction->executeAction();
		$this->saved();
		
		// save I18n-values
		$packageID = PackageCache::getInstance()->getPackageID('com.thurnax.wcf.tour');
		$returnValues = $this->objectAction->getReturnValues();
		$tourPointID = $returnValues['returnValues']->tourPointID;
		$updateData = array();
		
		if (!I18nHandler::getInstance()->isPlainValue('title')) {
			I18nHandler::getInstance()->save('title', 'wcf.acp.tour.step.title'.$tourPointID, 'wcf.acp.tour', $packageID);
			$updateData['title'] = 'wcf.acp.tour.step.title'.$tourPointID;
		}
		
		if (!I18nHandler::getInstance()->isPlainValue('stepContent')) {
			I18nHandler::getInstance()->save('stepContent', 'wcf.acp.tour.step.content'.$tourPointID, 'wcf.acp.tour', $packageID);
			$updateData['content'] = 'wcf.acp.tour.step.content'.$tourPointID;
		}
		
		// update tour step
		if ($updateData) {
			$tourStepEditor = new TourStepEditor($returnValues['returnValues']);
			$tourStepEditor->update($updateData);
		}
		
		// reset values
		$this->target = $this->stepContent = '';
		$this->placement = 'left';
		I18nHandler::getInstance()->reset();
		
		// show success
		WCF::getTPL()->assign('success', true);
	}

	/**
	 * Fetches the next show order
	 * 
	 * @return	integer
	 */
	protected function getShowOrder() {
		$tourSteps = TourStepCacheBuilder::getInstance()->getData(array('tourID' => $this->tourID));
		if (count($tourSteps)) {
			$tourStep = array_pop($tourSteps);
			return $tourStep->showOrder + 1;
		}
		
		return 1;
	}
	
	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		I18nHandler::getInstance()->assignVariables();
		WCF::getTPL()->assign(array(
			'action' => 'add',
			'tourID' => $this->tourID,
			'tours' => $this->tours,
			'target' => $this->target,
			'content' => $this->stepContent,
			'placement' => $this->placement,
			'validPlacements' => $this->validPlacements,
			'xOffset' => $this->xOffset,
			'yOffset' => $this->yOffset,
			'url' => $this->url
		));
	}
}
