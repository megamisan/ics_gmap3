<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 In Cité Solution <technique@in-cite.net>
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
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'Google map (v3)' for the 'ics_gmap3' extension.
 *
 * @author	In Cité Solution <technique@in-cite.net>
 * @package	TYPO3
 * @subpackage	tx_icsgmap3
 */
class tx_icsgmap3_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_icsgmap3_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_icsgmap3_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'ics_gmap3';	// The extension key.
	var $data          = array();
	var $dataInit      = array();
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj = 1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
	
		$this->init();

		$markers = array(
			'###MAP###' => $this->displayMap()
		);
		$content = $this->template2html($markers, '###TEMPLATE_CONTENT###');
		return $this->pi_wrapInBaseClass($content);
	}
	
	function init() {
		$this->pi_initPIflexForm();
		
		$this->lConf = array(); // Setup our storage array...
		$this->providers = array();
		$piFlexForm = $this->cObj->data['pi_flexform'];
		foreach ($piFlexForm['data']['sDEF'] as $lang => $value) {
			foreach ($value as $key => $val) {
				$this->lConf[$key] = $this->pi_getFFvalue($piFlexForm, $key, 'sDEF');
			}
		}
		
		$aProviders = t3lib_div::trimExplode(',',$this->lConf['providers']);
		
		if(is_array($aProviders) && count($aProviders)) {
			foreach($aProviders as $aProvider) {

				$provider = t3lib_div::makeInstance($aProvider);
				$subscribers = tx_icsgmap3_provider_manager::getSubscribers();

				if(!empty($piFlexForm['data'][$aProvider])) {
					foreach ($piFlexForm['data'][$aProvider] as $lang => $value) {
						foreach ($value as $key => $val) {
							$confProvider[$key] = $this->pi_getFFvalue($piFlexForm, $key, $aProvider);
						}
					}
				}
				
				if($provider->type == 'data') {
					if($subscribers[$aProvider]['data'] & tx_icsgmap3_provider_manager::DATA_NONE) {
						$this->data[] = null;
						//$this->dataInit[] = $provider->data;
					}
					elseif($subscribers[$aProvider]['data'] & tx_icsgmap3_provider_manager::DATA_STATIC) {
						$this->data[] = $provider->getStaticData(array_merge($confProvider,array('prefixId' => $this->prefixId)));
						//var_dump($this->data);
						//$this->dataInit[] = $provider->data;
					}
					elseif($subscribers[$aProvider]['data'] & tx_icsgmap3_provider_manager::DATA_DYNAMIC) {
						$this->data[] = $provider->getDynamicDataUrl($confProvider);
						//$this->dataInit[] = $provider->data;
					}
				}
				else {
					if($subscribers[$aProvider]['data'] & tx_icsgmap3_provider_manager::BEHAVIOUR_ADD) {
						$this->behaviourFunc[] = $provider->getBehaviourInitFunction($confProvider);
					}
				}
			}
		}
		
		$GLOBALS['TSFE']->additionalHeaderData[$this->extKey . '_scale' . $this->cObj->data['uid']] = '<meta name="viewport" content="initial-scale=1.0, user-scalable=no"/>';
		
		$this->incJsFile($jsCode, true, '_carto');
		
		$this->storage = $this->lConf['pages'];
		$this->mapId = $this->lConf['mapId'];
		$this->mapWidth = $this->lConf['width'];
		$this->mapHeight = $this->lConf['height'];
		$this->mapLng = $this->lConf['lng'];
		$this->mapLat = $this->lConf['lat'];
		$this->mapZoom = $this->lConf['zoom'];
		$this->mapTypeId = $this->lConf['type'];
		$this->mapTypeControl = $this->lConf['type_controls'] == "show"?"true":"false";
		$this->navigationControl = $this->lConf['nav_controls'] == "hide"?"false":"true";
		$this->navigationControl_id = $this->lConf['nav_controls'];
		$this->scrollwheel = $this->lConf['scrollwheel'] == "show"?"true":"false";
		$this->streetViewControl = $this->lConf['streetview_control'] == "show"?"true":"false";
		$this->windowsInfoFields = $this->lConf['windowsInfoFields'];
		
		$includeLibJS = t3lib_div::trimExplode(',', $this->lConf['includeLibJS'], 1);
		while (list(, $lib) = each($includeLibJS)) {
			$lib = (string)strtoupper(trim($lib));
			switch ($lib) {
				case 'GMAP_API':
					$this->incJsFile('http://maps.google.com/maps/api/js?sensor=false', false, '_gmap_api');
				break;
				case 'JQUERY_UI':
					$this->incJsFile(t3lib_extMgm::siteRelPath($this->extKey).'res/js/jquery-ui-1.8.12.custom.min.js', false, '_jquery_ui_js');
					$this->incCssFile(t3lib_extMgm::siteRelPath($this->extKey).'res/css/jquery-ui-1.8.12.custom.css','_jquery_ui_css');
				break;
				case 'GMAP3':
					$this->incJsFile(t3lib_extMgm::siteRelPath($this->extKey).'res/js/gmap3.min.js', false, '_gmap3');
				break;
				case 'JQUERY_LIB':
					$this->incJsFile('http://code.jquery.com/jquery-1.6.1.min.js', false, '_jquery_lib');
				break;
			}
		}
		
		if(!empty($this->lConf['templateFile'])) {
			$this->template = $this->cObj->fileResource($this->lConf['templateFile']);
		}
		else {
			$this->template = $this->cObj->fileResource($this->conf['template']);
		}
		
		$this->initJSData();

	}
	
	function initJSData() {
		$marker= array();

		//$GLOBALS['TSFE']->additionalHeaderData[] = '<script type="text/javascript" src="' . t3lib_extMgm::siteRelPath($this->extKey) . 'res/js/gmap3.js"></script>';
		//$GLOBALS['TSFE']->additionalHeaderData[] = '<script type="text/javascript" src="' . t3lib_extMgm::siteRelPath($this->extKey) . 'res/js/gmap3_provider_data.js"></script>';
		
		$jsCodeInitData = '

jQuery(function(){
	var gmap3 = new ics.Map();
	gmap3.setConf("' . $this->mapId . '",' . $this->mapLng . ',' . $this->mapLat . ',' . $this->mapZoom . ',' . $this->mapTypeId . ',' . $this->mapTypeControl . ',' . $this->navigationControl . ',' . $this->scrollwheel . ',' . $this->streetViewControl . ');
	gmap3.addStaticData(' . implode('',$this->data) . ');
	gmap3.addBehaviourInit(' . implode(',',$this->behaviourFunc) . ');
	gmap3.createMap();
	
});';
		$this->incJsFile($jsCodeInitData, true, '_carto_init');
	}
	
	function displayMap() {	

		$zoom = "";
		if($this->navigationControl_id != "hide") {
			$zoom  = "
						zoomControlOptions: {
							style: " . $this->navigationControl_id . "
						},";
		}
		
		$marker = array(
			'###MAP_ID###' => $this->mapId,
			'###MAP_HEIGHT###' => $this->mapHeight,
			'###MAP_WIDTH###' => !empty($this->mapWidth)?$this->mapWidth:'100%',
			'###MAP_LNG###' => $this->mapLng,
			'###MAP_LAT###' => $this->mapLat,
			'###MAP_ZOOM###' => $this->mapZoom,
			'###MAP_TYPEID###' => $this->mapTypeId,
			'###MAP_TYPECONTROL###' => $this->mapTypeControl,
			'###MAP_NAVIGATIONCONTROL###' => $this->navigationControl,
			'###ZOOM###' => $zoom,
			'###MAP_SCROLLWHEEL###' => $this->scrollwheel,
			'###MAP_STREETVIEWCONTROL###' => $this->streetViewControl,
			'###ID###' => $this->cObj->data['uid'],
			'###JSON_DATA###' => implode('',$this->data),
		);
		
		$map = $this->template2html($marker, '###DISPLAY_MAP###');
	
		return $map;
	}
	
	/**
	* Function to insert Javascript at Ext. Runtime
	*
	* @param string $script Input the Script Name to insert JS
	* @return
	*/
	function incJsFile($script,$jsCode = false, $suffix = '') {
		if(!$jsCode) {
			$js = '<script src="'.$script.'" type="text/javascript"><!-- //--></script>';
			//var_dump($script);
		}
		else
		{
			$js .= '<script type="text/javascript">
				'.$script.'
			</script>';
		}
		
		
		
		$GLOBALS['TSFE']->additionalHeaderData[$this->extKey . $suffix . $this->cObj->data['uid']] .= $js;
	}
	
	/**
	* Function to insert CSS
	*
	* @param string $cssFile Input the Css Name to insert JS
	* @return
	*/
	function incCssFile($cssFile, $suffix = '') {
		$css = '<link type="text/css" href="' . $cssFile . '" rel="stylesheet" />';
		$GLOBALS['TSFE']->additionalHeaderData[$this->extKey . $suffix . $this->cObj->data['uid']] .= $css;
	}
	
	/**
	* Replace an HTML template with data
	*
	* @param	string		path to the html template file
	* @param	string		zone to substitute
	* @param	array		marker array
	* @return	string		html code
	*/	
	function template2html($values, $subpart) {
		$mySubpart = $this->cObj->getSubpart($this->template, $subpart);
		return $this->cObj->substituteMarkerArray($mySubpart, $values);
	}	
	
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_gmap3/pi1/class.tx_icsgmap3_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ics_gmap3/pi1/class.tx_icsgmap3_pi1.php']);
}

?>