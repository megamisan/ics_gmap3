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

use PlanNet\IcsGmap3\Provider\Manager;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Plugin 'Google map (v3)' for the 'ics_gmap3' extension.
 *
 * @author    Plan.Net France <typo3@plan-net.fr>
 * @package    TYPO3
 * @subpackage    tx_icsgmap3
 */
class tx_icsgmap3_pi1 extends \TYPO3\CMS\Frontend\Plugin\AbstractPlugin {

    public $prefixId = 'tx_icsgmap3_pi1';        // Same as class name
    public $scriptRelPath = 'pi1/class.tx_icsgmap3_pi1.php';    // Path to this script relative to the extension dir.
    public $extKey = 'ics_gmap3';    // The extension key.
    protected $behaviourFunc;
    protected $providers;
    protected $lConf;
    protected $data = array();
    protected $dataInit = array();
    protected $context;

    /**
     * The main method of the PlugIn
     *
     * @param string $content The PlugIn content
     * @param array $conf The PlugIn configuration
     * @return string The content that is displayed on the website
     */
    function main($content, $conf) {
        $this->conf = $conf;
        $this->pi_setPiVarDefaults();
        $this->pi_loadLL();
        $this->pi_USER_INT_obj = 1;    // Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
        $this->init();
        $markers = array(
            '###MAP###'           => $this->displayMap(),
            '###ACCESSIBILITY###' => $this->displayAccessibility(),
        );
        $content = $this->template2html($markers, '###TEMPLATE_CONTENT###');
        return $this->pi_wrapInBaseClass($content);
    }

    /**
     * Init configuration
     */
    function init() {
        $this->pi_initPIflexForm();
        $this->lConf = array(); // Setup our storage array...
        $this->providers = array();
        $piFlexForm = $this->cObj->data['pi_flexform'];
        foreach ($piFlexForm['data']['sDEF'] as $lang => $value) {
            foreach ($value as $key => $val) {
                if (!is_null($this->pi_getFFvalue($piFlexForm, $key, 'sDEF')) && $this->pi_getFFvalue($piFlexForm, $key, 'sDEF') != '') {
                    $this->lConf[$key] = $this->pi_getFFvalue($piFlexForm, $key, 'sDEF');
                }
            }
        }
        $this->lConf = array_merge($this->conf, $this->lConf);
        $providers = GeneralUtility::trimExplode(',', $this->lConf['providers'], TRUE);
        if (is_array($providers) && count($providers) && !empty($providers[0])) {
            foreach ($providers as $providerClassNameForward) {
                $providerClassName = str_replace('/', '\\', $providerClassNameForward);
                /** @var PlanNet\IcsGmap3\IProvider $provider */
                $provider = GeneralUtility::makeInstance($providerClassName);
                $subscribers = Manager::getSubscribers();
                $confProvider = array();
                if (!empty($piFlexForm['data'][$providerClassNameForward])) {
                    foreach ($piFlexForm['data'][$providerClassNameForward] as $lang => $value) {
                        foreach ($value as $key => $val) {
                            if (trim($this->pi_getFFvalue($piFlexForm, $key, $providerClassNameForward)) != '') {
                                $confProvider[$key] = $this->pi_getFFvalue($piFlexForm, $key, $providerClassNameForward);
                            }
                        }
                    }
                }
                if (method_exists($provider, 'getConf')) {
                    $providerConf = $provider->getConf();
                    if (is_array($providerConf) && !empty($providerConf)) {
                        $confProvider = array_merge($providerConf, $confProvider);
                    }
                }
                if ($subscribers[$providerClassName]['data'] & Manager::DATA_STATIC) {
                    $this->data[] = $provider->getStaticData(array_merge($confProvider, array('prefixId' => $this->prefixId)));
                }
                if ($subscribers[$providerClassName]['data'] & Manager::DATA_DYNAMIC) {
                    $this->data[] = $provider->getDynamicDataUrl($confProvider);
                }
                if ($subscribers[$providerClassName]['data'] & Manager::BEHAVIOUR_ADD) {
                    $this->behaviourFunc[] = $provider->getBehaviourInitFunction(array_merge($confProvider, array('prefixId' => $this->prefixId)));
                }
            }
        }
        if ($this->lConf['disableUserScaling']) {
            $GLOBALS['TSFE']->additionalHeaderData[$this->extKey . '_scale' . $this->cObj->data['uid']] = '<meta name="viewport" content="initial-scale=1.0, user-scalable=no"/>';
        }
        $this->context = new stdClass();
        $this->context->storage = $this->lConf['pages'];
        $this->context->mapId = $this->lConf['mapId'];
        $this->context->mapWidth = intval($this->lConf['width']);
        $this->context->mapHeight = intval($this->lConf['height']);
        $this->context->mapLng = floatval($this->lConf['lng']);
        $this->context->mapLat = floatval($this->lConf['lat']);
        $this->context->mapZoom = intval($this->lConf['zoom']);
        $this->context->mapTypeId = $this->lConf['type'];
        $this->context->mapTypeControl = ($this->lConf['type_controls'] === "show");
        $this->context->navigationControl = ($this->lConf['nav_controls'] !== "hide");
        $this->context->navigationControl_id = $this->lConf['nav_controls'];
        $this->context->scrollwheel = ($this->lConf['scrollwheel'] === "show");
        $this->context->streetViewControl = ($this->lConf['streetview_control'] === "show");
        $this->context->windowsInfoFields = $this->lConf['windowsInfoFields'];
        $this->context->styledMap = $this->lConf['styledMap'];
        $includeLibJS = GeneralUtility::trimExplode(',', $this->lConf['includeLibJS'], 1);
        while (list(, $lib) = each($includeLibJS)) {
            $lib = (string)strtoupper(trim($lib));
            switch ($lib) {
                case 'GMAP_API':
                    $gmapParams = $this->conf['gmapParams'] ?: '';
                    if ($this->conf['apiKey']) {
                        $gmapParams .= '&key=' . rawurlencode($this->conf['apiKey']);
                    }
                    $this->incJsFile('http://maps.google.com/maps/api/js?' . $gmapParams, FALSE, '_gmap_api');
                    break;
                case 'JQUERY_UI':
                    $this->incJsFile(ExtensionManagementUtility::siteRelPath($this->extKey) . 'Resources/Public/JavaScript/jquery-ui-1.8.12.custom.min.js', FALSE, '_jquery_ui_js');
                    $this->incCssFile(ExtensionManagementUtility::siteRelPath($this->extKey) . 'Resources/Public/Css/jquery-ui-1.8.12.custom.css', '_jquery_ui_css');
                    break;
                case 'GMAP3':
                    $this->incJsFile(ExtensionManagementUtility::siteRelPath($this->extKey) . 'Resources/Public/JavaScript/gmap3.min.js', FALSE, '_gmap3');
                    break;
                case 'JQUERY_LIB':
                    $this->incJsFile('http://code.jquery.com/jquery-1.6.1.min.js', FALSE, '_jquery_lib');
                    break;
            }
        }
        if (!empty($this->lConf['templateFile'])) {
            $this->context->template = $this->cObj->fileResource($this->lConf['templateFile']);
        } else {
            $this->context->template = $this->cObj->fileResource($this->conf['template']);
        }
        $this->initJSData();
    }

    /**
     * Initialize javascript functions
     */
    function initJSData() {
        $jsCodeInitData = '

jQuery(function(){
	var gmap3 = new ics.Map();
	gmap3.setConf(' . json_encode($this->context->mapId) . ',' . json_encode($this->context->mapLng) . ',' .
            json_encode($this->context->mapLat) . ',' . json_encode($this->context->mapZoom) . ',' .
            $this->context->mapTypeId . ',' . json_encode($this->context->mapTypeControl) . ',' .
            json_encode($this->context->navigationControl) . ',' . json_encode($this->context->scrollwheel) . ',' .
            json_encode($this->context->streetViewControl) . ');';
        foreach ($this->data as $data) {
            $jsCodeInitData .= '
	gmap3.addStaticData(' . $data . ');';
        };
        foreach ($this->behaviourFunc as $bFunc) {
            $jsCodeInitData .= '
	gmap3.addBehaviourInit(' . $bFunc . ');';
        }
        $jsCodeInitData .= '
	gmap3.createMap();
	';
        if (!empty($this->context->styledMap)) {
            $jsCodeInitData .= '
	var stylers = [
' . $this->context->styledMap . '
];
	gmap3.setOptions({styles: stylers});;
	';
        }
        $jsCodeInitData .= '
	document.getElementById(' . json_encode($this->context->mapId) . ').map = gmap3;
});';
        $this->incJsFile($jsCodeInitData, TRUE, '_carto_init');
    }

    /**
     *    Render map html
     *
     * @return    string    html code
     */
    function displayMap() {
        $zoom = "";
        if ($this->context->navigationControl_id != "hide") {
            $zoom = "
						zoomControlOptions: {
							style: " . json_encode($this->context->navigationControl_id) . "
						},";
        }
        $marker = array(
            '###MAP_ID###'                => htmlspecialchars($this->context->mapId),
            '###MAP_HEIGHT###'            => !empty($this->context->mapHeight) ? $this->context->mapHeight . 'px' : '100%',
            '###MAP_WIDTH###'             => !empty($this->context->mapWidth) ? $this->context->mapWidth . 'px' : '100%',
            '###MAP_LNG###'               => htmlspecialchars($this->context->mapLng),
            '###MAP_LAT###'               => htmlspecialchars($this->context->mapLat),
            '###MAP_ZOOM###'              => htmlspecialchars($this->context->mapZoom),
            '###MAP_TYPEID###'            => htmlspecialchars($this->context->mapTypeId),
            '###MAP_TYPECONTROL###'       => htmlspecialchars($this->context->mapTypeControl),
            '###MAP_NAVIGATIONCONTROL###' => htmlspecialchars($this->context->navigationControl),
            '###ZOOM###'                  => $zoom,
            '###MAP_SCROLLWHEEL###'       => htmlspecialchars($this->context->scrollwheel),
            '###MAP_STREETVIEWCONTROL###' => htmlspecialchars($this->context->streetViewControl),
            '###ID###'                    => htmlspecialchars($this->cObj->data['uid']),
            '###JSON_DATA###'             => implode('', $this->data),
        );
        $map = $this->template2html($marker, '###DISPLAY_MAP###');
        return $map;
    }

    /**
     *    Render data list accessibility html
     *
     * @return    string    html code
     */
    function displayAccessibility() {
        $template = $this->cObj->getSubpart($this->context->template, '###ACCESSIBILITY_LIST###');
        $outputList = '';
        $markersArray = array(
            '###PREFIXID###' => $this->prefixId,
        );
        $templateListBase = $this->cObj->getSubpart($this->context->template, '###DATA_ITEM###');
        $fieldTemplate = $this->cObj->getSubpart($templateListBase, '###FIELDS###');
        foreach ($this->data as $data) {
            $data = json_decode($data);
            if (is_array($data) && count($data)) {
                foreach ($data as $marker) {
                    $outputData = '';
                    foreach ((array)$marker->data as $name => $value) {
                        if (is_object($value)) {
                            continue;
                        }
                        $markerArrayField = array(
                            '###FIELD_NAME###'  => (string)$name,
                            '###FIELD_VALUE###' => (string)$value,
                        );
                        $outputData .= $this->cObj->substituteMarkerArray($fieldTemplate, $markerArrayField);
                    }
                    $templateList = $this->cObj->substituteSubpart($templateListBase, '###FIELDS###', $outputData);
                    $outputList .= $this->cObj->substituteMarkerArray($templateList, $markersArray);
                }
            }
        }
        $subpartsArray = array(
            '###DATA_ITEM###' => $outputList,
        );
        // Hook
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalAccessibilityMarkers'])) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['additionalAccessibilityMarkers'] as $_classRef) {
                $_procObj = GeneralUtility::getUserObj($_classRef);
                $_procObj->additionalAccessibilityMarkers($template, $markersArray, $subpartsArray, $this);
            }
        }
        $content = $this->cObj->substituteMarkerArrayCached($template, $markersArray, $subpartsArray);
        return $content;
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
            $js = '<script src="' . $script . '" type="text/javascript"><!-- //--></script>';
        } else {
            if (!empty($script)) {
                $js = '<script type="text/javascript">//<![CDATA[
					' . $script . '
				//]]></script>';
            }
        }
        if (isset($js)) {
            $GLOBALS['TSFE']->additionalHeaderData[$this->extKey . $suffix . $this->cObj->data['uid']] .= $js;
        }
    }

    /**
     * Function to insert CSS
     *
     * @param string $cssFile Input the Css Name to insert JS
     * @param string $suffix
     */
    function incCssFile($cssFile, $suffix = '') {
        $css = '<link type="text/css" href="' . $cssFile . '" rel="stylesheet" />';
        $GLOBALS['TSFE']->additionalHeaderData[$this->extKey . $suffix . $this->cObj->data['uid']] .= $css;
    }

    /**
     * Replace an HTML template with data
     * @param array $values
     * @param string $subpart
     * @return string
     */
    function template2html($values, $subpart) {
        $mySubpart = $this->cObj->getSubpart($this->context->template, $subpart);
        return $this->cObj->substituteMarkerArray($mySubpart, $values);
    }
}
