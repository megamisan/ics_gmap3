<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011-2017 Plan.Net France <typo3@plan-net.fr>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

namespace PlanNet\IcsGmap3\Taglist;

use PlanNet\IcsGmap3\IProvider;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class Provider
 * @package PlanNet\IcsGmap3\Taglist
 */
class Provider implements IProvider {

    /** @var ContentObjectRenderer */
    public $cObj;
    /** @var string */
    protected $flexform;
    /** @var string */
    protected $extKey = 'ics_gmap3';

    /**
     * Provider constructor.
     */
    public function __construct() {
        // Hook additional_flex
        $additional_flex = '';
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['PlanNet\IcsGmap3\Taglist\Provider']['additionalFlex'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['PlanNet\IcsGmap3\Taglist\Provider']['additionalFlex'] as $_classRef) {
                $_procObj = GeneralUtility::getUserObj($_classRef);
                $_procObj->additionalFlex($additional_flex);
            }
        }
        $this->flexform = str_replace(
            '<!-- ###ADDITIONAL FLEX### -->',
            $additional_flex,
            file_get_contents(
                GeneralUtility::getFileAbsFileName('EXT:' . $this->extKey . '/Configuration/FlexForms/flexform_ds_taglist.xml')));
    }

    /**
     * Initializes configuration.
     * @param array $conf Configuration
     * @return array
     */
    function initConf($conf) {
        $confTS = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_icsgmap3_pi1.']['tagList.'];
        if (is_array($confTS)) {
            foreach ($confTS as $key => $value) {
                if (!$conf[$key] && $value) {
                    $conf[$key] = $value;
                }
            }
        }
        // Hook change configuration
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['PlanNet\IcsGmap3\Taglist\Provider']['initConf'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['PlanNet\IcsGmap3\Taglist\Provider']['initConf'] as $_classRef) {
                $_procObj = GeneralUtility::getUserObj($_classRef);
                $_procObj->initConf($conf);
            }
        }
        return $conf;
    }

    /**
     * @param array $conf
     * @return null
     */
    public function getStaticData($conf) {
        return NULL;
    }

    /**
     * @param array $conf
     * @return null
     */
    public function getDynamicDataUrl($conf) {
        return NULL;
    }

    /**
     * @param array $conf
     * @return string
     */
    public function getBehaviourInitFunction($conf) {
        $conf = $this->initConf($conf);
        $this->incJsFile(
            ExtensionManagementUtility::siteRelPath($this->extKey) . 'Resources/js/gmap3_provider_taglist.js',
            FALSE, '_gmap3_provider_taglist');
        $jsCode = '
			function (map) { 
				var exclusivesTags = new Array();
				var hiddenTags = new Array();
				var defaultTags = new Array();';
        $exclusivesTags = explode(',', $conf['exclusivesTags']);
        if (is_array($exclusivesTags) && !empty($exclusivesTags)) {
            foreach ($exclusivesTags as $tag) {
                if ($tag) {
                    $jsCode .= '
				exclusivesTags.push(' . json_encode($tag) . ');';
                }
            }
        }
        $hiddenTags = explode(',', $conf['hiddenTags']);
        if (is_array($hiddenTags) && !empty($hiddenTags)) {
            foreach ($hiddenTags as $tag) {
                if ($tag) {
                    $jsCode .= '
				hiddenTags.push(' . json_encode($tag) . ');';
                }
            }
        }
        $defaultTags = explode(',', $conf['defaultTags']);
        if (is_array($defaultTags) && !empty($defaultTags)) {
            foreach ($defaultTags as $tag) {
                if ($tag) {
                    $jsCode .= '
				defaultTags.push(' . json_encode($tag) . ');';
                }
            }
        }
        $jsCode .= '
				(new ics.TagList()).init(map, exclusivesTags, hiddenTags, defaultTags, ' .
            json_encode($conf['defaultMapEmpty']) . ', ' . json_encode($conf['tagsSelector']) . ');
			}
		';
        return $jsCode;
    }

    /**
     * @return string
     */
    public function getFlexform() {
        return $this->flexform;
    }

    /**
     * Function to insert Javascript at Ext. Runtime
     *
     * @param string $script Input the Script Name to insert JS
     * @param bool $jsCode
     * @param string $suffix
     */
    function incJsFile($script, $jsCode = FALSE, $suffix = '') {
        if (!$jsCode) {
            $js = '<script src="' . htmlspecialchars($script) . '" type="text/javascript"><!-- //--></script>';
        } else {
            $js = '<script type="text/javascript">
				' . htmlspecialchars($script) . '
			</script>';
        }
        $GLOBALS['TSFE']->additionalHeaderData[$this->extKey . $suffix . $this->cObj->data['uid']] .= $js;
    }
}
