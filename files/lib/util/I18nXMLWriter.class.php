<?php
namespace wcf\util;
use wcf\data\language\item\LanguageItem;
use wcf\data\language\Language;
use wcf\system\WCF;

/**
 * Writes XML documents supporting I18n values.
 *
 * @author    Magnus Kühn
 * @copyright 2013-2014 Thurnax.com
 * @license   GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package   com.thurnax.wcf.tour
 */
class I18nXMLWriter extends XMLWriter {
	/**
	 * Writes an element directly.
	 *
	 * @param string   $element
	 * @param string   $cdata
	 * @param string[] $attributes
	 * @param boolean  $force
	 */
	public function writeElement($element, $cdata, array $attributes = array(), $force = false) {
		if ($force || $cdata) {
			parent::writeElement($element, $cdata, $attributes);
		}
	}

	/**
	 * Writes an I18n element directly.
	 *
	 * @param string   $element
	 * @param string   $cdata
	 * @param string[] $attributes
	 * @param boolean  $force
	 */
	public function writeI18nElement($element, $cdata, array $attributes = array(), $force = false) {
		if (WCF::getLanguage()->get($cdata) != $cdata) { // I18n value
			$sql = "SELECT	language.languageCode, language_item.languageItemValue
				FROM	".LanguageItem::getDatabaseTableName()." language_item
				LEFT JOIN ".Language::getDatabaseTableName()." language
				ON	(language.languageID = language_item.languageID)
				WHERE	language_item.languageItem = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute(array($cdata));

			// write language values
			while ($row = $statement->fetchArray()) {
				$attributes['language'] = $row['languageCode'];
				$this->writeElement($element, $row['languageItemValue'], $attributes, $force);
			}
		} else { // plain value
			$this->writeElement($element, $cdata, $attributes, $force);
		}
	}

}
