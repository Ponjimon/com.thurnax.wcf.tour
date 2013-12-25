<?php
namespace wcf\data\tour;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\cache\builder\TourStepCacheBuilder;
use wcf\system\cache\builder\TourTriggerCacheBuilder;
use wcf\system\tour\TourHandler;

/**
 * Provides functions to edit tours.
 *
 * @author	Magnus Kühn
 * @copyright	2013 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class TourEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * @see	\wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = 'wcf\data\tour\Tour';
	
	/**
	 * @see	\wcf\data\IEditableCachedObject::resetCache()
	 */
	public static function resetCache() {
		TourStepCacheBuilder::getInstance()->reset();
		TourTriggerCacheBuilder::getInstance()->reset();
		TourHandler::getInstance()->reset();
	}
}
