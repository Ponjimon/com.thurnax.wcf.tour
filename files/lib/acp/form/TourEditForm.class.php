<?php
namespace wcf\acp\form;
use wcf\data\tour\Tour;
use wcf\data\tour\step\TourStepAction;
use wcf\form\AbstractForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;

/**
 * Shows the tour edit form.
 *
 * @author	Magnus Kühn
 * @copyright	2013 Thurnax.com
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
		
		if (I18nHandler::getInstance()->isPlainValue('description')) {
			I18nHandler::getInstance()->remove($this->description);
			$this->description = I18nHandler::getInstance()->getValue('description');
		} else {
			I18nHandler::getInstance()->save('description', $this->description, 'wcf.acp.tour', $this->tour->tourID);
			$this->description = 'wcf.acp.tour.description'.$this->tour->tourID;
		}
		
		// update tour
		$this->objectAction = new TourStepAction(array($this->tourID), 'update', array('data' => array(
			'tourName' => $this->tourName,
			'description' => $this->description,
			'showPrevButton' => $this->showPrevButton
		)));
		$this->objectAction->executeAction();
		$this->saved();
		
		// show success
		WCF::getTPL()->assign('success', true);
	}
	
	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		
		if (empty($_POST)) {
			I18nHandler::getInstance()->setOptions('description', $this->tour->tourID, $this->tour->description, 'wcf.acp.tour.description\d+');
			
			$this->tourName = $this->tour->tourName;
			$this->description = $this->tour->description;
			$this->showPrevButton = $this->tour->showPrevButton;
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
