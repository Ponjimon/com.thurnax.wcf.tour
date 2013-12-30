<?php
namespace wcf\system\cache\builder;
use wcf\data\tour\step\TourStepList;
use wcf\data\tour\Tour;

/**
 * Caches the rendered tour steps for a tour
 *
 * @author	Magnus Kühn
 * @copyright	2013 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class TourStepCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @see	\wcf\system\cache\builder\AbstractCacheBuilder::rebuild()
	 */
	public function rebuild(array $parameters) {
		// fetch tour steps
		$tourStepList = new TourStepList();
		$tourStepList->getConditionBuilder()->add('tourID = ?', array($parameters['tourID']));
		$tourStepList->getConditionBuilder()->add('isDisabled = ?', array(0));
		$tourStepList->sqlOrderBy = 'showOrder ASC';
		$tourStepList->readObjects();
		
		// render tour steps
		$tourSteps = array();
		$previousTourStep = null;
		foreach ($tourStepList->getObjects() as $tourStep) {
			$tourSteps[] = $tourStep->render($previousTourStep);
			$previousTourStep = $tourStep;
		}
		
		return $tourSteps;
	}
}
