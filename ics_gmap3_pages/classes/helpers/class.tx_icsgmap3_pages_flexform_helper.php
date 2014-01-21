<?php

class tx_icsgmap3pages_flexform_helper {
	function renderFields($config) {
		$optionList = array();
		if(!empty($GLOBALS['TCA']['pages']['feInterface']['fe_admin_fieldList'])) {
			$aFields = array_unique(t3lib_div::trimExplode(',', str_replace('_first', '', $GLOBALS['TCA']['pages']['feInterface']['fe_admin_fieldList'])));
			foreach($aFields as $field) {
				$fieldName = $GLOBALS['LANG']->sL($GLOBALS['TCA']['pages']['columns'][$field]['label']);
				$optionList[] = array(0 => strip_tags($fieldName), strip_tags($field));
			}
		}
		$config['items'] = array_merge($config['items'],$optionList);
		return $config;
	}
}

?>