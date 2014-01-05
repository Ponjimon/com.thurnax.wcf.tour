<?php
use wcf\data\package\PackageCache;
use wcf\data\tour\step\TourStep;
use wcf\data\tour\Tour;
use wcf\system\WCF;

/**
 * Updates the database to 1.0.0 RC 2
 * 
 * @author	Magnus Kühn
 * @copyright	2013-2014 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */

// set package id
setPackageID(Tour::getDatabaseTableName());
setPackageID(TourStep::getDatabaseTableName());

/**
 * Sets the package id in a table
 * 
 * @param	string	$databaseTableName
 */
function setPackageID($databaseTableName) {
	$sql = "UPDATE " . $databaseTableName . " SET packageID = ?";
	$statement = WCF::getDB()->prepareStatement($sql);
	$statement->execute(array(PackageCache::getInstance()->getPackageID('com.thurnax.wcf.tour')));
}
