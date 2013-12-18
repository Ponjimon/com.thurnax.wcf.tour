<?php
namespace wcf\data\tour;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\IToggleAction;
use wcf\system\cache\builder\TourStepCacheBuilder;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;

/**
 * Executes tour-related actions.
 *
 * @author	Magnus Kühn
 * @copyright	2013 Thurnax.com
 * @package	com.thurnax.wcf.tour
 * @category	Community Framework (commercial)
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
	protected $requireACP = array('update', 'delete');

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
	 */
	public function loadSteps() {
		$data = TourStepCacheBuilder::getInstance()->getData(array('tourName' => $this->parameters['tourName']));
		if (!$data) {
			throw new UserInputException('tourName');
		}
		
		$this->objectIDs = array($data['tour']->tourID);
		return $data['tourSteps'];
	}
	
	/**
	 * Validates the 'loadSteps'-action
	 */
	public function validateLoadSteps() {
		$this->readString('tourName');
		WCF::getSession()->checkPermissions(array('user.tour.enableTour'));
	}
}
