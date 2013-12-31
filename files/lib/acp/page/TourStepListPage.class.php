<?php
namespace wcf\acp\page;
use wcf\data\tour\TourList;
use wcf\page\SortablePage;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\exception\NamedUserException;
use wcf\system\WCF;

/**
 * Lists available tour steps.
 * 
 * @author	Magnus Kühn
 * @copyright	2013-2014 Thurnax.com
 * @package	com.thurnax.wcf.tour
 */
class TourStepListPage extends SortablePage {
	/**
	 * @see	\wcf\acp\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.user.tour.step.list';
	
	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.user.canManageTour');
	
	/**
	 * @see	\wcf\page\AbstractPage::$neededModules
	 */
	public $neededModules = array('MODULE_TOUR');
	
	/**
	 * @see	\wcf\page\MultipleLinkPage::$objectListClassName
	 */
	public $objectListClassName = 'wcf\data\tour\step\TourStepList';
	
	/**
	 * @see	\wcf\page\MultipleLinkPage::$defaultSortField
	 */
	public $defaultSortField = 'showOrder';
	
	/**
	 * @see	\wcf\page\MultipleLinkPage::$validSortFields
	 */
	public $validSortFields = array('tourStepID', 'showOrder', 'target', 'placement', 'title', 'content', 'xOffset', 'yOffset', 'showPrevButton', 'url');
	
	/**
	 * @see	\wcf\page\MultipleLinkPage::$itemsPerPage
	 */
	public $itemsPerPage = 100;
	
	/**
	 * selected tour id
	 * @var	integer
	 */
	public $tourID = null;
	
	/**
	 * list of all tours
	 * @var	array<\wcf\data\tour\Tour>
	 */
	public $tours = null;
	
	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		// read tours
		$tourList = new TourList();
		$tourList->sqlOrderBy = 'visibleName ASC';
		$tourList->readObjects();
		$this->tours = $tourList->getObjects();
		if (empty($this->tours)) {
			throw new NamedUserException(WCF::getLanguage()->getDynamicVariable('wcf.acp.tour.step.noTours'));
		}
		
		// validate tour id
		if (isset($_REQUEST['id']) && isset($this->tours[intval($_REQUEST['id'])])) {
			$this->tourID = intval($_REQUEST['id']);
		}
		
		parent::readData();
	}
	
	/**
	 * @see	\wcf\page\MultipleLinkPage::initObjectList()
	 */
	protected function initObjectList() {
		parent::initObjectList();
		
		if ($this->tourID) {
			$this->objectList->getConditionBuilder()->add('tourID = ?', array($this->tourID));
		}
	}
	
	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'tourID' => $this->tourID,
			'tours' => $this->tours,
			'hasMarkedItems' => ClipboardHandler::getInstance()->hasMarkedItems(ClipboardHandler::getInstance()->getObjectTypeID('com.thurnax.wcf.tour.step'))
		));
	}
}
