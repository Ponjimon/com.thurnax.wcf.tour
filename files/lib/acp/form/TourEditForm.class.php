<?php
namespace wcf\acp\form;
use wcf\data\package\PackageCache;
use wcf\data\tour\Tour;
use wcf\data\tour\TourAction;
use wcf\form\AbstractForm;
use wcf\system\acl\ACLHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;

/**
 * Shows the tour edit form.
 *
 * @author	Magnus Kühn
 * @copyright	2013 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class TourEditForm extends TourAddForm {
	/**
	 * @see	\wcf\acp\form\ACPForm::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.user.tour';
	
	/**
	 * tour id
	 * @var	integer
	 */
	public $tourID = 0;
	
	/**
	 * tour
	 * @var	\wcf\data\tour\Tour
	 */
	public $tour = null;
	
	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['id'])) $this->tourID = intval($_REQUEST['id']);
		$this->tour = new Tour($this->tourID);
		if (!$this->tour->tourID) {
			throw new IllegalLinkException();
		}
	}

	/**
	 * @see	\wcf\acp\form\TourAddForm::validateTourName()
	 */
	protected function validateTourName() {
		if ($this->tourName != $this->tour->tourName) { // only validate if value has changed
			parent::validateTourName();
		}
	}
	
	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		AbstractForm::save();
		
		// update visible name
		if (I18nHandler::getInstance()->isPlainValue('visibleName')) {
			I18nHandler::getInstance()->remove($this->visibleName);
		} else {
			$this->visibleName = 'wcf.acp.tour.visibleName'.$this->tour->tourID;
			I18nHandler::getInstance()->save('visibleName', $this->visibleName, 'wcf.acp.tour', PackageCache::getInstance()->getPackageID('com.thurnax.wcf.tour'));
		}
		
		// update tour
		$this->objectAction = new TourAction(array($this->tourID), 'update', array('data' => array(
			'visibleName' => $this->visibleName,
			'tourTrigger' => $this->tourTrigger,
			'className' => ($this->className ?: null),
			'tourName' => ($this->tourName ?: null)
		)));
		$this->objectAction->executeAction();
		
		// update acl
		ACLHandler::getInstance()->save($this->tourID, $this->objectTypeID);
		ACLHandler::getInstance()->disableAssignVariables();
		
		// show success
		$this->saved();
		WCF::getTPL()->assign('success', true);
	}
	
	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		
		if (empty($_POST)) {
			I18nHandler::getInstance()->setOptions('visibleName', $this->tour->tourID, $this->tour->visibleName, 'wcf.acp.tour.visibleName\d+');
			
			$this->visibleName = $this->tour->visibleName;
			$this->tourTrigger = $this->tour->tourTrigger;
			$this->className = $this->tour->className;
			$this->tourName = $this->tour->tourName;
		}
	}
	
	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		I18nHandler::getInstance()->assignVariables(!empty($_POST));
		WCF::getTPL()->assign(array(
			'tourID' => $this->tourID,
			'tour' => $this->tour,
			'action' => 'edit'
		));
	}
}
