<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}
if (TYPO3_MODE === 'BE') {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['LinkBrowser']['hooks'][] = [
        'handler' => \PlanNet\IcsGmap3Levels\LinkHandler\MapLayerLinkHandler::class,
        'before'  => [], // optional
        'after'   => [] // optional
    ];
}
