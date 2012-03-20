<?php
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


class tx_icsgmap3_taglist_provider implements tx_icsgmap3_iprovider {

	var $extKey = 'ics_gmap3';
	
	function __construct() {
		$this->uploadsPath = 'uploads/tx_icsgmap3/';
		
		// Hook additional_flex
		$additional_flex = '';
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tx_icsgmap3_taglist_provider']['additionalFlex'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tx_icsgmap3_taglist_provider']['additionalFlex'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->additionalFlex($additional_flex);
			}
		}
		
		$this->flexform = str_replace('<!-- ###ADDITIONAL FLEX### -->', $additional_flex, file_get_contents(t3lib_div::getFileAbsFileName('EXT:' . $this->extKey . '/flexform_ds_taglist.xml')));
	}
	
	function getStaticData($conf) {
		return null;	
	}
	
	function getDynamicDataUrl($conf) {
		return null;
	}
	
	function getBehaviourInitFunction($conf) {
		$this->incJsFile(t3lib_extMgm::siteRelPath($this->extKey) . 'res/js/gmap3_provider_taglist.js', false, '_gmap3_provider_taglist');
		
		$jsCode = '';
		$jsCode .= '
			function (map) { 
				var exclusivesTags = new Array();
				var hiddenTags = new Array();
				var defaultTags = new Array();';
		
		$exclusivesTags = explode(',', $conf['exclusivesTags']);
		if (is_array($exclusivesTags) && !empty($exclusivesTags)) {
			foreach ($exclusivesTags as $tag) {
				if ($tag) 
					$jsCode .= '
				exclusivesTags.push(\'' . addslashes($tag) . '\');';
			}
		}
		$hiddenTags = explode(',', $conf['hiddenTags']);
		if (is_array($hiddenTags) && !empty($hiddenTags)) {
			foreach ($hiddenTags as $tag) {
				if ($tag) 
					$jsCode .= '
				hiddenTags.push(\'' . addslashes($tag) . '\');';
				
			}
		}
		$defaultTags = explode(',', $conf['defaultTags']);
		if (is_array($defaultTags) && !empty($defaultTags)) {
			foreach ($defaultTags as $tag) {
				if ($tag) 
					$jsCode .= '
				defaultTags.push(\'' . addslashes($tag) . '\');';
			}
		}
		
		$jsCode .= '
				(new ics.TagList()).init(map, exclusivesTags, hiddenTags, defaultTags, ' . $conf['defaultMapEmpty'] . ');
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
		if (!$jsCode)
			$js = '<script src="' . $script . '" type="text/javascript"><!-- //--></script>';
		else {
			$js .= '<script type="text/javascript">
				' . $script . '
			</script>';
		}
		$GLOBALS['TSFE']->additionalHeaderData[$this->extKey . $suffix . $this->cObj->data['uid']] .= $js;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_gmap3/Classes/class.tx_icsgmap3_taglist_provider.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_gmap3/Classes/class.tx_icsgmap3_taglist_provider.php']);
}

?>