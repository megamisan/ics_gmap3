<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

tx_icsgmap3_provider_manager::subscribe(tx_icsgmap3_provider_manager::DATA_STATIC, 'tx_pnfgmap3ttaddresspoi_provider', 'LLL:EXT:pnf_gmap3_ttaddress_poi/locallang.xml:provider.ttaddress_poi');
?>