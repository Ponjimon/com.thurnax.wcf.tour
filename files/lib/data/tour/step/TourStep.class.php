<?php
namespace wcf\data\tour\step;
use wcf\data\DatabaseObject;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents a tour step. 
  
  
*
*@property	integer	$tourStepID
 * @property	integer	$tourID
 * @property	integer	$showOrder
 * @property	integer	$isDisabled
 * @property	integer	$packageID
 * @property	string	$target
 * @property	string	$orientation
 * @property	string	$content
 * @property	string	$title
 * @property	integer	$xOffset
 * @property	integer	$yOffset
 * @property	string	$url
 * @property	string	$callbackBefore
 * @property	string	$callbackAfter
 * @author	Magnus Kühn
 * @copyright	2013-2014 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class TourStep extends DatabaseObject {
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableName
	 */
	protected static $databaseTableName = 'tour_step';
	
	/**
	 * @see	\wcf\data\DatabaseObject::$databaseTableIndexName
	 */
	protected static $databaseTableIndexName = 'tourStepID';
	
	/**
	 * Renders the tour step
	 * 
	 * @param	\wcf\data\tour\step\TourStep	$previousTourStep
	 * @return	array<mixed>
	 */
	public function render(TourStep $previousTourStep = null) {
		$tourStep = array(
			'target' => $this->target,
			'orientation' => $this->getOrientation(),
			'template' => WCF::getTPL()->fetch('tour', 'wcf', array(
				'tourStep' => $this,
				'content' => $this->compileField('content'),
				'title' => $this->compileField('title'),
				'previousTourStep' => $previousTourStep
			)),
			'xOffset' => ($this->xOffset ?: 0),
			'yOffset' => ($this->yOffset ?: 0)
		);
		
//		// redirect forward
//		if ($this->url) {
//			$tourStep['multipage'] = true;
//			$tourStep['callbackAfter'] = array('redirect_forward', $this->url);
//		}
//		
//		// redirect back
//		if ($previousTourStep && $previousTourStep->url) {
//			$tourStep['callbackBefore'] = array('redirect_back');
//		}
		
		// callbacks
		if ($this->callbackBefore) $tourStep['callbackBefore'] = array('custom_callback', $this->callbackBefore);
		if ($this->callbackAfter) $tourStep['callbackAfter'] = array('custom_callback', $this->callbackAfter);
		
		return $tourStep;
	}
	
	/**
	 * Returns the orientation
	 * 
	 * @return	array<string>
	 */
	public function getOrientation() {
		$orientations = explode('-', $this->orientation);
		return array('x' => $orientations[1], 'y' => $orientations[0]);
	}
	
	/**
	 * Compiles a field
	 * 
	 * @param	string	$field
	 * @return	string
	 */
	protected function compileField($field) {
		if (WCF::getLanguage()->isDynamicItem($this->$field)) {
			return WCF::getLanguage()->getDynamicVariable($this->$field);
		} else {
			$compiledString = WCF::getTPL()->getCompiler()->compileString('tourStep'.ucfirst($this->$field).$this->tourStepID, WCF::getLanguage()->get($this->$field));
			return WCF::getTPL()->fetchString($compiledString['template']);
		}
	}
}
