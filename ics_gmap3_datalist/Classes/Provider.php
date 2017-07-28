<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 In Cit� Solution <technique@in-cite.net>
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

namespace PlanNet\IcsGmap3Datalist;

use PlanNet\IcsGmap3\IProvider;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class Provider
 * @package PlanNet\IcsGmap3Datalist
 */
class Provider implements IProvider {

    protected $extKey = 'ics_gmap3_datalist';
    /** @var string */
    protected $flexform;
    /** @var ContentObjectRenderer */
    public $cObj;

    /**
     * Provider constructor.
     */
    function __construct() {
        $this->flexform = file_get_contents(GeneralUtility::getFileAbsFileName('EXT:' . $this->extKey . '/Configuration/FlexForms/flexform_datalist_ds.xml'));
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
        $this->incJsFile(ExtensionManagementUtility::siteRelPath($this->extKey) . 'Resources/Public/JavaScript/tx_icsgmap3datalist.js', FALSE, '_gmap3datalist_provider');
        $markerPerPage = $conf['markerperpage'] ? $conf['markerperpage'] : 0;
        $jsCode = '
			function (map) {
				(new ics.DataList()).init(map, ' . json_encode($markerPerPage) . ');
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
