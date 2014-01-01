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

// update wcf1_tour
executeQuery("ALTER TABLE wcf1_tour ADD COLUMN packageID INT(10) NOT NULL");
executeQuery("ALTER TABLE wcf1_tour MODIFY tourTrigger ENUM('firstSite', 'specificSite', 'manual') NOT NULL DEFAULT 'firstSite'");

// update wcf1_tour_step
executeQuery("ALTER TABLE wcf1_tour_step ADD COLUMN packageID INT(10) NOT NULL");
executeQuery("ALTER TABLE wcf1_tour_step MODIFY placement ENUM('top', 'bottom', 'left', 'right') NOT NULL DEFAULT 'left'");
executeQuery("ALTER TABLE wcf1_tour_step MODIFY xOffset INT(10) NOT NULL DEFAULT 0");
executeQuery("ALTER TABLE wcf1_tour_step MODIFY yOffset INT(10) NOT NULL DEFAULT 0");
executeQuery("ALTER TABLE wcf1_tour_step ADD COLUMN ctaLabel VARCHAR(255) NULL DEFAULT NULL");
executeQuery("ALTER TABLE wcf1_tour_step ADD COLUMN onPrev MEDIUMTEXT NULL DEFAULT NULL");
executeQuery("ALTER TABLE wcf1_tour_step ADD COLUMN onNext MEDIUMTEXT NULL DEFAULT NULL");
executeQuery("ALTER TABLE wcf1_tour_step ADD COLUMN onShow MEDIUMTEXT NULL DEFAULT NULL");
executeQuery("ALTER TABLE wcf1_tour_step ADD COLUMN onCTA MEDIUMTEXT NULL DEFAULT NULL");

// set package id
setPackageID(Tour::getDatabaseTableName());
setPackageID(TourStep::getDatabaseTableName());

/**
 * Executes a simple query
 * 
 * @param	string	$sql
 */
function executeQuery($sql) {
	$statement = WCF::getDB()->prepareStatement($sql);
	$statement->execute();
}

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
