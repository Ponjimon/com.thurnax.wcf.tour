<?php
namespace wcf\system\tour\storage;

/**
 * Tour state storage for users
 *
 * @author    Magnus Kühn
 * @copyright 2013-2014 Thurnax.com
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package   com.thurnax.wcf.tour
 */
abstract class AbstractTourStateStorage implements ITourStateStorage {
	/**
	 * cache for the current user
	 *
	 * @var array
	 */
	protected $cache = array('availableTours' => array(), 'takenTours' => array(), 'lastTourTime' => 0);

	/**
	 * Initializes the tour state storage
	 */
	abstract public function __construct();

	/**
	 * Reads cookie data
	 */
	protected function readCookie() {
		if (isset($_COOKIE[COOKIE_PREFIX.self::STORAGE_NAME])) {
			$this->cache['takenTours'] = @unserialize($_COOKIE[COOKIE_PREFIX.self::STORAGE_NAME]);

			if (!$this->cache['takenTours'] || !is_array($this->cache['takenTours'])) {
				$this->cache['takenTours'] = array();
			}
		}
	}

	/**
	 * Returns the available tours with the tour trigger 'manual'
	 *
	 * @return int[]
	 */
	public function getAvailableManualTours() {
		return $this->cache['availableTours'];
	}

	/**
	 * Returns the taken tours
	 *
	 * @return int[]
	 */
	public function getTakenTours() {
		return $this->cache['takenTours'];
	}

	/**
	 * Checks whether a tour should be started
	 *
	 * @return boolean
	 */
	public function shouldStartTour() {
		return ($this->cache['lastTourTime'] + TOUR_COOLDOWN_TIME * 60) <= TIME_NOW;
	}

	/**
	 * Marks a tour as taken
	 *
	 * @param int $tourID
	 */
	public function takeTour($tourID) {
		// update cache
		$this->cache['takenTours'][] = $tourID;
		$this->cache['lastTourTime'] = TIME_NOW;
		if (($index = array_search($tourID, $this->cache['availableTours'])) !== null) {
			unset ($this->cache['availableTours'][$index]);
		}
	}
}
