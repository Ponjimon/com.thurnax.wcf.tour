<?php
namespace wcf\acp\form;
use wcf\data\tour\Tour;
use wcf\data\tour\TourAction;
use wcf\form\AbstractForm;
use wcf\system\acl\ACLHandler;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;

/**
 * Shows the tour edit form.
 *
 * @author    Magnus Kühn
 * @copyright 2013-2014 Thurnax.com
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package   com.thurnax.wcf.tour
 */
class TourEditForm extends TourAddForm {
	/**
	 * name of the active menu item
	 *
	 * @var string
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.user.tour';

	/**
	 * tour id
	 *
	 * @var int
	 */
	public $tourID = 0;

	/**
	 * tour
	 *
	 * @var \wcf\data\tour\Tour
	 */
	public $tour = null;

	/**
	 * Reads the given parameters.
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
	 * Validates the identifier
	 */
	protected function validateIdentifier() {
		return (mb_strtolower($this->tour->identifier) == mb_strtolower($this->identifier)) || parent::validateIdentifier();
	}

	/**
	 * Saves the data of the form.
	 */
	public function save() {
		AbstractForm::save();

		// update tour
		$this->objectAction = new TourAction(array($this->tourID), 'update', array('data' => array('visibleName' => $this->visibleName,
			'tourTrigger' => $this->tourTrigger,
			'className' => ($this->className ? : null),
			'identifier' => ($this->identifier ? : null))));
		$this->objectAction->executeAction();

		// update acl
		ACLHandler::getInstance()->save($this->tourID, $this->objectTypeID);
		ACLHandler::getInstance()->disableAssignVariables();

		// show success
		$this->saved();
		WCF::getTPL()->assign('success', true);
	}

	/**
	 * Reads/Gets the data to be displayed on this page.
	 */
	public function readData() {
		parent::readData();

		if (empty($_POST)) {
			$this->visibleName = $this->tour->visibleName;
			$this->tourTrigger = $this->tour->tourTrigger;
			$this->className = $this->tour->className;
			$this->identifier = $this->tour->identifier;
		}
	}

	/**
	 * Assigns variables to the template engine.
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign(array('tourID' => $this->tourID,
			'tour' => $this->tour,
			'action' => 'edit'));
	}
}
