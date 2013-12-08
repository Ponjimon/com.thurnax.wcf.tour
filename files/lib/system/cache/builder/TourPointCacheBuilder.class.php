<?php
namespace wcf\system\cache\builder;
use wcf\data\tour\step\TourStepList;

/**
 * Caches tour points
 * 
 * @author	Magnus Kühn
 * @copyright	2013 Thurnax.com
 * @package	com.thurnax.wcf.tour
 * @category	Community Framework (commercial)
 */
class TourPointCacheBuilder extends AbstractCacheBuilder {
	/**
	 * @see	\wcf\system\cache\builder\AbstractCacheBuilder::rebuild()
	 */
	public function rebuild(array $parameters) {
		$tourPointList = new TourStepList();
		$tourPointList->sqlOrderBy = 'step ASC';
		$tourPointList->readObjects();
		return $tourPointList->getObjects();
	}
}
