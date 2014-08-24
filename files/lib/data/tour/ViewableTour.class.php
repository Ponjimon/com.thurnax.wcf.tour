<?php
namespace wcf\data\tour;
use wcf\data\DatabaseObjectDecorator;
use wcf\system\WCF;

/**
 * Represents a viewable tour.
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
class ViewableTour extends DatabaseObjectDecorator {
	/**
	 * name of the base class
	 *
	 * @var string
	 */
	protected static $baseClass = 'wcf\data\tour\Tour';

	/**
	 * list of permissions by type
	 *
	 * @var int[]
	 */
	protected $permissions = array('group' => array(), 'user' => array());

	/**
	 * Sets group permissions
	 *
	 * @param int                              $groupID
	 * @param int[]                            $options
	 * @param \wcf\data\acl\option\ACLOption[] $aclOptions
	 */
	public function setGroupPermissions($groupID, $options, $aclOptions) {
		foreach ($options as $optionID => $value) {
			$this->permissions['group'][$groupID][$aclOptions[$optionID]->optionName] = $value;
		}
	}

	/**
	 * Sets user permissions
	 *
	 * @param int                              $userID
	 * @param int[]                            $options
	 * @param \wcf\data\acl\option\ACLOption[] $aclOptions
	 */
	public function setUserPermissions($userID, $options, $aclOptions) {
		foreach ($options as $optionID => $value) {
			$this->permissions['user'][$userID][$aclOptions[$optionID]->optionName] = $value;
		}
	}

	/**
	 * Returns true, if current user can view the tour
	 *
	 * @param string $option
	 * @return boolean
	 */
	public function getPermission($option) {
		// check user permissions
		$userID = WCF::getUser()->userID;
		if ($userID) {
			if (isset($this->permissions['user'][$userID]) && isset($this->permissions['user'][$userID][$option]) && $this->permissions['user'][$userID][$option] == 1) {
				return true;
			}
		}

		// check group permissions
		foreach (WCF::getUser()->getGroupIDs() as $groupID) {
			if (isset($this->permissions['group'][$groupID]) && isset($this->permissions['group'][$groupID][$option]) && $this->permissions['group'][$groupID][$option] == 1) {
				return true;
			}
		}

		return null;
	}
}
