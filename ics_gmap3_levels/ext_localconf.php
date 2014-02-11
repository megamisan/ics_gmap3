<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_icsgmap3levels_levels=1
');

tx_icsgmap3_provider_manager::subscribe(tx_icsgmap3_provider_manager::BEHAVIOUR_ADD, 'tx_icsgmap3levels_provider', 'LLL:EXT:ics_gmap3_levels/locallang.xml:provider');

?>