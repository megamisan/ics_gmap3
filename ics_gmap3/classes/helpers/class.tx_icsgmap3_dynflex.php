<?php

class tx_icsgmap3_dynflex {

	function getSingleField_preProcess($table, $field, & $row, $altName, $palette, $extra, $pal, &$tce)
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
					//$provider = t3lib_div::trimExplode('|',$aProvider);
					if(!empty($aProvider)) {
						$providerClassName = t3lib_div::trimExplode('%23',$aProvider); //%23 = séparateur #
						foreach($classes as $classe) {
							//$classNameSubcriber = t3lib_div::trimExplode('|',$classe);
							if(strpos($providerClassName[0],$classe) !== false) {
								$providerObj = new $classe();
								$flexform = $providerObj->getFlexform();
								
								$flex.= '
<' . $classe . '>
	<ROOT>
		<TCEforms>
			<sheetTitle>' . $subscribers[$classe]['name'] . '</sheetTitle>
		</TCEforms>
		<type>array</type>';
								$flex .= $flexform;
								$flex .= '
	</ROOT>
</' . $classe . '>';
							}
						}
					}
				}
			}
			$conf['config']['ds']['ics_gmap3_pi1,list'] = str_replace('<!-- ###ADDITIONAL FLEX DATA PROVIDER### -->', $flex, file_get_contents(t3lib_div::getFileAbsFileName('EXT:ics_gmap3/flexform_ds_pi1.xml')));
		}
	}
}

?>