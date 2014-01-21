<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['FE']['XCLASS']['ext/ics_gmap3/Classes/class.tx_icsgmap3_taglist_provider.php'] = PATH_typo3conf.'ext/' . $_EXTKEY . '/classes/class.ux_tx_icsgmap3_taglist_provider.php';

tx_icsgmap3_provider_manager::subscribe(tx_icsgmap3_provider_manager::DATA_STATIC, 'tx_icsgmap3pages_provider', 'LLL:EXT:ics_gmap3_pages/locallang.xml:provider.pages');

//t3lib_div::loadTCA('tt_content');
//$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_befunc.php']['getFlexFormDSClass']['cv_merge_flexform']='EXT:cv_merge_flexform/class.tx_cv_merge_flexform.php:tx_cv_merge_flexform';

?>