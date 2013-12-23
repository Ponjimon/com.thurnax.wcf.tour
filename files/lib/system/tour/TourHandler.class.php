<?php
namespace wcf\system\tour;
use wcf\data\tour\Tour;
use wcf\system\cache\builder\TourCacheBuilder;
use wcf\system\SingletonFactory;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;

/**
 * Handles tours
 *
 * @author	Magnus Kühn
 * @copyright	2013 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class TourHandler extends SingletonFactory {
	const SESSION_FIELD = 'activeTour';
	const USER_STORAGE_FIELD = 'takenTours';
	
	/**
	 * cache for the current user
	 * @var	array<string>
	 */
	protected $cache = array('takenTours' => array(), 'availableTours' => array());
	
	/**
	 * @see	\wcf\system\SingletonFactory::init()
	 */
	protected function init() {
		$userID = WCF::getUser()->userID;
		UserStorageHandler::getInstance()->loadStorage(array($userID));
		$data = UserStorageHandler::getInstance()->getStorage(array($userID), self::USER_STORAGE_FIELD);
		if ($data[$userID] === null) { // build cache
			$sql = "SELECT	tourID
				FROM	".Tour::getDatabaseTableName()."_user
				WHERE	userID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array($userID));
			
			// collect taken tour ids
			while ($row = $statement->fetchArray()) {
				$this->cache['takenTours'][] = $row['tourID'];
			}
			
			// get available tours
			foreach (TourCacheBuilder::getInstance()->getData() as $tourName => $tour) {
				if (!in_array($tour->tourID, $this->cache['takenTours'])) {
					$this->cache['availableTours'][$tour->tourID] = $tourName;
				}
			}
			
			// update user storage
			UserStorageHandler::getInstance()->update($userID, self::USER_STORAGE_FIELD, serialize($this->cache));
		} else {
			$this->cache = unserialize($data[$userID]);
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
	 * Returns the available tours
	 * 
	 * @return	array<string>
	 */
	public function getAvailableTours() {
		return array_values($this->cache['availableTours']);
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
	 * @param	\wcf\data\tour\Tour	$tour
	 */
	public function startTour(Tour $tour) {
		if (!in_array($tour->tourID, $this->cache['takenTours'])) {
			WCF::getSession()->register(self::SESSION_FIELD, $tour->tourID);
		}
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
	 * @param	\wcf\data\tour\Tour	$tour
	 */
	public function takeTour(Tour $tour) {
		if (!in_array($tour->tourID, $this->cache['takenTours'])) {
			$sql = "INSERT INTO ".Tour::getDatabaseTableName()."_user (tourID, userID) VALUES (?, ?)";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array($tour->tourID, WCF::getUser()->userID));
			
			// update cache
			if (isset($this->cache['availableTours'][$tour->tourID])) {
				unset ($this->cache['availableTours'][$tour->tourID]);
			}
			
			$this->cache['takenTours'][] = $tour->tourID;
			UserStorageHandler::getInstance()->update(WCF::getUser()->userID, self::USER_STORAGE_FIELD, serialize($this->cache));
		}
	}
}
