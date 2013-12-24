<?php
namespace wcf\system\event\listener;
use wcf\system\cache\builder\TourTriggerCacheBuilder;
use wcf\system\event\IEventListener;
use wcf\system\tour\TourHandler;

/**
 * Event listener for show@wcf\page\IPage.
 *
 * @author	Magnus Kühn
 * @copyright	2013 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class TourPageListener implements IEventListener {
	/**
	 * cache of tours
	 * @var	array<string>
	 */
	protected $cache = array();
	
	/**
	 * Initializes the event listener
	 */
	public function __construct() {
		$this->cache = TourTriggerCacheBuilder::getInstance()->getData(array());
		
		foreach ($this->cache['firstSite'] as $tour) {
			TourHandler::getInstance()->startTour($tour);
		}
	}
	
	/**
	 * @see	\wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		if (isset($this->cache['specificSite'][$className])) {
			TourHandler::getInstance()->startTour($this->cache['specificSite'][$className]);
		}
	}
}