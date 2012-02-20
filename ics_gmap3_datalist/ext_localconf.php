<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

tx_icsgmap3_provider_manager::subscribe(tx_icsgmap3_provider_manager::BEHAVIOUR_ADD, 'tx_icsgmap3datalist_provider', 'LLL:EXT:ics_gmap3_datalist/locallang.xml:provider.dataList');

?>