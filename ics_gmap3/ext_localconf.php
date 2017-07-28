<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43(
    $_EXTKEY,
    'pi1/class.tx_icsgmap3_pi1.php',
    '_pi1',
    'list_type',
    0);
\PlanNet\IcsGmap3\Provider\Manager::subscribe(
    \PlanNet\IcsGmap3\Provider\Manager::BEHAVIOUR_ADD,
    \PlanNet\IcsGmap3\Taglist\Provider::class,
    'LLL:EXT:ics_gmap3/Resources/Private/Language/locallang.xlf:provider.tagList');
