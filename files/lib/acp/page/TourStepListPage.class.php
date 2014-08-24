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
 * @author    Magnus Kühn
 * @copyright 2013-2014 Thurnax.com
 * @package   com.thurnax.wcf.tour
 */
class TourStepListPage extends SortablePage {
	/**
	 * name of the active menu item
	 *
	 * @var string
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.user.tour.step.list';

	/**
	 * needed permissions to view this page
	 *
	 * @var string[]
	 */
	public $neededPermissions = array('admin.user.canManageTour');

	/**
	 * needed modules to view this page
	 *
	 * @var string[]
	 */
	public $neededModules = array('MODULE_TOUR');

	/**
	 * class name for DatabaseObjectList
	 *
	 * @var string
	 */
	public $objectListClassName = 'wcf\data\tour\step\TourStepList';

	/**
	 * default sort field
	 *
	 * @var string
	 */
	public $defaultSortField = 'showOrder';

	/**
	 * list of valid sort fields
	 *
	 * @var string
	 */
	public $validSortFields = array('tourStepID',
		'showOrder',
		'target',
		'placement',
		'title',
		'content',
		'xOffset',
		'yOffset',
		'showPrevButton',
		'url');

	/**
	 * selected tour id
	 *
	 * @var int
	 */
	public $tourID = null;

	/**
	 * list of all tours
	 *
	 * @var \wcf\data\tour\Tour[]
	 */
	public $tours = null;

	/**
	 * Reads/Gets the data to be displayed on this page.
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
	 * Initializes DatabaseObjectList instance.
	 */
	protected function initObjectList() {
		parent::initObjectList();

		if ($this->tourID) {
			$this->objectList->getConditionBuilder()->add('tourID = ?', array($this->tourID));
		}
	}

	/**
	 * Assigns variables to the template engine.
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array('tourID' => $this->tourID,
			'tours' => $this->tours,
			'hasMarkedItems' => ClipboardHandler::getInstance()->hasMarkedItems(ClipboardHandler::getInstance()->getObjectTypeID('com.thurnax.wcf.tour.step'))));
	}
}
