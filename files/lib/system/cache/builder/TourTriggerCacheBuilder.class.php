<?php
namespace wcf\system\cache\builder;
use wcf\data\tour\step\TourStep;
use wcf\data\tour\TourList;

/**
 * Caches all tours using the manual tour trigger
 * 
 * @author	Magnus Kühn
 * @copyright	2013 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class TourTriggerCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @see	\wcf\system\cache\builder\AbstractCacheBuilder::rebuild()
	 */
	public function rebuild(array $parameters) {
		$tourList = new TourList();
		$tourList->getConditionBuilder()->add("(SELECT	COUNT(tourStepID) as count
							FROM	".TourStep::getDatabaseTableName()." tour_step
							WHERE	tour_step.tourID = ".$tourList->getDatabaseTableAlias().".tourID) > 0");
		
		return array(
			'firstSite' => $this->fetchFirstSiteTrigger($tourList),
			'specificSite' => $this->fetchSpecificSiteTrigger($tourList),
			'manual' => $this->fetchManualTriggers($tourList)
		);
	}
	
	/**
	 * Fetches tours using the first site trigger
	 * 
	 * @param	\wcf\data\tour\TourList	$tourList
	 * @return	array<\wcf\data\tour\Tour>
	 */
	protected function fetchFirstSiteTrigger(TourList $tourList) {
		$tourList->getConditionBuilder()->add('tourTrigger = ?', array('firstSite'));
		$tourList->readObjects();
		return $tourList->getObjects();
	}
	
	/**
	 * Fetches tours using the specific site trigger
	 * 
	 * @param	\wcf\data\tour\TourList	$tourList
	 * @return	array<\wcf\data\tour\Tour>
	 */
	protected function fetchSpecificSiteTrigger(TourList $tourList) {
		$tourList->getConditionBuilder()->add('tourTrigger = ?', array('specificSite'));
		$tourList->readObjects();
		
		// use class name as key
		$tours = array();
		foreach ($tourList->getObjects() as $tour) {
			$tours[$tour->className] = $tour;
		}
		
		return $tours;
	}
	
	/**
	 * Fetches tours using the manual trigger
	 * 
	 * @param	\wcf\data\tour\TourList	$tourList
	 * @return	array<\wcf\data\tour\Tour>
	 */
	protected function fetchManualTriggers(TourList $tourList) {
		$tourList->getConditionBuilder()->add('tourTrigger = ?', array('manual'));
		$tourList->readObjects();

		// use tour name as key
		$tours = array();
		foreach ($tourList->getObjects() as $tour) {
			$tours[$tour->tourName] = $tour;
		}

		return $tours;
	}
}
