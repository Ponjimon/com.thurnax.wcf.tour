<?php
namespace wcf\data\tour\step;
use wcf\data\DatabaseObject;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents a tour step. 
 * 
 * @property	integer	$tourStepID
 * @property	integer	$tourID
 * @property	integer	$showOrder
 * @property	integer	$isDisabled
 * @property	integer	$packageID
 * @property	string	$target
 * @property	string	 $orientation
 * @property	string	$content
 * @property	string	$title
 * @property	integer	$showPrevButton
 * @property	integer	$xOffset
 * @property	integer	$yOffset
 * @property	string	$url
 * @property	string	$ctaLabel
 * @property	string	$onPrev
 * @property	string	$onNext
 * @property	string	$onShow
 * @property	string	$onCTA
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
			'content' => $this->compileField('content'),
			'template' => WCF::getTPL()->fetch('tour', 'wcf', array(
				'tourStep' => $this,
				'content' => $this->compileField('content'),
				'title' => $this->compileField('title')
			))
		);
		
		// add optional fields
		if ($this->title) $tourStep['title'] = $this->compileField('title');
		$tourStep['showPrevButton'] = ($this->showPrevButton ? 1 : 0);
		if ($this->xOffset) $tourStep['xOffset'] = $this->xOffset;
		if ($this->yOffset) $tourStep['yOffset'] = $this->yOffset;
		
		// redirect forward
		if ($this->url) {
			$tourStep['multipage'] = true;
			$tourStep['onNext'] = array('redirect_forward', $this->url);
		}
		
		// redirect back
		if ($previousTourStep && $previousTourStep->url) {
			$tourStep['onPrev'] = array('redirect_back');
		}
		
		// cta button
		if ($this->ctaLabel) {
			$tourStep['showCTAButton'] = true;
			$tourStep['ctaLabel'] = $this->compileField('ctaLabel');
		}
		
		// callbacks
		if ($this->onPrev) $tourStep['onPrev'] = array('custom_callback', $this->onPrev);
		if ($this->onNext) $tourStep['onNext'] = array('custom_callback', $this->onNext);
		if ($this->onShow) $tourStep['onShow'] = array('custom_callback', $this->onShow);
		if ($this->onCTA) $tourStep['onCTA'] = $this->onCTA; // on cta doesn't invoke helpers
		
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
