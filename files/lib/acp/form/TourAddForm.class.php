<?php
namespace wcf\acp\form;
use wcf\data\package\PackageCache;
use wcf\data\tour\Tour;
use wcf\data\tour\TourAction;
use wcf\form\AbstractForm;
use wcf\system\acl\ACLHandler;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\ClassUtil;
use wcf\util\StringUtil;

/**
 * Shows the tour add form.
 *
 * @author    Magnus Kühn
 * @copyright 2013-2014 Thurnax.com
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package   com.thurnax.wcf.tour
 */
class TourAddForm extends AbstractForm {
	/**
	 * name of the active menu item
	 *
	 * @var string
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.user.tour.add';

	/**
	 * name of the active menu item
	 *
	 * @var string
	 */
	public $neededPermissions = array('admin.user.canManageTour');

	/**
	 * name of the active menu item
	 *
	 * @var string
	 */
	public $neededModules = array('MODULE_TOUR');

	/**
	 * visibleName
	 *
	 * @var string
	 */
	public $visibleName = '';

	/**
	 * trigger
	 *
	 * @var string
	 */
	public $tourTrigger = 'firstSite';

	/**
	 * class name
	 *
	 * @var string
	 */
	public $className = null;

	/**
	 * identifier
	 *
	 * @var string
	 */
	public $identifier = null;

	/**
	 * list of label group to object type relations
	 *
	 * @var array<array>
	 */
	public $objectTypes = array();

	/**
	 * object type id
	 *
	 * @var integer
	 */
	public $objectTypeID = 0;

	/**
	 * Reads the given parameters.
	 */
	public function readParameters() {
		parent::readParameters();

		// setup form
		$this->objectTypeID = ACLHandler::getInstance()->getObjectTypeID('com.thurnax.wcf.tour');
	}

	/**
	 * Validates form inputs.
	 */
	public function readFormParameters() {
		parent::readFormParameters();

		if (isset($_POST['visibleName'])) $this->visibleName = StringUtil::trim($_POST['visibleName']);
		if (isset($_POST['tourTrigger'])) $this->tourTrigger = $_POST['tourTrigger'];
		if (isset($_POST['className'])) $this->className = $_POST['className'];
		if (isset($_POST['identifier'])) $this->identifier = $_POST['identifier'];
		if (isset($_POST['objectTypes']) && is_array($_POST['objectTypes'])) $this->objectTypes = $_POST['objectTypes'];
	}

	/**
	 * Validates form inputs.
	 */
	public function validate() {
		parent::validate();

		// validate visible name
		if (empty($this->visibleName)) {
			throw new UserInputException('visibleName');
		}

		// validate tour trigger
		switch ($this->tourTrigger) {
			case 'specificSite': // validate class name
				if (empty($this->className)) {
					throw new UserInputException('className');
				}
				if (!class_exists($this->className) || !ClassUtil::isInstanceOf($this->className, 'wcf\page\IPage')) {
					throw new UserInputException('className', 'invalid');
				}
			case 'firstSite': // no additional parameters
				break;
			case 'manual': // require identifier
				if (empty($this->identifier)) {
					throw new UserInputException('identifier');
				}

				break;
			default:
				throw new UserInputException('tourTrigger');
		}

		// validate identifier
		if ($this->identifier && !$this->validateIdentifier()) {
			throw new UserInputException('identifier', 'notUnique');
		}
	}

	/**
	 * Validates the identifier
	 */
	protected function validateIdentifier() {
		$sql = "SELECT	COUNT(tourID) as count
			FROM	".Tour::getDatabaseTableName()."
			WHERE	identifier = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute(array($this->identifier));
		$row = $statement->fetchArray();
		return $row['count'] == 0;
	}

	/**
	 * Saves the data of the form.
	 */
	public function save() {
		parent::save();
		$packageID = PackageCache::getInstance()->getPackageID('com.thurnax.wcf.tour');

		// save tour point
		$this->objectAction = new TourAction(array(), 'create', array('data' => array('visibleName' => $this->visibleName,
			'isDisabled' => 0,
			'packageID' => $packageID,
			'tourTrigger' => $this->tourTrigger,
			'className' => ($this->className ? : null),
			'identifier' => ($this->identifier ? : null))));
		$this->objectAction->executeAction();
		$this->saved();

		// save ACL
		$returnValues = $this->objectAction->getReturnValues();
		ACLHandler::getInstance()->save($returnValues['returnValues']->tourID, $this->objectTypeID);
		ACLHandler::getInstance()->disableAssignVariables();

		// reset values
		$this->visibleName = $this->className = $this->identifier = '';
		$this->tourTrigger = 'firstSite';

		// show success
		WCF::getTPL()->assign('success', true);
	}

	/**
	 * Assigns variables to the template engine.
	 */
	public function assignVariables() {
		parent::assignVariables();

		ACLHandler::getInstance()->assignVariables($this->objectTypeID);
		WCF::getTPL()->assign(array('action' => 'add',
			'visibleName' => $this->visibleName,
			'tourTrigger' => $this->tourTrigger,
			'className' => $this->className,
			'identifier' => $this->identifier,
			'objectTypeID' => $this->objectTypeID));
	}
}
