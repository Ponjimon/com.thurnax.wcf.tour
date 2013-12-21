<?php
namespace wcf\system\tour;
use wcf\system\SingletonFactory;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;

/**
 * Handles tours for the current user
 *
 * @author	Magnus Kühn
 * @copyright	2013 Thurnax.com
 * @package	com.thurnax.wcf.tour
 */
class TourHandler extends SingletonFactory {
	const USER_STORAGE_FIELD = 'activeTours';
	
	/**
	 * cache for currently active tours
	 * @var	array<string>
	 */
	protected $activeTours = array();
	
	/**
	 * @see	\wcf\system\SingletonFactory::init()
	 */
	protected function init() {
		// load active tours
		$userID = WCF::getUser()->userID;
		UserStorageHandler::getInstance()->loadStorage(array($userID));
		$data = UserStorageHandler::getInstance()->getStorage(array($userID), self::USER_STORAGE_FIELD);
		if ($data[$userID] !== null) {
			$this->activeTours = unserialize($data[$userID]);
		}
	}
	
	/**
	 * Checks if the tours are enabled for the current user
	 * 
	 * @return	boolean
	 */
	public function isEnabled() {
		return MODULE_TOUR && WCF::getSession()->getPermission('user.tour.enableTour');
	}
	
	/**
	 * Returns the active tours
	 * 
	 * @return	array<string>
	 */
	public function getActiveTours() {
		return $this->activeTours;
	}
	
	/**
	 * Starts a tour
	 * 
	 * @param	string	$tourName
	 */
	public function startTour($tourName) {
		if (!in_array($tourName, $this->activeTours)) {
			$this->activeTours[] = $tourName;
		}
		
		UserStorageHandler::getInstance()->update(WCF::getUser()->userID, self::USER_STORAGE_FIELD, serialize($this->activeTours));
	}
	
	/**
	 * Marks a tour as ended
	 * 
	 * @param	string	$tourName
	 */
	public function endTour($tourName) {
		if(($key = array_search($tourName, $this->activeTours)) !== false) {
			unset($this->activeTours[$key]);
		}
		
		UserStorageHandler::getInstance()->update(WCF::getUser()->userID, self::USER_STORAGE_FIELD, serialize($this->activeTours));
	}
}
