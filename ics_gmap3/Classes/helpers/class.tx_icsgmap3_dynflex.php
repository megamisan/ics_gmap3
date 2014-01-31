<?php

class tx_icsgmap3_dynflex {

	function getSingleField_preProcess($table, $field, array &$row, $altName, $palette, $extra, $pal, t3lib_TCEforms $tce)
	{
		if (($table != 'tt_content') || ($field != 'pi_flexform') || ($row['CType'] != 'list') || ($row['list_type'] != 'ics_gmap3_pi1'))
			return;
		t3lib_div::loadTCA($table);
		$conf = &$GLOBALS['TCA'][$table]['columns'][$field];
		$this->id = $row['pid'];
		$flexData = (!empty($row['pi_flexform'])) ? (t3lib_div::xml2array($row['pi_flexform'])) : (array('data' => array()));
		
		$subscribers = tx_icsgmap3_provider_manager::getSubscribers();
		if(is_array($subscribers) && count($subscribers)) {
			$classes = array_keys($subscribers);
		}

		if(!empty($flexData['data']['sDEF']['lDEF']['providers']['vDEF'])) {
			$aProviders = t3lib_div::trimExplode(',',$flexData['data']['sDEF']['lDEF']['providers']['vDEF']);
			if(is_array($aProviders) && count($aProviders)) {
				foreach($aProviders as $aProvider) {
					if(!empty($aProvider)) {
						$providerClassName = t3lib_div::trimExplode('%23',$aProvider); //%23 = séparateur #
						foreach($classes as $class) {
							if(strpos($providerClassName[0],$class) !== false) {
								$providerObj = t3lib_div::makeInstance($class);
								$flexform = $providerObj->getFlexform($providerObj->conf);
								if ($flexform) {
									$flex.= '
<' . $class . '>
	<ROOT>
		<TCEforms>
			<sheetTitle>' . $subscribers[$class]['name'] . '</sheetTitle>
		</TCEforms>
		<type>array</type>';
									$flex .= $flexform;
									$flex .= '
	</ROOT>
</' . $class . '>';
								}
							}
						}
					}
				}
			}
			$conf['config']['ds']['ics_gmap3_pi1,list'] = str_replace('<!-- ###ADDITIONAL FLEX DATA PROVIDER### -->', $flex, file_get_contents(t3lib_div::getFileAbsFileName('EXT:ics_gmap3/flexform_ds_pi1.xml')));
		}
	}
	
	function emptyControl() {
		return '';
	}
}

?>