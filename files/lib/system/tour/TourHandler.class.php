<?php
namespace wcf\system\tour;
use wcf\system\cache\builder\TourCacheBuilder;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\SingletonFactory;
use wcf\system\tour\storage\GuestTourStateStorage;
use wcf\system\tour\storage\UserTourStateStorage;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;

/**
 * Handles tours
 *
 * @author	Magnus Kühn
 * @copyright	2013-2014 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class TourHandler extends SingletonFactory {
	const SESSION_FIELD = 'activeTour';
	
	/**
	 * tour state storage
	 * @var	\wcf\system\tour\storage\ITourStateStorage
	 */
	protected $tourStateStorage = null;
	
	/**
	 * @see	\wcf\system\SingletonFactory::init()
	 */
	protected function init() {		
		// init tour state storage
		if (WCF::getUser()->userID) {
			$this->tourStateStorage = new UserTourStateStorage();
		} else {
			$this->tourStateStorage = new GuestTourStateStorage();
		}
	}
	
	/**
	 * Checks if the tours are enabled for the current user
	 * 
	 * @return	boolean
	 */
	public function isEnabled() {
		return MODULE_TOUR;
	}
	
	/**
	 * Returns the available tours with the tour trigger 'manual'
	 * 
	 * @return	array<string>
	 */
	public function getAvailableManualTours() {
		return $this->tourStateStorage->getAvailableManualTours();
	}
	
	/**
	 * Returns the active tour
	 * 
	 * @return	string
	 */
	public function getActiveTour() {
		return WCF::getSession()->getVar(self::SESSION_FIELD);
	}
	
	/**
	 * Starts a tour
	 * 
	 * @param	integer $tourID
	 * @param       boolean $force
	 * @return	boolean
	 */
	public function startTour($tourID, $force = false) {
		if ($this->isEnabled() && self::canViewTour($tourID) && !$this->getActiveTour() && ($force || !in_array($tourID, $this->tourStateStorage->getTakenTours()))) {
			WCF::getSession()->register(self::SESSION_FIELD, $tourID);
			return true;
		}
		
		return false;
	}
	
	/**
	 * Ends the current tour
	 */
	public function endTour() {
		WCF::getSession()->unregister(self::SESSION_FIELD);
	}
	
	/**
	 * Marks a tour as taken
	 *
	 * @param        integer $tourID
	 */
	public function takeTour($tourID) {
		if ($this->isEnabled() && !in_array($tourID, $this->tourStateStorage->getTakenTours())) {
			if (!self::canViewTour($tourID)) {
				throw new PermissionDeniedException();
			}
			
			$this->tourStateStorage->takeTour($tourID);
		}
	}
	
	/**
	 * Checks if the user may access the tour
	 * 
	 * @param	integer	$tourID
	 * @return	boolean
	 */
	public static function canViewTour($tourID) {
		$viewableTours = TourCacheBuilder::getInstance()->getData(array(), 'viewableTours');
		if (!isset($viewableTours[$tourID])) {
			return false;
		}
		
		$aclPermission = $viewableTours[$tourID]->getPermission();
		return ($aclPermission === null ? WCF::getSession()->getPermission('user.tour.canViewTour') : $aclPermission);
	}
	
	/**
	 * Resets the user storage
	 */
	public static function reset() {
		UserStorageHandler::getInstance()->resetAll(UserTourStateStorage::STORAGE_NAME);
	}
}
