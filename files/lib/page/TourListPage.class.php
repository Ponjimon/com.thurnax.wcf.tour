<?php
namespace wcf\page;
use wcf\system\cache\builder\TourCacheBuilder;
use wcf\system\menu\user\UserMenu;
use wcf\system\WCF;

/**
 * Lists all tours available for replaying.
 *
 * @author    Magnus Kühn
 * @copyright 2013-2014 Thurnax.com
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package   com.thurnax.wcf.tour
 */
class TourListPage extends AbstractPage {
	/**
	 * needed permissions to view this page
	 *
	 * @var string[]
	 */
	public $neededPermissions = array('user.tour.canPlayTourAgain');

	/**
	 * available tours
	 *
	 * @var \wcf\data\tour\ViewableTour[]
	 */
	public $tours = null;

	/**
	 * Reads/Gets the data to be displayed on this page.
	 */
	public function readData() {
		parent::readData();

		/** @var $viewableTours \wcf\data\tour\ViewableTour[] */
		$viewableTours = TourCacheBuilder::getInstance()->getData(array(), 'tours');
		foreach ($viewableTours as $viewableTour) {
			if ($viewableTour->getPermission('canPlayTourAgain')) {
				$this->tours[$viewableTour->tourID] = $viewableTour;
			}
		}
	}

	/**
	 * Sets the active menu item of the page.
	 */
	protected function setActiveMenuItem() {
		parent::setActiveMenuItem();

		UserMenu::getInstance()->setActiveMenuItem('wcf.user.menu.profile.tours');
	}

	/**
	 * Assigns variables to the template engine.
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign('tours', $this->tours);
	}
} 
