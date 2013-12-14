<?php
namespace wcf\system\event\listener;
use wcf\system\cache\builder\TourStepCacheBuilder;
use wcf\system\event\IEventListener;
use wcf\system\WCF;

/**
 * EventListener for tour points.
 * 
 * @author	Simon Nußbaumer
 * @copyright	2013 Thurnax.com
 * @package	com.thurnax.wcf.tour
 * @category	Community Framework (commercial)
 */
class BeforeDisplayTourListener implements IEventListener {
	/**
	 * @see	\wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		$tourPoints = TourStepCacheBuilder::getInstance()->getData();
		WCF::getTPL()->assign(array(
			'tourPoints' => $tourPoints,
			'tourPointsCount' => count($tourPoints),
			'showTour' => WCF::getUser()->showTour
		));
	}
}
