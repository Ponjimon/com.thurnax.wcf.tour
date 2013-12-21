<?php
namespace wcf\data\tour\step;
use wcf\data\tour\Tour;
use wcf\data\DatabaseObject;
use wcf\system\WCF;

/**
 * Represents a tour step.
 * 
 * @author	Simon Nußbaumer
 * @copyright	2013 Thurnax.com
 * @package	com.thurnax.wcf.tour
 * @category	Community Framework (commercial)
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
	 * @param	\wcf\data\tour\Tour	$tour
	 * @return	array<mixed>
	 */
	public function render(Tour $tour) {
		$tourStep = array(
			'target' => $this->target,
			'placement' => $this->placement,
			'content' => $this->compileField('content')
		);
		
		// add optional fields
		if ($this->title) $tourStep['title'] = $this->compileField('title');
		if ($this->xOffset) $tourStep['xOffset'] = $this->xOffset;
		if ($this->yOffset) $tourStep['yOffset'] = $this->yOffset;
		if (!$tour->showPrevButton && $this->showOrder > 1) $tourStep['showPrevButton'] = false;
		
		if ($this->url) {
			$tourStep['multipage'] = true;
			$tourStep['onNext'] = array('redirect', $this->url);
		}
		
		return $tourStep;
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
