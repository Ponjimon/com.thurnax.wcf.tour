<?php
namespace wcf\data\tour\step;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\ISortableAction;
use wcf\data\IToggleAction;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;

/**
 * Executes tour step-related actions.
 *
 * @author    Magnus Kühn
 * @copyright 2013-2014 Thurnax.com
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package   com.thurnax.wcf.tour
 */
class TourStepAction extends AbstractDatabaseObjectAction implements ISortableAction, IToggleAction {
	/**
	 * object editor class name
	 *
	 * @var string
	 */
	protected $className = 'wcf\data\tour\step\TourStepEditor';

	/**
	 * list of permissions required to update objects
	 *
	 * @var string[]
	 */
	protected $permissionsUpdate = array('admin.user.canManageTour');

	/**
	 * list of permissions required to delete objects
	 *
	 * @var string[]
	 */
	protected $permissionsDelete = array('admin.user.canManageTour');

	/**
	 * disallow requests for specified methods if the origin is not the ACP
	 *
	 * @var string[]
	 */
	protected $requireACP = array('update', 'updatePosition', 'delete');

	/**
	 * Updates the position of given objects.
	 */
	public function updatePosition() {
		$sql = "UPDATE	".TourStep::getDatabaseTableName()."
			SET	showOrder = ?
			WHERE	tourStepID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);

		WCF::getDB()->beginTransaction();
		foreach ($this->parameters['data']['structure'][0] as $showOrder => $tourStepID) {
			$statement->execute(array($showOrder + $this->parameters['data']['offset'], $tourStepID));
		}
		WCF::getDB()->commitTransaction();
	}

	/**
	 * Validates the 'updatePosition' action.
	 */
	public function validateUpdatePosition() {
		WCF::getSession()->checkPermissions(array('admin.user.canManageTour'));
		$this->readInteger('offset', false, 'data');
		if (!isset($this->parameters['data']) || !isset($this->parameters['data']['structure']) || !isset($this->parameters['data']['structure'][0])) {
			throw new UserInputException('structure');
		}

		// read tour steps
		$this->objectIDs = $this->parameters['data']['structure'][0];
		$tourStepList = new TourStepList();
		$tourStepList->setObjectIDs($this->objectIDs);
		$tourStepList->readObjects();
		$this->objects = $tourStepList->getObjects();

		// validate IDs
		if (count($this->objects) != count($this->objectIDs)) {
			throw new UserInputException('structure');
		}
	}

	/**
	 * Toggles the "isDisabled" status of the relevant objects.
	 */
	public function toggle() {
		foreach ($this->objects as $tourStep) {
			$tourStep->update(array('isDisabled' => $tourStep->isDisabled ? 0 : 1));
		}
	}

	/**
	 * Validates the "toggle" action.
	 */
	public function validateToggle() {
		parent::validateUpdate();
	}
}
