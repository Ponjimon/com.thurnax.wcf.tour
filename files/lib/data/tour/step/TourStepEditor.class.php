<?php
namespace wcf\data\tour\step;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\cache\builder\TourStepCacheBuilder;
use wcf\system\cache\builder\TourTriggerCacheBuilder;

/**
 * Provides functions to edit tour steps.
 * 
 * @author	Magnus Kühn
 * @copyright	2013 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class TourStepEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * @see	\wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = 'wcf\data\tour\step\TourStep';
	
	/**
	 * @see	\wcf\data\IEditableCachedObject::resetCache()
	 */
	public static function resetCache() {
		TourStepCacheBuilder::getInstance()->reset();
		TourTriggerCacheBuilder::getInstance()->reset();
	}
}
