<?php
namespace wcf\acp\page;
use wcf\page\SortablePage;

/**
 * Lists available tour points.
 * 
 * @author	Simon Nußbaumer
 * @copyright	2013 Thurnax.com
 * @package	com.thurnax.wcf.tour
 * @subpackage	acp.page
 * @category	Community Framework (commercial)
 */
class TourPointListPage extends SortablePage {
	/**
	 * @see	\wcf\acp\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.user.tour.point.list';
	
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
	public $objectListClassName = 'wcf\data\tour\point\TourPointList';
	
	/**
	 * @see	\wcf\page\MultipleLinkPage::$defaultSortField
	 */
	public $defaultSortField = 'step';
	
	/**
	 * @see	\wcf\page\MultipleLinkPage::$validSortFields
	 */
	public $validSortFields = array('tourPointID', 'step', 'elementName', 'pointText', 'position');
}
