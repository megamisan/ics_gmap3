<?php

class tx_icsgmap3ttaddress_flexform_helper {
	function renderFields($config) {
		$optionList = array();
		if(!empty($GLOBALS['TCA']['tt_address']['feInterface']['fe_admin_fieldList'])) {
			$aFields = array_unique(t3lib_div::trimExplode(',', str_replace('_first', '', $GLOBALS['TCA']['tt_address']['feInterface']['fe_admin_fieldList'])));
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