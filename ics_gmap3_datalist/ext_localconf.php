<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}
\PlanNet\IcsGmap3\Provider\Manager::subscribe(
    \PlanNet\IcsGmap3\Provider\Manager::BEHAVIOUR_ADD,
    'PlanNet\IcsGmap3Datalist\Provider',
    'LLL:EXT:ics_gmap3_datalist/Resources/Private/Language/locallang.xlf:provider.dataList');
