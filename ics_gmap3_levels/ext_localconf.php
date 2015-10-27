<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_icsgmap3levels_levels=1
');

tx_icsgmap3_provider_manager::subscribe(tx_icsgmap3_provider_manager::BEHAVIOUR_ADD, 'tx_icsgmap3levels_provider', 'LLL:EXT:ics_gmap3_levels/locallang.xml:provider');

// Hooks pour pré selectionner une couche de cartographie
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/rtehtmlarea/mod3/class.tx_rtehtmlarea_browse_links.php']['browseLinksHook']['ics_gmap3_levels_preselect_levels'] = '\PlanNet\IcsGmap3Levels\Hooks\BrowseLinks';

// Hook sur Parser typolink pour construction des liens en FE
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_content.php']['typolinkLinkHandler']['ics_gmap3_levels_preselect_levels'] = '\PlanNet\IcsGmap3Levels\Hooks\LinkHandler';
?>