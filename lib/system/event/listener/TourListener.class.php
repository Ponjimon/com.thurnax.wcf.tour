<?php
namespace wcf\system\event\listener;
use wcf\system\event\IEventListener;
use wcf\data\user\User;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Eventlistener for tour points.
 * 
 * @author	Simon Nußbaumer
 * @copyright	2013 Thurnax.com
 * @package	com.thurnax.wcf.tour
 * @category	Community Framework (commercial)
 */
class TourListener implements IEventListener {
	/**
	 * @see	\wcf\system\event\IEventListener::execute()
	 */
	public function execute($eventObj, $className, $eventName) {
		$showTour = WCF::getUser()->showTour;	
		$sql = "SELECT * FROM wcf".WCF_N."_tour_tourPoint ORDER BY step ASC";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		$points = array();
		$sql2 = "SELECT COUNT(*) AS Count FROM wcf".WCF_N."_tour_tourPoint";
		$statement2 = WCF::getDB()->prepareStatement($sql2);
		$statement2->execute();
		$countRow = $statement2->fetchArray();
		while($row = $statement->fetchArray()) {
			$points[] = $row;
		}
		WCF::getTPL()->assign(array('tourPoints' => $points, 'showTour' => $showTour, 'tourPointsCount' => $countRow['Count']));
	}
}
