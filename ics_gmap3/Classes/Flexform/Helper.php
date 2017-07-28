<?php

namespace PlanNet\IcsGmap3\Flexform;

use PlanNet\IcsGmap3\Provider\Manager;

/**
 * Class Helper
 * @package PlanNet\IcsGmap3\Flexform
 */
class Helper {

    /**
     * Fills select items array with providers.
     * @param array $config
     */
    public function renderProviderList($config) {
        $optionList = array();
        $subscribers = Manager::getSubscribers();
        if (is_array($subscribers) && count($subscribers)) {
            foreach ($subscribers as $providerClassName => $provider) {
                $providerName = $GLOBALS['LANG']->sL($provider['name']);
                $optionList[] = array($providerName, str_replace('\\', '/', $providerClassName));
            }
        }
        $config['items'] = array_merge($config['items'], $optionList);
    }

    /**
     * Fills select items with tt_address used fields.
     * @param array $config
     */
    public function renderFields($config) {
        $optionList = array();
        $aFields = array_keys($GLOBALS['TCA']['tt_address']['columns']);
        foreach ($aFields as $field) {
            $fieldName = $GLOBALS['LANG']->sL($GLOBALS['TCA']['tt_address']['columns'][$field]['label']);
            $fieldName = $fieldName ? sprintf('%s (%s)', $fieldName, $field) : sprintf('[%s]', $field);
            $optionList[] = array($fieldName, $field);
        }
        uasort($optionList, function ($v1, $v2) {
            return strcmp($v1[0], $v2[0]);
        });
        $config['items'] = array_merge($config['items'], $optionList);
    }
}
