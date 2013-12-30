<?php
namespace wcf\acp\form;
use wcf\data\package\PackageCache;
use wcf\data\tour\step\TourStep;
use wcf\data\tour\step\TourStepAction;
use wcf\data\tour\step\TourStepList;
use wcf\form\AbstractForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;

/**
 * Shows the tour step edit form.
 * 
 * @author	Magnus Kühn
 * @copyright	2013 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class TourStepEditForm extends TourStepAddForm {
	/**
	 * @see	\wcf\acp\form\ACPForm::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.user.tour.step.list';
	
	/**
	 * tour step id
	 * @var	integer
	 */
	public $tourStepID = 0;
	
	/**
	 * tour step
	 * @var	\wcf\data\tour\step\TourStep
	 */
	public $tourStep = null;
	
	/**
	 * available tour steps
	 * @var	array<\wcf\data\tour\step\TourStep>
	 */
	public $availableTourSteps = array();
	
	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		AbstractForm::readParameters();
		
		// read tour step
		if (isset($_REQUEST['id'])) $this->tourStepID = intval($_REQUEST['id']);
		$this->tourStep = new TourStep($this->tourStepID);
		if (!$this->tourStep->tourStepID) {
			throw new IllegalLinkException();
		}

		// register I18n-items
		I18nHandler::getInstance()->register('title');
		I18nHandler::getInstance()->register('stepContent');
	}
	
	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		
		if (empty($_POST)) {
			I18nHandler::getInstance()->setOptions('title', $this->tourStep->tourStepID, $this->tourStep->title, 'wcf.acp.tour.step.title\d+');
			I18nHandler::getInstance()->setOptions('stepContent', $this->tourStep->tourStepID, $this->tourStep->content, 'wcf.acp.tour.step.content\d+');

			$this->tourID = $this->tourStep->tourID;
			$this->target = $this->tourStep->target;
			$this->placement = $this->tourStep->placement;
			$this->title = $this->tourStep->title;
			$this->stepContent = $this->tourStep->content;
			$this->xOffset = $this->tourStep->xOffset;
			$this->yOffset = $this->tourStep->yOffset;
			$this->showPrevButton = $this->tourStep->showPrevButton;
			$this->url = $this->tourStep->url;
		}
		
		// read available tour steps
		$tourStepList = new TourStepList();
		$tourStepList->getConditionBuilder()->add('tourID = ?', array($this->tourID));	
		$tourStepList->readObjects();
		$this->availableTourSteps = $tourStepList->getObjects();
	}
	
	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		AbstractForm::save();
		$packageID = PackageCache::getInstance()->getPackageID('com.thurnax.wcf.tour');
		
		// save title
		if (I18nHandler::getInstance()->isPlainValue('title')) {
			I18nHandler::getInstance()->remove($this->title);
		} else {
			$this->title = 'wcf.acp.tour.step.title'.$this->tourStep->tourStepID;
			I18nHandler::getInstance()->save('title', $this->title, 'wcf.acp.tour', $packageID);
		}
		
		// save content
		if (I18nHandler::getInstance()->isPlainValue('stepContent')) {
			I18nHandler::getInstance()->remove($this->stepContent);
		} else {
			$this->stepContent = 'wcf.acp.tour.step.content'.$this->tourStep->tourStepID;
			I18nHandler::getInstance()->save('stepContent', $this->stepContent, 'wcf.acp.tour', $packageID);
		}
		
		// update tour point
		$this->objectAction = new TourStepAction(array($this->tourStepID), 'update', array('data' => array(
			'tourID' => $this->tourID,
			'target' => $this->target,
			'placement' => $this->placement,
			'title' => $this->title,
			'content' => $this->stepContent,
			'xOffset' => $this->xOffset,
			'yOffset' => $this->yOffset,
			'showPrevButton' => ($this->showPrevButton ? 1 : 0),
			'url' => $this->url
		)));
		$this->objectAction->executeAction();
		$this->saved();
		
		// show success
		WCF::getTPL()->assign('success', true);
	}
	
	/**
	 * @see	\wcf\page\IPage::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		I18nHandler::getInstance()->assignVariables(!empty($_POST));
		WCF::getTPL()->assign(array(
			'action' => 'edit',
			'tourStepID' => $this->tourStepID,
			'tourStep' => $this->tourStep,
			'availableTourSteps' => $this->availableTourSteps
		));
	}
}
