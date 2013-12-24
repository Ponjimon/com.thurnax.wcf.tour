<?php
namespace wcf\data\tour;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\IToggleAction;
use wcf\data\tour\step\TourStep;
use wcf\system\cache\builder\TourTriggerCacheBuilder;
use wcf\system\cache\builder\TourStepCacheBuilder;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\request\LinkHandler;
use wcf\system\tour\TourHandler;
use wcf\system\WCF;

/**
 * Executes tour-related actions.
 *
 * @author	Magnus Kühn
 * @copyright	2013 Thurnax.com
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
	protected $permissionsUpdate = array('admin.user.canEditTour');
	
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsDelete
	 */
	protected $permissionsDelete = array('admin.user.canEditTour');
	
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$requireACP
	 */
	protected $requireACP = array('update', 'delete', 'move');
	
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$resetCache
	 */
	protected $resetCache = array('create', 'delete', 'toggle', 'update', 'updatePosition', 'move');
	
	/**
	 * @see	\wcf\data\IToggleAction::toggle()
	 */
	public function toggle() {
		foreach ($this->objects as $tourStep) {
			$tourStep->update(array(
				'isDisabled' => $tourStep->isDisabled ? 0 : 1
			));
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
		$tour = $this->getSingleObject();
		TourHandler::getInstance()->startTour($tour->getDecoratedObject());
		TourHandler::getInstance()->takeTour($tour->getDecoratedObject());
		return TourStepCacheBuilder::getInstance()->getData(array('tourID' => $tour->tourID));
	}
	
	/**
	 * Validates the 'loadTour'-action
	 */
	public function validateLoadTour() {
		WCF::getSession()->checkPermissions(array('user.tour.enableTour'));
	}
	
	/**
	 * Loads the steps for a tour by the tour name
	 * 
	 * @return        array<mixed>
	 */
	public function loadTourByName() {
		$cache = TourTriggerCacheBuilder::getInstance()->getData(array(), 'manual');
		$this->setObjects(array($cache[$this->parameters['tourName']]));
		$this->objectIDs = array($this->objects[0]->tourID); // @todo Remove after merging #1606 (https://github.com/WoltLab/WCF/pull/1606)
		return $this->loadTour();
	}
	
	/**
	 * Validates the 'loadTourByName'-action
	 */
	public function validateLoadTourByName() {
		$this->readString('tourName');
		WCF::getSession()->checkPermissions(array('user.tour.enableTour'));
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
		WCF::getSession()->checkPermissions(array('user.tour.enableTour'));
	}

	/**
	 * Moves tour steps to another tour
	 * 
	 * @return	string
	 */
	public function move() {
		$targetTour = $this->getSingleObject();
		$objectTypeID = ClipboardHandler::getInstance()->getObjectTypeID('com.thurnax.wcf.tour.step');
		
		// update tour steps
		$sql = "UPDATE	".TourStep::getDatabaseTableName()."
			SET	tourID = ?
			WHERE	tourStepID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		foreach (ClipboardHandler::getInstance()->getMarkedItems($objectTypeID) as $tourStep) {
			$statement->execute(array($targetTour->tourID, $tourStep->tourStepID));
		}
		
		// clear clipboard
		ClipboardHandler::getInstance()->unmarkAll($objectTypeID);
		return LinkHandler::getInstance()->getLink('TourStepList', array('id' => $targetTour->tourID));
	}

	/**
	 * Validates the 'move'-action
	 */
	public function validateMove() {
		WCF::getSession()->checkPermissions($this->permissionsUpdate);
	}
}
