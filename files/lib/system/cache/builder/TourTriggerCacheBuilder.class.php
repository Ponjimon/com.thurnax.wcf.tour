<?php
namespace wcf\system\cache\builder;
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
		return array(
			'firstSite' => $this->fetchFirstSiteTrigger(),
			'specificSite' => $this->fetchSpecificSiteTrigger(),
			'manual' => $this->fetchManualTriggers()
		);
	}
	
	/**
	 * Fetches tours using the first site trigger
	 *
	 * @return	array<string>
	 */
	protected function fetchFirstSiteTrigger() {
		$tourList = new TourList();
		$tourList->getConditionBuilder()->add('tourTrigger = ?', array('firstSite'));
		$tourList->readObjects();
		return $tourList->getObjects();
	}
	
	/**
	 * Fetches tours using the specific site trigger
	 * 
	 * @return	array<string>
	 */
	protected function fetchSpecificSiteTrigger() {
		$tourList = new TourList();
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
	 * @return	array<string>
	 */
	protected function fetchManualTriggers() {
		$tourList = new TourList();
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
