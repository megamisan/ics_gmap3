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
 
 class ux_tx_icsgmap3_taglist_provider extends tx_icsgmap3_taglist_provider {
	var $extKey_xclass = 'ics_gmap3_multiple_tagslist';
	
	function getBehaviourInitFunction($conf) {
		$conf = $this->initConf($conf);
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
		
		if ($conf['fieldSecondList']) {
			$jsCode .= '
				var secondListFieldName = \'' . $conf['fieldSecondList'] . '\';';
		} else {
				$jsCode .= '
				var secondListFieldName = \'\';';
		}
		
		if ($conf['tagsSelectorSecondList']) {
			$jsCode .= '
				var secondTagsSelector = \'' . $conf['tagsSelectorSecondList'] . '\';';
		} else {
				$jsCode .= '
				var secondTagsSelector = \'\';';
		}
		
		$jsCode .= '
				(new ics.TagList()).init(map, exclusivesTags, hiddenTags, defaultTags, ' . $conf['defaultMapEmpty'] . ', \'' . $conf['tagsSelector'] . '\', secondListFieldName, secondTagsSelector);
			}
		';
		
		return $jsCode;
	}
	
	function incJsFile($script, $jsCode = false, $suffix = '') {
		parent::incJsFile($script, $jsCode, $suffix);
		$script = 'typo3conf/ext/' . $this->extKey_xclass . '/res/gmap3_multiple_taglist.js';
		if (!$jsCode)
			$js = '<script src="' . $script . '" type="text/javascript"><!-- //--></script>';
		else {
			$js .= '<script type="text/javascript">
				' . $script . '
			</script>';
		}
		$GLOBALS['TSFE']->additionalHeaderData[$this->extKey_xclass . $suffix . $this->cObj->data['uid']] .= $js;
	}
	
	
 }
 
?>