<?php
namespace wcf\system\cache\builder;
use wcf\data\tour\TourList;

/**
 * Cache for matching tour names to their ID
 * 
 * @author	Magnus Kühn
 * @copyright	2013 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class TourCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @see	\wcf\system\cache\builder\AbstractCacheBuilder::rebuild()
	 */
	public function rebuild(array $parameters) {
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
