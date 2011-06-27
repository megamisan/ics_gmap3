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
}

?>