<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_icsgmap3_pi1.php', '_pi1', 'list_type', 0);

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi2/class.tx_icsgmap3_pi2.php', '_pi2', 'list_type', 0);

tx_icsgmap3_provider_manager::subscribe(tx_icsgmap3_provider_manager::BEHAVIOUR_ADD, 'tx_icsgmap3_taglist_provider', 'LLL:EXT:ics_gmap3/locallang.xml:provider.tagList');

//$TYPO3_CONF_VARS['BE']['PROVIDERS']['tceFormsProviderList'] = t3lib_extMgm::extPath('ics_gmap3').'lib/class.tx_icsgmap3_providerList.php:tx_icsgmap3_providerList->displayProviders';

?>