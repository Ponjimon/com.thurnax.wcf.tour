<?php
namespace wcf\system\package;
use wcf\data\package\PackageCache;

/**
 * Dummy package installation dispatcher.
 * 
 * @author	Magnus Kühn
 * @copyright	2013-2014 Thurnax.com
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	com.thurnax.wcf.tour
 */
class DummyPackageInstallationDispatcher extends PackageInstallationDispatcher {
	/**
	 * @see	\wcf\system\package\PackageInstallationDispatcher::$action
	 */
	protected $action = 'install';
	
	/**
	 * Creates a new instance of DummyPackageInstallationDispatcher.
	 * 
	 * @param	string	$package
	 */
	public function __construct($package) {
		$this->package = PackageCache::getInstance()->getPackageByIdentifier($package);
		
	}
	
	/**
	 * @see	\wcf\system\package\PackageInstallationDispatcher::getPackageID()
	 */
	public function getPackageID() {
		return $this->package->packageID;
	}
}
