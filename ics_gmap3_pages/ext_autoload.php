<?php

$extensionPath = t3lib_extMgm::extPath('ics_gmap3_pages');
$extensionClassesPath = t3lib_extMgm::extPath('ics_gmap3_pages') . 'classes/';
return array(
	'tx_icsgmap3pages_provider' => $extensionClassesPath . 'class.tx_icsgmap3pages_provider.php',
);
?>