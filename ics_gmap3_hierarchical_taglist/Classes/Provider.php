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

namespace PlanNet\IcsGmap3HierarchicalTaglist;

use PlanNet\IcsGmap3\IProvider;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class Provider
 * @package PlanNet\IcsGmap3HierarchicalTaglist
 */
class Provider implements IProvider {

    /** @var ContentObjectRenderer */
    public $cObj;
    protected $extKey = 'ics_gmap3_hierarchical_taglist';
    /** @var string */
    protected $flexform;

    /**
     * Provider constructor.
     */
    function __construct() {
        $this->flexform = file_get_contents(GeneralUtility::getFileAbsFileName('EXT:' . $this->extKey . '/Configuration/FlexForms/flexform_taglist_ds.xml'));
    }

    /**
     * @param array $conf
     * @return null
     */
    function getStaticData($conf) {
        return NULL;
    }

    /**
     * @param array $conf
     * @return null
     */
    function getDynamicDataUrl($conf) {
        return NULL;
    }

    /**
     * @param array $conf
     * @return string
     */
    function getBehaviourInitFunction($conf) {
        $this->incJsFile(ExtensionManagementUtility::siteRelPath($this->extKey) . 'Resources/Public/JavaScript/tx_icsgmap3hierarchicaltaglist.js', FALSE, '_gmap3hierarchicaltaglist_provider');
        $jsCode = '
			function (map) {
				var exclusivesTags = new Array();
				var hiddenTags = new Array();
				var defaultTags = new Array();
				var lang = ' . json_encode([
                'select'   => $GLOBALS['TSFE']->sL('LLL:EXT:ics_gmap3_hierarchical_taglist/Resources/Private/Language/locallang.xlf:selectTags'),
                'unselect' => $GLOBALS['TSFE']->sL('LLL:EXT:ics_gmap3_hierarchical_taglist/Resources/Private/Language/locallang.xlf:unselectTags'),
            ], JSON_FORCE_OBJECT) . ';';
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
        $s_selectedTags = GeneralUtility::_GP('selectedTags');
        if (!empty($s_selectedTags)) {
            $selectedTags = explode(',', $s_selectedTags);
            $jsCode .= '
				defaultTags = []';
            foreach ($selectedTags as $tag) {
                if ($tag) {
                    $jsCode .= '
				defaultTags.push(' . json_encode($tag) . ');';
                }
            }
        }
        $jsCode .= '
				(new ics.HierarchicalTagList()).init(map, exclusivesTags, hiddenTags, defaultTags, ' .
            json_encode(boolval($conf['defaultMapEmpty'])) . ', ' . json_encode($conf['separator']) . ', ' .
            json_encode(boolval($conf['checkOnParent'])) . ', ' . json_encode(boolval($conf['viewLinkSelectAll'])) . ', lang);
			}
		';
        return $jsCode;
    }

    /**
     * @return string
     */
    function getFlexform() {
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
