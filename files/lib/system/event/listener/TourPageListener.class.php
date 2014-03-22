<?php
namespace wcf\system\event\listener;
use wcf\system\cache\builder\TourTriggerCacheBuilder;
use wcf\system\event\IEventListener;
use wcf\system\tour\TourHandler;

/**
 * Event listener for show@wcf\page\IPage.
 *
 * @author	Magnus Kühn
 * @copyright	2013-2014 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class TourPageListener implements IEventListener {
	/**
	 * cache of tours using the 'specificSite'-trigger
	 * @var	array<integer>
	 */
	protected $cache = array();
	
	/**
	 * Initializes the event listener
	 */
	public function __construct() {
		// start tours using the 'firstSite'-trigger
		foreach (TourTriggerCacheBuilder::getInstance()->getData(array(), 'firstSite') as $tourID) {
			if (TourHandler::getInstance()->startTour($tourID)) {
				return;
			}
		}
	}
	
	/**
	 * @see	\wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		$triggers = TourTriggerCacheBuilder::getInstance()->getData(array(), 'specificSite');
		
		if (isset($triggers[$className])) {
			TourHandler::getInstance()->startTour($triggers[$className]);
		}
	}
}
