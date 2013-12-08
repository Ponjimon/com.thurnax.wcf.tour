<?php
namespace wcf\data\tour\step;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of tour steps.
 * 
 * @author	Magnus Kühn
 * @copyright	2013 Thurnax.com
 * @package	com.thurnax.wcf.tour
 * @category	Community Framework (commercial)
 */
class TourStepList extends DatabaseObjectList {
	/**
	 * @see	\wcf\data\DatabaseObjectList::$className
	 */
	public $className = 'wcf\data\tour\step\TourStep';
}
