<?php
namespace wcf\system\tour\storage;
use wcf\util\HeaderUtil;

/**
 * Tour state storage for guests
 * 
 * @author	Magnus Kühn
 * @copyright	2013-2014 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class GuestTourStateStorage extends AbstractTourStateStorage {
	/**
	 * @see	\wcf\system\tour\storage\AbstractTourStateStorage::__construct()
	 */
	public function __construct() {
		$this->readCookie();
	}
	
	/**
	 * @see	\wcf\system\tour\storage\ITourStateStorage::takeTour()
	 */
	public function takeTour($tourID) {
		parent::takeTour($tourID);
		HeaderUtil::setCookie(self::STORAGE_NAME, serialize($this->cache['takenTours']));
	}
}
