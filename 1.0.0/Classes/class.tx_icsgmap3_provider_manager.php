<?
/***************************************************************
*  Copyright notice
*
*  (c) 2011 In Cité Solution <technique@in-cite.net>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */


class tx_icsgmap3_provider_manager {
	// Data
	const DATA_NONE = 0;
	const DATA_STATIC = 1;
	const DATA_DYNAMIC = 2;
	
	// Behaviour
	const BEHAVIOUR_NONE = 0;
	const BEHAVIOUR_ADD = 4;
	
	private static $providers;
	
	function subscribe($data, $provider, $providerName) {
		//data ?
		
		//$aProviderName = t3lib_div::trimExplode('|',$providerName);
		//var_dump($provider);
		self::$providers[$provider]['name'] = $providerName;
		self::$providers[$provider]['data'] = $data;
		//self::$providers[$provider]['key'] = $aProviderName[1];
	}
	
	function getSubscribers() {
		return self::$providers;
	}
	
	/*function getSubscriber($provider) {
		//var_dump($provider);
		return $provider;
	}*/
	
}

?>