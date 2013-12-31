<?php
namespace wcf\acp\form;
use wcf\data\package\PackageCache;
use wcf\data\tour\step\TourStep;
use wcf\data\tour\step\TourStepAction;
use wcf\form\AbstractForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\language\I18nHandler;
use wcf\system\WCF;

/**
 * Shows the tour step edit form.
 * 
 * @author	Magnus Kühn
 * @copyright	2013-2014 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class TourStepEditForm extends TourStepAddForm {
	/**
	 * @see	\wcf\acp\form\ACPForm::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.user.tour';
	
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
		I18nHandler::getInstance()->register('ctaLabel');
	}
	
	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		parent::readData();
		
		if (empty($_POST)) {
			I18nHandler::getInstance()->setOptions('title', $this->tourStep->tourStepID, $this->tourStep->title, 'wcf.acp.tour.step.title\d+');
			I18nHandler::getInstance()->setOptions('stepContent', $this->tourStep->tourStepID, $this->tourStep->content, 'wcf.acp.tour.step.content\d+');
			I18nHandler::getInstance()->setOptions('ctaLabel', $this->tourStep->tourStepID, $this->tourStep->ctaLabel, 'wcf.acp.tour.step.ctaLabel\d+');

			$this->tourID = $this->tourStep->tourID;
			$this->target = $this->tourStep->target;
			$this->placement = $this->tourStep->placement;
			$this->stepContent = $this->tourStep->content;
			
			// optionals
			$this->title = $this->tourStep->title;
			$this->showPrevButton = $this->tourStep->showPrevButton;
			$this->xOffset = $this->tourStep->xOffset;
			$this->yOffset = $this->tourStep->yOffset;
			$this->url = $this->tourStep->url;
			$this->ctaLabel = $this->tourStep->ctaLabel;
			
			// callbacks
			$this->onPrev = $this->tourStep->onPrev;
			$this->onNext = $this->tourStep->onNext;
			$this->onShow = $this->tourStep->onShow;
			$this->onCTA = $this->tourStep->onCTA;
		}
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
		
		// save cta label
		if (I18nHandler::getInstance()->isPlainValue('ctaLabel')) {
			I18nHandler::getInstance()->remove($this->ctaLabel);
		} else {
			$this->title = 'wcf.acp.tour.step.ctaLabel'.$this->tourStep->tourStepID;
			I18nHandler::getInstance()->save('ctaLabel', $this->title, 'wcf.acp.tour', $packageID);
		}
		
		// update tour point
		$this->objectAction = new TourStepAction(array($this->tourStepID), 'update', array('data' => array(
			'tourID' => $this->tourID,
			'target' => $this->target,
			'placement' => $this->placement,
			'content' => $this->stepContent,
			
			// optionals
			'title' => ($this->title ?: null),
			'showPrevButton' => ($this->showPrevButton ? 1 : 0),
			'xOffset' => ($this->xOffset ?: null),
			'yOffset' => ($this->yOffset ?: null),
			'url' => ($this->url ?: null),
			'ctaLabel' => ($this->ctaLabel ?: null),
			
			// callbacks
			'onPrev' => ($this->onPrev ?: null),
			'onNext' => ($this->onNext ?: null),
			'onShow' => ($this->onShow ?: null),
			'onCTA' => ($this->onCTA ?: null),
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
			'tourStep' => $this->tourStep
		));
	}
}
