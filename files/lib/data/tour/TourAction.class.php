<?php
namespace wcf\data\tour;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\IToggleAction;
use wcf\data\tour\step\TourStep;
use wcf\data\tour\step\TourStepList;
use wcf\system\cache\builder\TourTriggerCacheBuilder;
use wcf\system\cache\builder\TourCacheBuilder;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\request\LinkHandler;
use wcf\system\tour\TourHandler;
use wcf\system\WCF;

/**
 * Executes tour-related actions.
 *
 * @author	Magnus Kühn
 * @copyright	2013-2014 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class TourAction extends AbstractDatabaseObjectAction implements IToggleAction {
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$className
	 */
	protected $className = 'wcf\data\tour\TourEditor';
	
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsUpdate
	 */
	protected $permissionsUpdate = array('admin.user.canManageTour');
	
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsDelete
	 */
	protected $permissionsDelete = array('admin.user.canManageTour');
	
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$requireACP
	 */
	protected $requireACP = array('update', 'delete', 'move', 'restartTour');
	
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$resetCache
	 */
	protected $resetCache = array('create', 'delete', 'toggle', 'update', 'updatePosition', 'move');
	
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$allowGuestAccess
	 */
	protected $allowGuestAccess = array('loadTour', 'endTour');
	
	/**
	 * @see	\wcf\data\IToggleAction::toggle()
	 */
	public function toggle() {
		/** @var $tour \wcf\data\tour\TourEditor */
		foreach ($this->objects as $tour) {
			$tour->update(array('isDisabled' => $tour->isDisabled ? 0 : 1));
		}
	}
	
	/**
	 * @see	\wcf\data\IToggleAction::validateToggle()
	 */
	public function validateToggle() {
		parent::validateUpdate();
	}
	
	/**
	 * Loads the steps for a tour
	 * 
	 * @return        array<mixed>
	 */
	public function loadTour() {
		/** @var $tour \wcf\data\tour\TourEditor */
		$tour = $this->getSingleObject();
		TourHandler::getInstance()->startTour($tour->tourID, true);
		TourHandler::getInstance()->takeTour($tour->tourID);
		
		// get tour steps
		$tourSteps = TourCacheBuilder::getInstance()->getData(array(), 'steps');
		return (isset($tourSteps[$tour->tourID]) ? $tourSteps[$tour->tourID] : null);
	}
	
	/**
	 * Validates the 'loadTour'-action
	 */
	public function validateLoadTour() {
		if (!TourHandler::getInstance()->isEnabled()) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * Marks a tour as ended
	 */
	public function endTour() {
		TourHandler::getInstance()->endTour();
	}
	
	/**
	 * Validates the 'endTour'-action
	 */
	public function validateEndTour() {
		if (!TourHandler::getInstance()->isEnabled()) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * Moves tour steps to another tour
	 * 
	 * @return	string
	 */
	public function move() {
		/** @var $targetTour \wcf\data\tour\TourEditor */
		$targetTour = $this->getSingleObject();
		$objectTypeID = ClipboardHandler::getInstance()->getObjectTypeID('com.thurnax.wcf.tour.step');
		
		// select last show order of target tour
		$sql = "SELECT	showOrder
			FROM	".TourStep::getDatabaseTableName()."
			WHERE	tourID = ?
			ORDER BY showOrder DESC";
		$statement = WCF::getDB()->prepareStatement($sql, 1);
		$statement->execute(array($targetTour->tourID));
		$row = $statement->fetchArray();
		if (!$row) {
			$row['showOrder'] = 0;
		}
		
		// read tour step IDs
		$tourStepIDs = array();
		/** @var $tourStep \wcf\data\tour\step\TourStep */
		foreach (ClipboardHandler::getInstance()->getMarkedItems($objectTypeID) as $tourStep) {
			$tourStepIDs[] = $tourStep->tourStepID;
 		}
		
		// read tour steps ordered by the show order
		$tourStepList = new TourStepList();
		$tourStepList->setObjectIDs($tourStepIDs);
		$tourStepList->sqlOrderBy = 'showOrder ASC';
		$tourStepList->readObjects();
		
		// update tour steps (appends the items in the old show order)
		$sql = "UPDATE	".TourStep::getDatabaseTableName()."
			SET	tourID = ?, showOrder = ?
			WHERE	tourStepID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$showOrderOffset = 1;
		/** @var $tourStep \wcf\data\tour\TourStep */
		foreach ($tourStepList->getObjects() as $tourStep) {
			$statement->execute(array($targetTour->tourID, $row['showOrder'] + $showOrderOffset, $tourStep->tourStepID));
			$showOrderOffset++;
		}
		
		// clear clipboard
		ClipboardHandler::getInstance()->unmarkAll($objectTypeID);
		return LinkHandler::getInstance()->getLink('TourStepList', array('object' => $targetTour));
	}
	
	/**
	 * Validates the 'move'-action
	 */
	public function validateMove() {
		WCF::getSession()->checkPermissions($this->permissionsUpdate);
	}
	
	/**
	 * Restarts a tour
	 */
	public function restartTour() {
		$sql = "DELETE FROM ".Tour::getDatabaseTableName()."_user WHERE tourID = ? AND userID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		foreach ($this->objects as $tourEditor) {
			$statement->execute(array($tourEditor->tourID, WCF::getUser()->userID));
		}
		
		TourHandler::reset();
	}
	
	/**
	 * Validates the 'restartTour'-action
	 */
	public function validateRestartTour() {
		$this->validateUpdate();
	}
}
