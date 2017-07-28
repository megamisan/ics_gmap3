<?php

namespace PlanNet\IcsGmap3;

use PlanNet\IcsGmap3\Provider\Manager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class Dynflex
 * @package PlanNet\IcsGmap3
 */
class Dynflex {

    /**
     * @param array|string $dataStructArray
     * @param array $conf
     * @param array $row
     * @param string $table
     * @param string $fieldName
     */
    public function getFlexFormDS_postProcessDS(&$dataStructArray, $conf, $row, $table, $fieldName) {
        if (!is_array($dataStructArray) || ($table != 'tt_content') || ($fieldName != 'pi_flexform') ||
            ($row['CType'] != 'list') || ($row['list_type'] != 'ics_gmap3_pi1')
        ) {
            return;
        }
        $flexData = (!empty($row['pi_flexform'])) ? (GeneralUtility::xml2array($row['pi_flexform'])) : (['data' => []]);
        $subscribers = Manager::getSubscribers();
        $classes = [];
        if (is_array($subscribers) && count($subscribers)) {
            $classes = array_keys($subscribers);
        }
        if (!empty($flexData['data']['sDEF']['lDEF']['providers']['vDEF'])) {
            $selectedProviders = GeneralUtility::trimExplode(',', $flexData['data']['sDEF']['lDEF']['providers']['vDEF'], TRUE);
            if (is_array($selectedProviders) && count($selectedProviders)) {
                foreach ($selectedProviders as $selectedProvider) {
                    list($providerClassNameForward) = GeneralUtility::trimExplode('%23', $selectedProvider, TRUE); //%23 = séparateur #
                    $providerClassName = str_replace('/', '\\', $providerClassNameForward);
                    if (in_array($providerClassName, $classes)) {
                        /** @var IProvider $providerObj */
                        $providerObj = GeneralUtility::makeInstance($providerClassName);
                        $flexform = $providerObj->getFlexform();
                        if ($flexform) {
                            $dataStructArray['sheets'][$providerClassNameForward] = [
                                'ROOT' => [
                                    'TCEforms' => [
                                        'sheetTitle' => $subscribers[$providerClassName]['name'],
                                    ],
                                    'el'       => GeneralUtility::xml2array($flexform),
                                ],
                            ];
                        }
                    }
                }
            }
        }
    }

    /**
     * Returns an empty string.
     * @return string
     */
    public function emptyControl() {
        return '';
    }
}
