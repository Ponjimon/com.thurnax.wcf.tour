<?php
namespace wcf\data\tour;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of tours.
 *
 * @author    Magnus Kühn
 * @copyright 2013-2014 Thurnax.com
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package   com.thurnax.wcf.tour
 */
class TourList extends DatabaseObjectList {
	/**
	 * class name
	 *
	 * @var string
	 */
	public $className = 'wcf\data\tour\Tour';
}
