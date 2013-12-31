<?php
namespace wcf\system\tour\storage;
use wcf\data\tour\Tour;
use wcf\system\cache\builder\TourTriggerCacheBuilder;
use wcf\system\tour\TourHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;

/**
 * Tour state storage for users
 * 
 * @author	Magnus Kühn
 * @copyright	2013-2014 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class UserTourStateStorage extends AbstractTourStateStorage {	
	/**
	 * @see	\wcf\system\tour\storage\AbstractTourStateStorage::__construct()
	 */
	public function __construct() {
		UserStorageHandler::getInstance()->loadStorage(array(WCF::getUser()->userID));
		$data = UserStorageHandler::getInstance()->getStorage(array(WCF::getUser()->userID), self::STORAGE_NAME);
		if ($data[WCF::getUser()->userID] === null) { // build cache
			$sql = "SELECT	tourID
				FROM	".Tour::getDatabaseTableName()."_user
				WHERE	userID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array(WCF::getUser()->userID));
			
			// collect taken tour ids
			while ($row = $statement->fetchArray()) {
				$this->cache['takenTours'][] = $row['tourID'];
			}
			
			// get available tours
			foreach (TourTriggerCacheBuilder::getInstance()->getData(array(), 'manual') as $tourName => $tourID) {
				if (!in_array($tourID, $this->cache['takenTours']) && TourHandler::getInstance()->canViewTour($tourID)) {
					$this->cache['availableTours'][$tourID] = $tourName;
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
		
		parent::takeTour($tourID);
		UserStorageHandler::getInstance()->update(WCF::getUser()->userID, self::STORAGE_NAME, serialize($this->cache));
	}
}
