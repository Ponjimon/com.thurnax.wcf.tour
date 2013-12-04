<?php
namespace wcf\data\tour\tourPoint;
use wcf\data\DatabaseObject;

/**
 * Represents a tour point.
 * 
 * @author	Simon Nußbaumer
 * @copyright	2013 Thurnax.com
 * @package	com.thurnax.wcf.tour
 * @subpackage	data.tour.point
 * @category	Community Framework (commercial)
 */
class TourPoint extends DatabaseObject {
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'tour_tourPoint';
	
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'tourPointID';
}
