<?php
namespace wcf\system\clipboard\action;
use wcf\data\clipboard\action\ClipboardAction;
use wcf\system\WCF;

/**
 * Prepares clipboard editor items for tour steps.
 *
 * @author	Magnus Kühn
 * @copyright	2013-2014 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class TourStepClipboardAction extends AbstractClipboardAction {
	/**
	 * @see	\wcf\system\clipboard\action\AbstractClipboardAction::$actionClassActions
	 */
	protected $actionClassActions = array('delete');
	
	/**
	 * @see	\wcf\system\clipboard\action\AbstractClipboardAction::$supportedActions
	 */
	protected $supportedActions = array('enable', 'disable', 'move', 'delete');
	
	/**
	 * @see	\wcf\system\clipboard\action\IClipboardAction::execute()
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
	 * @see	\wcf\system\clipboard\action\IClipboardAction::getClassName()
	 */
	public function getClassName() {
		return 'wcf\data\tour\step\TourStepAction';
	}
	
	/**
	 * @see	\wcf\system\clipboard\action\IClipboardAction::getTypeName()
	 */
	public function getTypeName() {
		return 'com.thurnax.wcf.tour.step';
	}
	
	/**
	 * Returns the ids of the tour steps which can be enabled.
	 *
	 * @return	array<integer>
	 */
	protected function validateEnable() {
		// check permissions
		if (!WCF::getSession()->getPermission('admin.user.canEditTour')) {
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
	 * @return	array<integer>
	 */
	protected function validateDisable() {
		// check permissions
		if (!WCF::getSession()->getPermission('admin.user.canEditTour')) {
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
	 * @return	array<integer>
	 */
	protected function validateMove() {
		return $this->validateDelete();
	}
	
	/**
	 * Returns the ids of the tour steps which can be deleted.
	 * 
	 * @return	array<integer>
	 */
	protected function validateDelete() {
		// check permissions
		if (!WCF::getSession()->getPermission('admin.user.canEditTour')) {
			return array();
		}
		
		return array_keys($this->objects);
	}
}
