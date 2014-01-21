<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Plan.Net France <typo3@plan-net.fr>
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
	var $extKey_xclass = 'ics_gmap3_pages';
	
	function incJsFile($script, $jsCode = false, $suffix = '') {
		parent::incJsFile($script, $jsCode, $suffix);
		$script = 'typo3conf/ext/' . $this->extKey_xclass . '/res/gmap3_taglist_pages.js';
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