<?php
namespace wcf\data\tour\step;
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
	 * @return	array<mixed>
	 */
	public function render() {
		$tourStep = array(
			'target' => $this->target,
			'placement' => $this->placement,
			'content' => WCF::getLanguage()->get($this->content)
		);
		
		// add optional fields
		if ($this->title) $tourStep['title'] = WCF::getLanguage()->get($this->title);
		if ($this->xOffset) $tourStep['xOffset'] = $this->xOffset;
		if ($this->yOffset) $tourStep['yOffset'] = $this->yOffset;
		$tourStep['showPrevButton'] = ($this->showPrevButton ? 1 : 0);
		
		if ($this->url) {
			$tourStep['multipage'] = true;
			$tourStep['onNext'] = array('redirect', $this->url);
		}
		
		return $tourStep;
	}
}
