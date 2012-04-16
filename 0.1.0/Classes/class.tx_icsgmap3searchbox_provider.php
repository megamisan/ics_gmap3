<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 In Cité Solution <technique@in-cite.net>
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

/**
 * Behaviour provider for ics_gmap3. Adds a search box.
 * The search box use client side search to display a marker set.
 *
 * @package TYPO3
 * @subpackage ics_gmap3_searchbox
 * @author Pierrick Caillon <pierrick@in-cite.net>
 */
class tx_icsgmap3searchbox_provider implements tx_icsgmap3_iprovider {

	var $extKey = 'ics_gmap3_searchbox';
	
	function __construct() {
		$this->flexform = file_get_contents(t3lib_div::getFileAbsFileName('EXT:' . $this->extKey . '/flexform_searchbox_ds.xml'));
	}
	
	function getStaticData($conf) {
		return null;	
	}
	
	function getDynamicDataUrl($conf) {
		return null;
	}
	
	function getBehaviourInitFunction($conf) {
		$this->incJsFile(t3lib_extMgm::siteRelPath($this->extKey).'res/tx_icsgmap3searchbox.js', false, '_gmap3searchbox_provider');
		$searchFields = json_encode(t3lib_div::trimExplode(chr(10), $conf['search_fields']));
		$jsCode = '';		
		$jsCode .= '
			function (map) {
				(new ics.SearchBox()).init(map, ' . $searchFields . ');
			}
		';
		
		return $jsCode;
	}
	
	function getFlexform($conf) {
		return $this->flexform;
	}
	
	/**
	* Function to insert Javascript at Ext. Runtime
	*
	* @param string $script Input the Script Name to insert JS
	* @return
	*/
	function incJsFile($script, $jsCode = false, $suffix = '') {
		if (!$jsCode) {
			$js = '<script src="'.$script.'" type="text/javascript"><!-- //--></script>';
		}
		else {
			$js .= '<script type="text/javascript">
				' . $script . '
			</script>';
		}
		$GLOBALS['TSFE']->additionalHeaderData[$this->extKey . $suffix . $this->cObj->data['uid']] .= $js;
	}	
}
?>