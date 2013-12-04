<?php
namespace wcf\data\tour\tourPoint;
use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit tour points.
 * 
 * @author	Simon Nußbaumer
 * @copyright	2013 Thurnax.com
 * @package	com.thurnax.wcf.tour
 * @subpackage	data.tour.point
 * @category	Community Framework (commercial)
 */
class TourPointEditor extends DatabaseObjectEditor {
	/**
	 * @see	\wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = 'wcf\data\tour\tourPoint\TourPoint';
}
