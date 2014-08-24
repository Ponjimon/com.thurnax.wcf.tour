<?php
namespace wcf\system\tour\storage;
use wcf\util\HeaderUtil;

/**
 * Tour state storage for guests
 *
 * @author    Magnus Kühn
 * @copyright 2013-2014 Thurnax.com
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package   com.thurnax.wcf.tour
 */
class GuestTourStateStorage extends AbstractTourStateStorage {
	/**
	 * Initializes the tour state storage
	 */
	public function __construct() {
		$this->readCookie();
	}

	/**
	 * Marks a tour as taken
	 *
	 * @param int $tourID
	 */
	public function takeTour($tourID) {
		parent::takeTour($tourID);
		HeaderUtil::setCookie(self::STORAGE_NAME, serialize($this->cache['takenTours']));
	}
}
