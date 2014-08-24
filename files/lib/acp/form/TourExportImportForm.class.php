<?php
namespace wcf\acp\form;
use wcf\data\tour\TourEditor;
use wcf\data\tour\TourList;
use wcf\form\AbstractForm;
use wcf\system\exception\UserInputException;
use wcf\system\language\LanguageFactory;
use wcf\system\package\plugin\TourPackageInstallationPlugin;
use wcf\system\tour\TourExporter;
use wcf\system\WCF;

/**
 * Exports a tour.
 *
 * @author    Magnus Kühn
 * @copyright 2013-2014 Thurnax.com
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package   com.thurnax.wcf.tour
 */
class TourExportImportForm extends AbstractForm {
	/**
	 * name of the active menu item
	 * @var string
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.user.tour.exportImport';

	/**
	 * name of the active menu item
	 *
	 * @var string
	 */
	public $neededPermissions = array('admin.user.canManageTour');

	/**
	 * name of the active menu item
	 *
	 * @var string
	 */
	public $neededModules = array('MODULE_TOUR');

	/**
	 * available tours
	 *
	 * @var \wcf\data\tour\Tour[]
	 */
	public $tours = array();

	/**
	 * upload data
	 *
	 * @var string[]
	 */
	public $importSource = array();

	/**
	 * selected tours
	 *
	 * @var int[]
	 */
	public $selectedTours = array();

	/**
	 * Reads the given parameters.
	 */
	public function readParameters() {
		parent::readParameters();

		// read preselected tour
		if (isset($_GET['id'])) {
			$this->selectedTours[] = intval($_GET['id']);
		}
	}

	/**
	 * Reads/Gets the data to be displayed on this page.
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
		$tourList->getConditionBuilder()->add('identifier IS NOT NULL');
		$tourList->sqlOrderBy = 'tourID ASC';
		$tourList->readObjects();
		$this->tours = $tourList->getObjects();
	}

	/**
	 * Reads the given form parameters.
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
	 * Validates form inputs.
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
	 * Saves the data of the form.
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
			$tourExporter->send(WCF::getLanguage()->get('wcf.acp.tour'));
			$this->saved();
			exit;
		}
	}

	/**
	 * Assigns variables to the template engine.
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array('tours' => $this->tours,
			'selectedTours' => $this->selectedTours));
	}
}
