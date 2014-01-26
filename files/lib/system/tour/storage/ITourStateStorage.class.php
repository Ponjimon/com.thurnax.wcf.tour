<?php
namespace wcf\system\tour\storage;

/**
 * Interface for tour state storages
 * 
 * @author	Magnus Kühn
 * @copyright	2013-2014 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
interface ITourStateStorage {
	const STORAGE_NAME = 'tourCache';

	/**
	 * Returns the available tours with the tour trigger 'manual'
	 * 
	 * @return	array<string>
	 */
	public function getAvailableManualTours();
	
	/**
	 * Returns the taken tours
	 * 
	 * @return	array<string>
	 */
	public function getTakenTours();
	
	/**
	 * Marks a tour as taken
	 * 
	 * @param	integer	$tourID
	 */
	public function takeTour($tourID);
}
