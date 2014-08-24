<?php
namespace wcf\system\tour;
use wcf\system\cache\builder\TourCacheBuilder;
use wcf\system\cache\builder\TourTriggerCacheBuilder;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\SingletonFactory;
use wcf\system\tour\storage\GuestTourStateStorage;
use wcf\system\tour\storage\UserTourStateStorage;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;

/**
 * Handles tours.
 *
 * @author    Magnus Kühn
 * @copyright 2013-2014 Thurnax.com
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package   com.thurnax.wcf.tour
 */
class TourHandler extends SingletonFactory {
	/**
	 * session field for storing the active tour
	 */
	const SESSION_FIELD = 'activeTour';

	/**
	 * tour state storage
	 *
	 * @var \wcf\system\tour\storage\ITourStateStorage
	 */
	protected $tourStateStorage = null;

	/**
	 * Called within __construct(), override if necessary.
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
	 * @return boolean
	 */
	public function isEnabled() {
		return MODULE_TOUR;
	}

	/**
	 * Returns all tours with the trigger 'manual'
	 *
	 * @return string[]
	 */
	public function getManualTours() {
		return TourTriggerCacheBuilder::getInstance()->getData(array(), 'manual');
	}

	/**
	 * Returns the available tours with the tour trigger 'manual'
	 *
	 * @return string[]
	 */
	public function getAvailableManualTours() {
		return $this->tourStateStorage->getAvailableManualTours();
	}

	/**
	 * Returns the active tour
	 *
	 * @return string
	 */
	public function getActiveTour() {
		return WCF::getSession()->getVar(self::SESSION_FIELD);
	}

	/**
	 * Starts a tour
	 *
	 * @param int           $tourID
	 * @param       boolean $force
	 * @return boolean
	 */
	public function startTour($tourID, $force = false) {
		if ($this->isEnabled() && self::canViewTour($tourID) && !$this->getActiveTour() && ($force || (!in_array($tourID, $this->tourStateStorage->getTakenTours()) && $this->tourStateStorage->shouldStartTour()))) {
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
	 * @param int $tourID
	 * @throws \wcf\system\exception\PermissionDeniedException
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
	 * @param int    $tourID
	 * @param string $permission
	 * @return boolean
	 */
	public static function canViewTour($tourID, $permission = 'canViewTour') {
		/** @var $viewableTours \wcf\data\tour\ViewableTour[] */
		$viewableTours = TourCacheBuilder::getInstance()->getData(array(), 'tours');
		if (!isset($viewableTours[$tourID])) {
			return false;
		}

		// check acl permission, fallback to user group permission if none is set
		$aclPermission = $viewableTours[$tourID]->getPermission($permission);
		return ($aclPermission === null ? WCF::getSession()->getPermission('user.tour.'.$permission) : $aclPermission);
	}

	/**
	 * Resets the user storage
	 */
	public static function reset() {
		UserStorageHandler::getInstance()->resetAll(UserTourStateStorage::STORAGE_NAME);
	}
}
