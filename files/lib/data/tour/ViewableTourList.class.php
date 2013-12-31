<?php
namespace wcf\data\tour;
use wcf\system\acl\ACLHandler;

/**
 * Represents a list of viewable tours.
 * 
 * @author	Magnus Kühn
 * @copyright	2013-2014 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class ViewableTourList extends TourList {
	/**
	 * @see	\wcf\data\DatabaseObjectList::$decoratorClassName
	 */
	public $decoratorClassName = 'wcf\data\tour\ViewableTour';

	/**
	 * @see	\wcf\data\DatabaseObjectList::readObjects()
	 */
	public function readObjects() {
		parent::readObjects();
		
		// add ACL permissions
		$permissions = ACLHandler::getInstance()->getPermissions(ACLHandler::getInstance()->getObjectTypeID('com.thurnax.wcf.tour'), $this->getObjectIDs());
		foreach ($this->objects as $tourID => $viewableTour) {
			if (isset($permissions['group'][$tourID])) { // group permissions
				foreach ($permissions['group'][$tourID] as $groupID => $options) {
					$viewableTour->setGroupPermission($groupID, array_pop($options));
				}
			}
			
			if (isset($permissions['user'][$tourID])) { // user permissions
				foreach ($permissions['user'][$tourID] as $userID => $options) {
					$viewableTour->setUserPermission($userID, array_pop($options));
				}
			}
		}
	}
}
