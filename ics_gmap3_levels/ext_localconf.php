<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig('
options.saveDocNew.tx_icsgmap3levels_levels=1
');
if (TYPO3_MODE === 'BE') {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
TCEMAIN.linkHandler.mapLayer {
    handler = PlanNet\\IcsGmap3Levels\\LinkHandler\\MapLayerLinkHandler
    label = LLL:EXT:ics_gmap3_levels/Resources/Private/Language/locallang_browse_links.xlf:mapLayer
    scanBefore = page
}
');
}
\PlanNet\IcsGmap3\Provider\Manager::subscribe(
    \PlanNet\IcsGmap3\Provider\Manager::BEHAVIOUR_ADD,
    'PlanNet\IcsGmap3Levels\Provider',
    'LLL:EXT:ics_gmap3_levels/Resources/Private/Language/locallang.xlf:provider');
// Hook sur Parser typolink pour construction des liens en FE
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tslib/class.tslib_content.php']['typolinkLinkHandler']['ics_gmap3_levels_preselect_levels'] = 'PlanNet\IcsGmap3Levels\Hooks\LinkHandler';
