<?php
namespace wcf\acp\form;
use wcf\data\tour\step\TourStepEditor;
use wcf\data\tour\TourEditor;
use wcf\form\AbstractForm;
use wcf\system\cache\builder\LanguageCacheBuilder;
use wcf\system\event\EventHandler;
use wcf\system\exception\UserInputException;
use wcf\system\language\LanguageFactory;
use wcf\system\package\DummyPackageInstallationDispatcher;
use wcf\system\package\PackageInstallationDispatcher;
use wcf\system\package\plugin\TourPackageInstallationPlugin;
use wcf\system\WCF;

/**
 * Imports tours.
 * 
 * @author	Magnus Kühn
 * @copyright	2013-2014 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class TourImportForm extends AbstractForm {
	/**
	 * @see	\wcf\acp\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.user.tour.import';
	
	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.user.canManageTour');
	
	/**
	 * @see	\wcf\page\AbstractPage::$neededModules
	 */
	public $neededModules = array('MODULE_TOUR');
	
	/**
	 * upload data
	 * @var	array<string>
	 */
	public $source = array();
	
	/**
	 * @see	\wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();

		if (isset($_FILES['source'])) $this->source = $_FILES['source'];
	}
	
	/**
	 * @see	\wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		if (empty($this->source['name'])) {
			throw new UserInputException('source');
		}
		
		if (empty($this->source['tmp_name'])) {
			throw new UserInputException('source', 'uploadFailed');
		}
		
		try {
			TourPackageInstallationPlugin::importFile($this->source['tmp_name']);
		} catch (\Exception $e) {
			var_dump($e); exit;
			@unlink($this->source['tmp_name']);
			throw new UserInputException('source', 'importFailed');
		}
	}
	
	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();
		
		// cleanup
		TourEditor::resetCache();
		LanguageFactory::getInstance()->deleteLanguageCache();
		@unlink($this->source['tmp_name']);
		
		// import done
		$this->saved();
		WCF::getTPL()->assign('success', true);
	}
}
