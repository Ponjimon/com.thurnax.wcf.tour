<?php
namespace wcf\system\tour\storage;
use wcf\data\tour\Tour;
use wcf\system\cache\builder\TourCacheBuilder;
use wcf\system\cache\builder\TourTriggerCacheBuilder;
use wcf\system\tour\TourHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

/**
 * Tour state storage for users
 * 
 * @author	Magnus Kühn
 * @copyright	2013-2014 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class UserTourStateStorage extends GuestTourStateStorage {	
	/**
	 * @see	\wcf\system\tour\storage\AbstractTourStateStorage::__construct()
	 */
	public function __construct() {
		UserStorageHandler::getInstance()->loadStorage(array(WCF::getUser()->userID));
		$data = UserStorageHandler::getInstance()->getStorage(array(WCF::getUser()->userID), self::STORAGE_NAME);
		
		if ($data[WCF::getUser()->userID] === null) {
			parent::__construct();
			
			// collect taken tour ids from database
			if (!$this->cache['takenTours']) {
				$sql = "SELECT	tourID
					FROM	".Tour::getDatabaseTableName()."_user
					WHERE	userID = ?";
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute(array(WCF::getUser()->userID));
				
				while ($row = $statement->fetchArray()) {
					$this->cache['takenTours'][] = $row['tourID'];
				}
			} else { // import cookie data to the database
				$sql = "INSERT IGNORE INTO ".Tour::getDatabaseTableName()."_user (tourID, userID) VALUES (?, ?)";
				$statement = WCF::getDB()->prepareStatement($sql);
				$viewableTours = TourCacheBuilder::getInstance()->getData(array(), 'viewableTours');
				foreach ($this->getTakenTours() as $takenTourID) {
					if (isset($viewableTours[$takenTourID])) {
						$statement->execute(array($takenTourID, WCF::getUser()->userID));
					}
				}
				
				// delete cookie
				HeaderUtil::setCookie(self::STORAGE_NAME);
			}
			
			// get available tours
			foreach (TourTriggerCacheBuilder::getInstance()->getData(array(), 'manual') as $tourName => $tour) {
				if (!in_array($tour->tourID, $this->cache['takenTours']) && TourHandler::canViewTour($tour->tourID)) {
					$this->cache['availableTours'][$tour->tourID] = $tourName;
				}
			}
			
			// update user storage
			UserStorageHandler::getInstance()->update(WCF::getUser()->userID, self::STORAGE_NAME, serialize($this->cache));
		} else {
			$this->cache = unserialize($data[WCF::getUser()->userID]);
		}
	}
	
	/**
	 * @see	\wcf\system\tour\storage\ITourStateStorage::takeTour()
	 */
	public function takeTour($tourID) {
		$sql = "INSERT INTO ".Tour::getDatabaseTableName()."_user (tourID, userID) VALUES (?, ?)";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($tourID, WCF::getUser()->userID));
		
		AbstractTourStateStorage::takeTour($tourID);
		UserStorageHandler::getInstance()->update(WCF::getUser()->userID, self::STORAGE_NAME, serialize($this->cache));
	}
}
