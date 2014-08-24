<?php
namespace wcf\acp\page;
use wcf\page\SortablePage;

/**
 * Lists all tours.
 *
 * @author    Magnus Kühn
 * @copyright 2013-2014 Thurnax.com
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package   com.thurnax.wcf.tour
 */
class TourListPage extends SortablePage {
	/**
	 * name of the active menu item
	 *
	 * @var string
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.user.tour.list';

	/**
	 * needed permissions to view this page
	 *
	 * @var string[]
	 */
	public $neededPermissions = array('admin.user.canManageTour');

	/**
	 * needed modules to view this page
	 *
	 * @var string[]
	 */
	public $neededModules = array('MODULE_TOUR');

	/**
	 * class name for DatabaseObjectList
	 *
	 * @var string
	 */
	public $objectListClassName = 'wcf\data\tour\TourList';

	/**
	 * default sort field
	 *
	 * @var        string
	 */
	public $defaultSortField = 'visibleName';

	/**
	 * list of valid sort fields
	 *
	 * @var string[]
	 */
	public $validSortFields = array('tourID', 'visibleName', 'tourTrigger', 'className');
}
