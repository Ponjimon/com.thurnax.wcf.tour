<?php
namespace wcf\data\tour;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\IToggleAction;
use wcf\system\cache\builder\TourCacheBuilder;
use wcf\system\cache\builder\TourStepCacheBuilder;
use wcf\system\exception\UserInputException;
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
		$this->setObjects(array(TourCacheBuilder::getInstance()->getData(array(), $this->parameters['tourName'])));
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
}
