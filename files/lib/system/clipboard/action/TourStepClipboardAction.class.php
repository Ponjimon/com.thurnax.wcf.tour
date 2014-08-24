<?php
namespace wcf\system\clipboard\action;
use wcf\data\clipboard\action\ClipboardAction;
use wcf\system\WCF;

/**
 * Prepares clipboard editor items for tour steps.
 *
 * @author    Magnus Kühn
 * @copyright 2013-2014 Thurnax.com
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package   com.thurnax.wcf.tour
 */
class TourStepClipboardAction extends AbstractClipboardAction {
	/**
	 * list of the clipboard actions which are executed by the action class
	 *
	 * @var string
	 */
	protected $actionClassActions = array('delete');

	/**
	 * list of the supported clipboard actions
	 *
	 * @var string
	 */
	protected $supportedActions = array('enable', 'disable', 'move', 'delete');

	/**
	 * Returns type name identifier.
	 *
	 * @return string
	 */
	public function getTypeName() {
		return 'com.thurnax.wcf.tour.step';
	}

	/**
	 * Returns editor item for the clipboard action with the given name or null
	 * if the action is not applicable to the given objects.
	 *
	 * @param \wcf\data\DatabaseObject[]                 $objects
	 * @param \wcf\data\clipboard\action\ClipboardAction $action
	 * @return \wcf\system\clipboard\ClipboardEditorItem
	 */
	public function execute(array $objects, ClipboardAction $action) {
		$item = parent::execute($objects, $action);

		// handle actions
		if ($item !== null && $action->actionName == 'delete') {
			$item->addInternalData('confirmMessage', WCF::getLanguage()->getDynamicVariable('wcf.clipboard.item.com.thurnax.wcf.tour.step.delete.confirmMessage', array('count' => $item->getCount())));
		}

		return $item;
	}

	/**
	 * Returns action class name.
	 *
	 * @return string
	 */
	public function getClassName() {
		return 'wcf\data\tour\step\TourStepAction';
	}

	/**
	 * Returns the ids of the tour steps which can be enabled.
	 *
	 * @return array<integer>
	 */
	protected function validateEnable() {
		// check permissions
		if (!WCF::getSession()->getPermission('admin.user.canManageTour')) {
			return array();
		}

		$tourStepIDs = array();
		foreach ($this->objects as $tourStep) {
			if ($tourStep->isDisabled) {
				$tourStepIDs[] = $tourStep->tourStepID;
			}
		}

		return $tourStepIDs;
	}

	/**
	 * Returns the ids of the tour steps which can be disabled.
	 *
	 * @return array<integer>
	 */
	protected function validateDisable() {
		// check permissions
		if (!WCF::getSession()->getPermission('admin.user.canManageTour')) {
			return array();
		}

		$tourStepIDs = array();
		foreach ($this->objects as $tourStep) {
			if (!$tourStep->isDisabled) {
				$tourStepIDs[] = $tourStep->tourStepID;
			}
		}

		return $tourStepIDs;
	}

	/**
	 * Returns the ids of the tour steps which can be moved.
	 *
	 * @return array<integer>
	 */
	protected function validateMove() {
		return $this->validateDelete();
	}

	/**
	 * Returns the ids of the tour steps which can be deleted.
	 *
	 * @return array<integer>
	 */
	protected function validateDelete() {
		// check permissions
		if (!WCF::getSession()->getPermission('admin.user.canManageTour')) {
			return array();
		}

		return array_keys($this->objects);
	}
}
