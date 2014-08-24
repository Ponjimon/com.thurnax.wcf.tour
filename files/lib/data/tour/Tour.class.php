<?php
namespace wcf\data\tour;
use wcf\data\DatabaseObject;
use wcf\system\request\IRouteController;
use wcf\system\WCF;

/**
 * Represents a tour.
 *
 * @property integer $tourID
 * @property string  $visibleName
 * @property integer $isDisabled
 * @property integer $packageID
 * @property string  $tourTrigger
 * @property string  $className
 * @property string  $identifier
 * @author    Magnus Kühn
 * @copyright 2013-2014 Thurnax.com
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package   com.thurnax.wcf.tour
 */
class Tour extends DatabaseObject implements IRouteController {
	/**
	 * database table for this object
	 *
	 * @var string
	 */
	protected static $databaseTableName = 'tour';

	/**
	 * name of the primary index column
	 *
	 * @var string
	 */
	protected static $databaseTableIndexName = 'tourID';

	/**
	 * Returns the title of the object.
	 *
	 * @return string
	 */
	public function getTitle() {
		return WCF::getLanguage()->get($this->visibleName);
	}
}
