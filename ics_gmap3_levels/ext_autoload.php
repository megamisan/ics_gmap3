<?php

$extensionPath = t3lib_extMgm::extPath('ics_gmap3_levels');
$extensionClassesPath = t3lib_extMgm::extPath('ics_gmap3_levels') . 'Classes/';
return array(
    'tx_icsgmap3levels_tca' =>  $extensionClassesPath . 'class.tx_icgmap3levels_tca.php'
);

?>