<?php
namespace wcf\system\cache\builder;
use wcf\data\tour\step\TourStep;
use wcf\data\tour\TourList;

/**
 * Caches all tours using the manual tour trigger
 * 
 * @author	Magnus Kühn
 * @copyright	2013-2014 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class TourTriggerCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @see	\wcf\system\cache\builder\AbstractCacheBuilder::rebuild()
	 */
	public function rebuild(array $parameters) {
		return array(
			'firstSite' => $this->fetchFirstSiteTrigger(),
			'specificSite' => $this->fetchSpecificSiteTrigger(),
			'manual' => $this->fetchManualTriggers()
		);
	}
	
	/**
	 * Fetches tours using the first site trigger
	 * 
	 * @return	array<\wcf\data\tour\Tour>
	 */
	protected function fetchFirstSiteTrigger() {
		$tourList = new TourList();
		$tourList->getConditionBuilder()->add("(SELECT	COUNT(tourStepID) as count
							FROM	".TourStep::getDatabaseTableName()." tour_step
							WHERE	tour_step.tourID = ".$tourList->getDatabaseTableAlias().".tourID) > 0");
		$tourList->getConditionBuilder()->add('tourTrigger = ?', array('firstSite'));
		$tourList->readObjectIDs();
		return $tourList->getObjectIDs();
	}
	
	/**
	 * Fetches tours using the specific site trigger
	 * 
	 * @return	array<\wcf\data\tour\Tour>
	 */
	protected function fetchSpecificSiteTrigger() {
		$tourList = new TourList();
		$tourList->getConditionBuilder()->add("(SELECT	COUNT(tourStepID) as count
							FROM	".TourStep::getDatabaseTableName()." tour_step
							WHERE	tour_step.tourID = ".$tourList->getDatabaseTableAlias().".tourID) > 0");
		$tourList->getConditionBuilder()->add('tourTrigger = ?', array('specificSite'));
		$tourList->readObjects();
		
		// use class name as key
		$tourIDs = array();
		foreach ($tourList->getObjects() as $tour) {
			$tourIDs[$tour->className] = $tour->tourID;
		}
		
		return $tourIDs;
	}
	
	/**
	 * Fetches tours using the manual trigger
	 * 
	 * @return	array<\wcf\data\tour\Tour>
	 */
	protected function fetchManualTriggers() {
		$tourList = new TourList();
		$tourList->getConditionBuilder()->add("(SELECT	COUNT(tourStepID) as count
							FROM	".TourStep::getDatabaseTableName()." tour_step
							WHERE	tour_step.tourID = ".$tourList->getDatabaseTableAlias().".tourID) > 0");
		$tourList->getConditionBuilder()->add('tourTrigger = ?', array('manual'));
		$tourList->readObjects();
		
		// use tour name as key
		$tourIDs = array();
		foreach ($tourList->getObjects() as $tour) {
			$tourIDs[$tour->tourName] = $tour;
		}
		
		return $tourIDs;
	}
}
