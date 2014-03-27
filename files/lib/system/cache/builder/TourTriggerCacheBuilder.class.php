<?php
namespace wcf\system\cache\builder;
use wcf\data\tour\step\TourStep;
use wcf\data\tour\Tour;
use wcf\system\WCF;

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
		$sql = "SELECT	tourID, className, tourTrigger
			FROM	".Tour::getDatabaseTableName()." tour
			WHERE	(SELECT	COUNT(tourStepID) as count
				FROM	".TourStep::getDatabaseTableName()." tour_step
				WHERE	tour_step.tourID = tour.tourID) > 0 AND
				isDisabled = 0";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();

		// group by tour trigger
		$tourTriggers = array('firstSite' => array(), 'specificSite' => array(), 'manual' => array());
		while ($row = $statement->fetchArray()) {
			if ($row['tourTrigger'] == 'specificSite') {
				$tourTriggers['specificSite'][$row['className']] = intval($row['tourID']);
			} else {
				$tourTriggers[$row['tourTrigger']][] = intval($row['tourID']);
			}
		}
		
		return $tourTriggers;
	}
}
