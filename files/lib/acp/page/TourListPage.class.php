<?php
namespace wcf\acp\page;
use wcf\page\SortablePage;

/**
 * Lists all tours.
 *
 * @author	Magnus Kühn
 * @copyright	2013 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class TourListPage extends SortablePage {
	/**
	 * @see	\wcf\acp\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.user.tour.list';
	
	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.user.canEditTour');
	
	/**
	 * @see	\wcf\page\AbstractPage::$neededModules
	 */
	public $neededModules = array('MODULE_TOUR');
	
	/**
	 * @see	\wcf\page\MultipleLinkPage::$objectListClassName
	 */
	public $objectListClassName = 'wcf\data\tour\TourList';
	
	/**
	 * @see	\wcf\page\MultipleLinkPage::$defaultSortField
	 */
	public $defaultSortField = 'visibleName';
	
	/**
	 * @see	\wcf\page\MultipleLinkPage::$validSortFields
	 */
	public $validSortFields = array('tourID', 'visibleName', 'tourTrigger', 'className', 'tourName');
}
