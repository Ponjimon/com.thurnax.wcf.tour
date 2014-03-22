<?php
namespace wcf\data\tour\step;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\data\language\item\LanguageItem;
use wcf\data\tour\TourEditor;
use wcf\system\language\LanguageFactory;
use wcf\system\WCF;

/**
 * Provides functions to edit tour steps.
 * 
 * @property	integer	$tourStepID
 * @property	integer	$tourID
 * @property	integer	$showOrder
 * @property	integer	$isDisabled
 * @property	integer	$packageID
 * @property	string	$target
 * @property	string	$placement
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
class TourStepEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	/**
	 * @see	\wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = 'wcf\data\tour\step\TourStep';
	
	/**
	 * @see	\wcf\data\IEditableObject::create()
	 */
	public static function create(array $parameters = array()) {
		// get I18n fields
		list($title, $parameters) = self::getI18nField($parameters, 'title');
		list($content, $parameters) = self::getI18nField($parameters, 'content');
		list($ctaLabel, $parameters) = self::getI18nField($parameters, 'ctaLabel');
		
		$tourStep = parent::create($parameters);
		
		// save I18n fields
		if (!empty($title)) self::saveI18nField($title, 'title', $tourStep);
		if (!empty($content)) self::saveI18nField($content, 'content', $tourStep);
		if (!empty($ctaLabel)) self::saveI18nField($ctaLabel, 'ctaLabel', $tourStep);
		
		return $tourStep;
	}
	
	/**
	 * Gets the values for an I18n-field
	 *
	 * @param	array	$parameters
	 * @param	string	$field
	 * @return	array<string>
	 */
	protected static function getI18nField(array $parameters, $field) {
		$values = array();
		if (isset($parameters[$field]) && is_array($parameters[$field])) {
			if (count($parameters[$field]) > 1) {
				$values = $parameters[$field];
				$parameters[$field] = '';
			} else {
				$parameters[$field] = reset($parameters[$field]);
			}
		}
		
		return array($values, $parameters);
	}
	
	/**
	 * Saves the values for an I18n-field
	 * 
	 * @param	array<string>			$values
	 * @param	string				$field
	 * @param	\wcf\data\tour\step\TourStep	$tourStep
	 */
	protected static function saveI18nField($values, $field, TourStep $tourStep) {
		if (isset($values[''])) { // set default value
			$defaultValue = $values[''];
		} else if (isset($values['en'])) { // fallback to English
			$defaultValue = $values['en'];
		} else if (isset($values[WCF::getLanguage()->getFixedLanguageCode()])) { // fallback to the language of the current user
			$defaultValue = $values[WCF::getLanguage()->getFixedLanguageCode()];
		} else { // fallback to first description
			$defaultValue = reset($values);
		}
		
		// prepare statement
		$sql = "INSERT INTO	" . LanguageItem::getDatabaseTableName() . "
					(languageID, languageItem, languageItemValue, languageCategoryID, packageID)
			VALUES		(?, ?, ?, ?, ?)";
		$statement = WCF::getDB()->prepareStatement($sql);
		
		// insert into all languages
		$languages = LanguageFactory::getInstance()->getLanguages();
		$languageCategory = LanguageFactory::getInstance()->getCategory('wcf.acp.tour');
		foreach ($languages as $language) {
			$value = $defaultValue;
			if (isset($values[$language->languageCode])) {
				$value = $values[$language->languageCode];
			}
			
			$statement->execute(array($language->languageID, 'wcf.acp.tour.step.'.$field.$tourStep->tourStepID, $value, $languageCategory->languageCategoryID, $tourStep->packageID));
		}
		
		// update tour
		$tourStepEditor = new self($tourStep);
		$tourStepEditor->update(array($field => 'wcf.acp.tour.step.'.$field.$tourStep->tourStepID));
	}
	
	/**
	 * @see	\wcf\data\IEditableCachedObject::resetCache()
	 */
	public static function resetCache() {
		TourEditor::resetCache();
	}
}
