<?php
namespace wcf\system\cache\builder;
use wcf\data\tour\step\TourStepList;
use wcf\data\tour\TourList;
use wcf\data\tour\ViewableTour;
use wcf\system\acl\ACLHandler;

/**
 * Caches the rendered tour steps for a tour
 *
 * @author    Magnus Kühn
 * @copyright 2013-2014 Thurnax.com
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package   com.thurnax.wcf.tour
 */
class TourCacheBuilder extends AbstractCacheBuilder {
	/**
	 * Rebuilds cache for current resource.
	 *
	 * @param array $parameters
	 * @return array
	 */
	public function rebuild(array $parameters) {
		$tourList = new TourList();
		$tourList->readObjects();

		// get acl permissions
		$permissions = ACLHandler::getInstance()->getPermissions(ACLHandler::getInstance()->getObjectTypeID('com.thurnax.wcf.tour'), $tourList->getObjectIDs());
		/** @var $permissions \wcf\data\acl\option\ACLOptionList[] */
		$aclOptions = $permissions['options']->getObjects();

		// add acl permissions
		/** @var $tours \wcf\data\tour\ViewableTour[] */
		$tours = array();
		foreach ($tourList as $tour) {
			/** @var $tour \wcf\data\tour\Tour */
			$tours[$tour->tourID] = new ViewableTour($tour);

			// group permissions
			if (isset($permissions['group'][$tour->tourID])) {
				foreach ($permissions['group'][$tour->tourID] as $groupID => $options) {
					$tours[$tour->tourID]->setGroupPermissions($groupID, $options, $aclOptions);
				}
			}

			// user permissions
			if (isset($permissions['user'][$tour->tourID])) {
				foreach ($permissions['user'][$tour->tourID] as $userID => $options) {
					$tours[$tour->tourID]->setUserPermissions($userID, $options, $aclOptions);
				}
			}
		}

		// fetch tour steps
		$tourSteps = array();
		foreach ($tourList->getObjects() as $tourID => $tour) {
			$tourStepList = new TourStepList();
			$tourStepList->getConditionBuilder()->add('tourID = ?', array($tourID));
			$tourStepList->getConditionBuilder()->add('isDisabled = ?', array(0));
			$tourStepList->sqlOrderBy = 'showOrder ASC';
			$tourStepList->readObjects();

			// render tour steps
			$previousTourStep = null;
			foreach ($tourStepList->getObjects() as $tourStep) {
				/** @var $tourStep \wcf\data\tour\step\TourStep */
				$tourSteps[$tourID][] = $tourStep->render($previousTourStep);
				$previousTourStep = $tourStep;
			}
		}

		return array('tours' => $tours, 'steps' => $tourSteps);
	}
}
