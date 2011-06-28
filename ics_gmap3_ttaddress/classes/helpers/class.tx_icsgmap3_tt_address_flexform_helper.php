<?php

class tx_icsgmap3ttaddress_flexform_helper {
	function renderFields($config) {
		$optionList = array();
		if(!empty($GLOBALS['TCA']['tt_address']['interface']['showRecordFieldList'])) {
			$aFields = t3lib_div::trimExplode(',',$GLOBALS['TCA']['tt_address']['interface']['showRecordFieldList']);
			foreach($aFields as $field) {
				$fieldName = $GLOBALS['LANG']->sL($GLOBALS['TCA']['tt_address']['columns'][$field]['label']);
				$optionList[] = array(0 => $fieldName, $field);
			}
		}
		$config['items'] = array_merge($config['items'],$optionList);
		return $config;
	}
}

?>