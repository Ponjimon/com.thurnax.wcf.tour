<?php
namespace wcf\system\tour;
use wcf\data\tour\Tour;
use wcf\system\cache\builder\TourCacheBuilder;
use wcf\system\cache\builder\TourTriggerCacheBuilder;
use wcf\system\SingletonFactory;
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
	const USER_STORAGE_FIELD = 'tourCache';
	
	/**
	 * cache for the current user
	 * @var	array<string>
	 */
	protected $userCache = array('takenTours' => array(), 'availableTours' => array());

	/**
	 * viewable tour cache
	 * @var	array<\wcf\data\tour\ViewableTour>
	 */
	protected $viewableTourCache = array();
	
	/**
	 * @see	\wcf\system\SingletonFactory::init()
	 */
	protected function init() {
		if ($this->isEnabled()) {
			// build viewable tour cache
			$this->viewableTourCache = TourCacheBuilder::getInstance()->getData(array(), 'viewableTours');
			
			// build user cache
			UserStorageHandler::getInstance()->loadStorage(array(WCF::getUser()->userID));
			$data = UserStorageHandler::getInstance()->getStorage(array(WCF::getUser()->userID), self::USER_STORAGE_FIELD);
			if ($data[WCF::getUser()->userID] === null) { // build cache
				$sql = "SELECT	tourID
					FROM	".Tour::getDatabaseTableName()."_user
					WHERE	userID = ?";
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute(array(WCF::getUser()->userID));
				
				// collect taken tour ids
				while ($row = $statement->fetchArray()) {
					$this->userCache['takenTours'][] = $row['tourID'];
				}
				
				// get available tours
				foreach (TourTriggerCacheBuilder::getInstance()->getData(array(), 'manual') as $tourName => $tourID) {
					if (!in_array($tourID, $this->userCache['takenTours']) && $this->canViewTour($tourID)) {
						$this->userCache['availableTours'][$tourID] = $tourName;
					}
				}
				
				// update user storage
				UserStorageHandler::getInstance()->update(WCF::getUser()->userID, self::USER_STORAGE_FIELD, serialize($this->userCache));
			} else {
				$this->userCache = unserialize($data[WCF::getUser()->userID]);
			}
		}
	}
	
	/**
	 * Checks if the tours are enabled for the current user
	 * 
	 * @return	boolean
	 */
	public function isEnabled() {
		return MODULE_TOUR && WCF::getUser()->userID;
	}
	
	/**
	 * Returns the available tours
	 * 
	 * @return	array<string>
	 */
	public function getAvailableTours() {
		return array_values($this->userCache['availableTours']);
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
	 * @param	integer	$tourID
	 * @return	boolean
	 */
	public function startTour($tourID) {
		if (!$this->getActiveTour() && !in_array($tourID, $this->userCache['takenTours']) && $this->canViewTour($tourID)) {
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
	 * @param	integer	$tourID
	 */
	public function takeTour($tourID) {
		if (!in_array($tourID, $this->userCache['takenTours']) && $this->canViewTour($tourID)) {
			$sql = "INSERT INTO ".Tour::getDatabaseTableName()."_user (tourID, userID) VALUES (?, ?)";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array($tourID, WCF::getUser()->userID));
			
			// update cache
			if (isset($this->userCache['availableTours'][$tourID])) {
				unset ($this->userCache['availableTours'][$tourID]);
			}
			
			$this->userCache['takenTours'][] = $tourID;
			UserStorageHandler::getInstance()->update(WCF::getUser()->userID, self::USER_STORAGE_FIELD, serialize($this->userCache));
		}
	}
	
	/**
	 * Checks if the user may access the tour
	 * 
	 * @param	integer	$tourID
	 * @return	boolean
	 */
	protected function canViewTour($tourID) {
		if (!$this->isEnabled() || !isset($this->viewableTourCache[$tourID])) {
			return false;
		}
		
		$aclPermission = $this->viewableTourCache[$tourID]->getPermission();
		return ($aclPermission === null ? WCF::getSession()->getPermission('user.tour.canViewTour') : $aclPermission);
	}
	
	/**
	 * Resets the user storage
	 */
	public static function reset() {
		UserStorageHandler::getInstance()->resetAll(self::USER_STORAGE_FIELD);
	}
}
