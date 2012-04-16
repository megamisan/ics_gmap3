<?php

$GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/ics_gmap3/Classes/class.tx_icsgmap3_taglist_provider.php'] = PATH_typo3conf.'ext/' . $_EXTKEY . '/Classes/class.ux_tx_icsgmap3_taglist_provider.php';

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tx_icsgmap3_taglist_provider']['additionalFlex'][] = 'EXT:' . $_EXTKEY . '/Classes/class.user_icsgmap3taglist_additionalFlex.php:user_icsgmap3taglist_additionalFlex'; 
?>