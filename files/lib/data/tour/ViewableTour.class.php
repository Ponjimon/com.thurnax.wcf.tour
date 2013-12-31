<?php
namespace wcf\data\tour;
use wcf\data\DatabaseObjectDecorator;
use wcf\system\WCF;

/**
 * Represents a viewable label group.
 * 
 * @author	Magnus Kühn
 * @copyright	2013 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class ViewableTour extends DatabaseObjectDecorator {
	/**
	 * @see	\wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = 'wcf\data\tour\Tour';
	
	/**
	 * list of permissions by type
	 * @var	array<array>
	 */
	protected $permissions = array('group' => array(), 'user' => array());
	
	/**
	 * Sets a group permission.
	 * 
	 * @param	integer	$userID
	 * @param	integer	$value
	 */
	public function setGroupPermission($userID, $value) {
		$this->permissions['group'][$userID] = ($value == "1" ? true : false);
	}
	
	/**
	 * Sets an user permission.
	 * 
	 * @param	integer	$userID
	 * @param	integer	$value
	 */
	public function setUserPermission($userID, $value) {
		$this->permissions['user'][$userID] = ($value == "1" ? true : false);
	}
	
	/**
	 * Returns true, if current user can view the tour.
	 * 
	 * @return	boolean
	 */
	public function getPermission() {
		// validate by user id
		if (WCF::getUser()->userID) {
			if (isset($this->permissions['user'][WCF::getUser()->userID]) && isset($this->permissions['user'][WCF::getUser()->userID]) && $this->permissions['user'][WCF::getUser()->userID]) {
				return true;
			}
		}
		
		// validate by group id
		foreach (WCF::getUser()->getGroupIDs() as $groupID) {
			if (isset($this->permissions['group'][$groupID]) && isset($this->permissions['group'][$groupID]) && $this->permissions['group'][$groupID]) {
				return true;
			}
		}
		
		return false;
	}
}
