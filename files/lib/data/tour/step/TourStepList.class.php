<?php
namespace wcf\data\tour\step;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of tour steps.
 * 
 * @author	Magnus Kühn
 * @copyright	2013-2014 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class TourStepList extends DatabaseObjectList {
	/**
	 * @see	\wcf\data\DatabaseObjectList::$className
	 */
	public $className = 'wcf\data\tour\step\TourStep';
}
