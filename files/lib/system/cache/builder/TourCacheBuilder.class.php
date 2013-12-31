<?php
namespace wcf\system\cache\builder;
use wcf\data\tour\step\TourStepList;
use wcf\data\tour\ViewableTourList;

/**
 * Caches the rendered tour steps for a tour
 *
 * @author	Magnus Kühn
 * @copyright	2013-2014 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class TourCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @see	\wcf\system\cache\builder\AbstractCacheBuilder::rebuild()
	 */
	public function rebuild(array $parameters) {
		$data = array('viewableTours' => array(), 'steps' => array());
		
		// fetch viewable tours
		$viewableTourList = new ViewableTourList();
		$viewableTourList->readObjects();
		$data['viewableTours'] = $viewableTourList->getObjects();
		
		// fetch tour steps
		foreach ($viewableTourList->getObjects() as $tourID => $tour) {
			$tourStepList = new TourStepList();
			$tourStepList->getConditionBuilder()->add('tourID = ?', array($tourID));
			$tourStepList->getConditionBuilder()->add('isDisabled = ?', array(0));
			$tourStepList->sqlOrderBy = 'showOrder ASC';
			$tourStepList->readObjects();
			
			// render tour steps
			$previousTourStep = null;
			foreach ($tourStepList->getObjects() as $tourStep) {
				$data['steps'][$tourID][] = $tourStep->render($previousTourStep);
				$previousTourStep = $tourStep;
			}
		}
		
		return $data;
	}
}
