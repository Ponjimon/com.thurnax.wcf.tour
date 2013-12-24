<?php
namespace wcf\data\tour;
use wcf\data\DatabaseObject;
use wcf\system\WCF;

/**
 * Represents a tour.
 *
 * @author	Magnus Kühn
 * @copyright	2013 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class Tour extends DatabaseObject {
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'tour';
	
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'tourID';
	
	/**
	 * Fetches a tour by the tour name
	 * 
	 * @param	string	$tourName
	 * @return	\wcf\data\tour\Tour
	 */
	public static function getByName($tourName) {
		$sql = "SELECT	*
			FROM	".self::getDatabaseTableName()."
			WHERE	tourName = ?";
		$statement = WCF::getDB()->prepareStatement($sql, 1);
		$statement->execute(array($tourName));
		return $statement->fetchObject('wcf\data\tour\Tour');
	}
}
