<?php
namespace wcf\data\tour;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\acl\ACLHandler;
use wcf\system\cache\builder\TourCacheBuilder;
use wcf\system\cache\builder\TourTriggerCacheBuilder;
use wcf\system\tour\TourHandler;
use wcf\system\WCF;

/**
 * Provides functions to edit tours.
 *
 * @property integer $tourID
 * @property string  $visibleName
 * @property integer $isDisabled
 * @property integer $packageID
 * @property string  $tourTrigger
 * @property string  $className
 * @property string  $identifier
 * @author    Magnus Kühn
 * @copyright 2013-2014 Thurnax.com
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package   com.thurnax.wcf.tour
 */
class TourEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	const TOUR_IMPORTED_NOTICE = 'tourImportedNotice';

	/**
	 * name of the base class
	 *
	 * @var string
	 */
	protected static $baseClass = 'wcf\data\tour\Tour';

	/**
	 * Deletes all objects with the given ids and returns the number of deleted
	 * objects.
	 *
	 * @param int[] $objectIDs
	 * @return int
	 */
	public static function deleteAll(array $objectIDs = array()) {
		$count = parent::deleteAll($objectIDs);

		// remove ACL values
		ACLHandler::getInstance()->removeValues(ACLHandler::getInstance()->getObjectTypeID('com.thurnax.wcf.tour'), $objectIDs);

		return $count;
	}

	/**
	 * Resets the cache of this object type.
	 */
	public static function resetCache() {
		TourCacheBuilder::getInstance()->reset();
		TourTriggerCacheBuilder::getInstance()->reset();
		TourHandler::reset();
		WCF::getSession()->unregister(self::TOUR_IMPORTED_NOTICE);
	}
}
