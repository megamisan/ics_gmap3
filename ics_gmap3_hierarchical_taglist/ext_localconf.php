<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

tx_icsgmap3_provider_manager::subscribe(tx_icsgmap3_provider_manager::BEHAVIOUR_ADD, 'tx_icsgmap3hierarchicaltaglist_provider', 'LLL:EXT:ics_gmap3_hierarchical_taglist/locallang.xml:provider.tagList');

//t3lib_div::loadTCA('tt_content');
//$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_befunc.php']['getFlexFormDSClass']['cv_merge_flexform']='EXT:cv_merge_flexform/class.tx_cv_merge_flexform.php:tx_cv_merge_flexform';

?>