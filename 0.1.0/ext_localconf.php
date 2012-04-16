<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

tx_icsgmap3_provider_manager::subscribe(tx_icsgmap3_provider_manager::BEHAVIOUR_ADD, 'tx_icsgmap3searchbox_provider', 'LLL:EXT:ics_gmap3_searchbox/locallang.xml:provider.searchBox');

?>