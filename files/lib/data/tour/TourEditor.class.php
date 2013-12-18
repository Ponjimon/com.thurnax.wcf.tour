<?php
namespace wcf\data\tour;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\cache\builder\TourStepCacheBuilder;

/**
 * Provides functions to edit tours.
 *
 * @author	Magnus Kühn
 * @copyright	2013 Thurnax.com
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
	}
}