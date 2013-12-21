<?php
namespace wcf\data\tour\step;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\ISortableAction;
use wcf\data\IToggleAction;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;

/**
 * Executes tour step-related actions.
 * 
 * @author	Magnus Kühn
 * @copyright	2013 Thurnax.com
 * @package	com.thurnax.wcf.tour
 * @category	Community Framework (commercial)
 */
class TourStepAction extends AbstractDatabaseObjectAction implements ISortableAction, IToggleAction {
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$className
	 */
	protected $className = 'wcf\data\tour\step\TourStepEditor';
	
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
	protected $requireACP = array('update', 'updatePosition', 'delete');
	
	/**
	 * @see	\wcf\data\ISortableAction::updatePosition()
	 */
	public function updatePosition() {
		$sql = "UPDATE	".TourStep::getDatabaseTableName()."
			SET	showOrder = ?
			WHERE	tourStepID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		
		WCF::getDB()->beginTransaction();
		foreach ($this->parameters['data']['structure'][0] as $showOrder => $tourStepID) {
			$statement->execute(array($showOrder + $this->parameters['data']['offset'], $tourStepID));
		}
		WCF::getDB()->commitTransaction();
	}
	
	/**
	 * @see	\wcf\data\ISortableAction::validateUpdatePosition()
	 */
	public function validateUpdatePosition() {
		WCF::getSession()->checkPermissions(array('admin.user.canEditTour'));
		$this->readInteger('offset', false, 'data');
		if (!isset($this->parameters['data']) || !isset($this->parameters['data']['structure']) || !isset($this->parameters['data']['structure'][0])) {
			throw new UserInputException('structure');
		}
		
		// read tour steps
		$this->objectIDs = $this->parameters['data']['structure'][0];		
		$tourStepList = new TourStepList();
		$tourStepList->setObjectIDs($this->objectIDs);
		$tourStepList->readObjects();
		$this->objects = $tourStepList->getObjects();
		
		// validate IDs
		if (count($this->objects) != count($this->objectIDs)) {
			throw new UserInputException('structure');
		}
	}
	
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
}
