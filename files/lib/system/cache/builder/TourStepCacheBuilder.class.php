<?php
namespace wcf\system\cache\builder;
use wcf\data\tour\step\TourStepList;

/**
 * Caches tour steps
 * 
 * @author	Magnus Kühn
 * @copyright	2013 Thurnax.com
 * @package	com.thurnax.wcf.tour
 */
class TourStepCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @see	\wcf\system\cache\builder\AbstractCacheBuilder::rebuild()
	 */
	public function rebuild(array $parameters) {
		$tourStepList = new TourStepList();
		if (isset($parameters['tourID'])) {
			$tourStepList->getConditionBuilder()->add('tourID = ?', array($parameters['tourID']));
		}
		
		$tourStepList->sqlOrderBy = 'showOrder ASC';
		$tourStepList->readObjects();
		return $tourStepList->getObjects();
	}
}
