<?php
namespace wcf\data\tour\point;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of tour points.
 * 
 * @author	Simon Nußbaumer
 * @copyright	2013 Thurnax.com
 * @package	com.thurnax.wcf.tour
 * @category	Community Framework (commercial)
 */
class TourPointList extends DatabaseObjectList {
	/**
	 * @see	\wcf\data\DatabaseObjectList::$className
	 */
	public $className = 'wcf\data\tour\point\TourPoint';
}
