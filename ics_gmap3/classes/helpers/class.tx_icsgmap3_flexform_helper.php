<?php

class tx_icsgmap3_flexform_helper {

	function renderProviderList($config) {
		$optionList = array();
		$subscribers = tx_icsgmap3_provider_manager::getSubscribers();
		if(is_array($subscribers) && count($subscribers)) {
			foreach($subscribers as $providerClassName => $provider) {
				$aProviderClassName = t3lib_div::trimExplode('|',$providerClassName);
				$providerName = $GLOBALS['LANG']->sL($provider['name']);
				$optionList[] = array(0 => $providerName, $providerClassName);
			}
		}
		$config['items'] = array_merge($config['items'],$optionList);
		return $config;
	}
	
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