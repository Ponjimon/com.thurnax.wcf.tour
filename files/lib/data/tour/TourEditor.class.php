<?php
namespace wcf\data\tour;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\data\language\item\LanguageItem;
use wcf\system\acl\ACLHandler;
use wcf\system\cache\builder\TourCacheBuilder;
use wcf\system\cache\builder\TourTriggerCacheBuilder;
use wcf\system\language\LanguageFactory;
use wcf\system\tour\TourHandler;
use wcf\system\WCF;

/**
 * Provides functions to edit tours.
 * 
 * @property	integer	$tourID
 * @property	string	$visibleName
 * @property	integer	$isDisabled
 * @property	integer	$packageID
 * @property	string	$tourTrigger
 * @property	string	$className
 * @author	Magnus Kühn
 * @copyright	2013-2014 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class TourEditor extends DatabaseObjectEditor implements IEditableCachedObject {
	const TOUR_IMPORTED_NOTICE = 'tourImportedNotice';
	
	/**
	 * @see	\wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = 'wcf\data\tour\Tour';
	
	/**
	 * @see	\wcf\data\IEditableObject::create()
	 */
	public static function create(array $parameters = array()) {
		$visibleNames = array();
		if (isset($parameters['visibleName']) && is_array($parameters['visibleName'])) {
			if (count($parameters['visibleName']) > 1) {
				$visibleNames = $parameters['visibleName'];
				$parameters['visibleName'] = '';
			} else {
				$parameters['visibleName'] = reset($parameters['visibleName']);
			}
		}
		
		$tour = parent::create($parameters);
		
		// save visible name
		if (!empty($visibleNames)) {
			if (isset($visibleNames[''])) { // set default value
				$defaultValue = $visibleNames[''];
			} else if (isset($visibleNames['en'])) { // fallback to English
				$defaultValue = $visibleNames['en'];
			} else if (isset($visibleNames[WCF::getLanguage()->getFixedLanguageCode()])) { // fallback to the language of the current user
				$defaultValue = $visibleNames[WCF::getLanguage()->getFixedLanguageCode()];
			} else { // fallback to first description
				$defaultValue = reset($visibleNames);
			}
			
			// prepare statement
			$sql = "INSERT INTO	".LanguageItem::getDatabaseTableName()."
						(languageID, languageItem, languageItemValue, languageCategoryID, packageID)
				VALUES		(?, ?, ?, ?, ?)";
			$statement = WCF::getDB()->prepareStatement($sql);
			
			// insert into all languages
			$languages = LanguageFactory::getInstance()->getLanguages();
			$languageCategory = LanguageFactory::getInstance()->getCategory('wcf.acp.tour');
			foreach ($languages as $language) {
				$value = $defaultValue;
				if (isset($visibleNames[$language->languageCode])) {
					$value = $visibleNames[$language->languageCode];
				}
				
				$statement->execute(array($language->languageID, 'wcf.acp.tour.visibleName'.$tour->tourID, $value, $languageCategory->languageCategoryID, $tour->packageID));
			}
			
			// update tour
			$tourEditor = new TourEditor($tour);
			$tourEditor->update(array('visibleName' => 'wcf.acp.tour.visibleName'.$tour->tourID));
		}
		
		return $tour;
	}
	
	/**
	 * @see	\wcf\data\IEditableObject::deleteAll()
	 */
	public static function deleteAll(array $objectIDs = array()) {
		$count = parent::deleteAll($objectIDs);
		
		// remove ACL values
		ACLHandler::getInstance()->removeValues(ACLHandler::getInstance()->getObjectTypeID('com.thurnax.wcf.tour'), $objectIDs);
		
		return $count;
	}
	
	/**
	 * @see	\wcf\data\IEditableCachedObject::resetCache()
	 */
	public static function resetCache() {
		TourCacheBuilder::getInstance()->reset();
		TourTriggerCacheBuilder::getInstance()->reset();
		TourHandler::reset();
		WCF::getSession()->unregister(self::TOUR_IMPORTED_NOTICE);
	}
}
