<?php
namespace wcf\acp\form;
use wcf\data\tour\TourEditor;
use wcf\data\tour\TourList;
use wcf\form\AbstractForm;
use wcf\system\event\EventHandler;
use wcf\system\exception\UserInputException;
use wcf\system\language\LanguageFactory;
use wcf\system\package\plugin\TourPackageInstallationPlugin;
use wcf\system\tour\TourExporter;
use wcf\system\WCF;

/**
 * Exports a tour.
 * 
 * @author	Magnus Kühn
 * @copyright	2013-2014 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class TourExportImportForm extends AbstractForm {
	/**
	 * @see	\wcf\acp\page\AbstractPage::$activeMenuItem
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.user.tour.exportImport';
	
	/**
	 * @see	\wcf\page\AbstractPage::$neededPermissions
	 */
	public $neededPermissions = array('admin.user.canManageTour');
	
	/**
	 * @see	\wcf\page\AbstractPage::$neededModules
	 */
	public $neededModules = array('MODULE_TOUR');
	
	/**
	 * available tours
	 * @var	array<\wcf\data\tour\Tour>
	 */
	public $tours = array();
	
	/**
	 * upload data
	 * @var	array<string>
	 */
	public $importSource = array();
	
	/**
	 * selected tours
	 * @var	array<integer>
	 */
	public $selectedTours = array();
	
	/**
	 * @see	\wcf\page\IPage::readParameters()
	 */
	public function readParameters() {
		parent::readParameters();
		
		// read preselected tour
		if (isset($_POST['id'])) {
			$this->selectedTours[] = intval($_POST['id']);
		}
	}
	
	/**
	 * @see	\wcf\page\IPage::readData()
	 */
	public function readData() {
		// read tours
		$this->readTours();
		
		// submit form - read form parameters, validate and save
		parent::readData();
	}
	
	/**
	 * Reads the tours
	 */
	public function readTours() {
		$tourList = new TourList();
		$tourList->readObjects();
		$this->tours = $tourList->getObjects();
	}
	
	/**
	 * @see	\wcf\form\IForm::readFormParameters()
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		
		// read import source
		if (isset($_FILES['importSource'])) {
			$this->importSource = $_FILES['importSource'];
		}
		
		// read selected tours
		if (isset($_POST['selectedTours'])) {
			foreach ($_POST['selectedTours'] as $selectedTourID => $state) {
				$this->selectedTours[] = intval($selectedTourID);
			}
		}
	}
	
	/**
	 * @see	\wcf\form\IForm::validate()
	 */
	public function validate() {
		parent::validate();
		
		if ($this->action == 'import') { // validate import source
			if (empty($this->importSource['name'])) {
				throw new UserInputException('importSource');
			}
			
			if (empty($this->importSource['tmp_name'])) {
				throw new UserInputException('importSource', 'uploadFailed');
			}
		} else { // validate selected tours
			foreach ($this->selectedTours as $selectedTourID) {
				if (!isset($this->tours[$selectedTourID])) {
					throw new UserInputException('selectedTours');
				}
			}
		}
	}
	
	/**
	 * @see	\wcf\form\IForm::save()
	 */
	public function save() {
		parent::save();
		
		if ($this->action == 'import') {
			try {
				TourPackageInstallationPlugin::importFile($this->importSource['tmp_name']);
			} catch (\Exception $e) {
				@unlink($this->importSource['tmp_name']);
				throw new UserInputException('importSource', 'importFailed');
			}
			
			// cleanup
			TourEditor::resetCache();
			LanguageFactory::getInstance()->deleteLanguageCache();
			@unlink($this->selectedTours['tmp_name']);
			
			// import done
			$this->readTours();
			$this->saved();
			WCF::getTPL()->assign('success', true);
		} else {
			$tourExporter = new TourExporter();
			foreach ($this->selectedTours as $tourID) {
				$tourExporter->writeTour($this->tours[$tourID]);
			}
			
			// send tour xml
			$tourExporter->send('TOUR'); // @todo add proper filename
			$this->saved();
			exit;
		}
	}
	
	/**
	 * @see	\wcf\form\IForm::assignVariables()
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign(array(
			'tours' => $this->tours,
			'selectedTours' => $this->selectedTours
		));
	}
}
