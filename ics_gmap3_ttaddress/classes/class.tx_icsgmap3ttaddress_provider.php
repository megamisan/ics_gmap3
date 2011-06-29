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


class tx_icsgmap3ttaddress_provider implements tx_icsgmap3_iprovider {
	
	var $data = array();
	
	function tx_icsgmap3ttaddress_provider() {
		$this->uploadsPath = 'uploads/tx_icsgmap3ttaddress/';
		$this->type = 'data';
		$this->flexform = file_get_contents(t3lib_div::getFileAbsFileName('EXT:ics_gmap3_ttaddress/flexform_ds.xml'));
	}
	
	function getStaticData($conf) {
		$fields = '';
		$whereClauseCat = array();
		$tables = '`tt_address` address, `tt_address_group` addressgroup, `tt_address_group_mm` rel';
		if(!empty($conf['storagePid'])) {
			$aStorage = t3lib_div::trimExplode(',',$conf['storagePid'],true);
			if(is_array($aStorage) && count($aStorage)) {
				foreach($aStorage as $storage) {
					$storage = t3lib_div::trimExplode('_',$storage,true);
					if($storage[0] == 'pages') {
						$whereClause .= ' AND address.`pid` = ' . $storage[1] . '  AND addressgroup.`uid` = rel.`uid_foreign` AND address.`uid` = rel.`uid_local`  AND address.`deleted` = 0 AND address.`hidden` = 0  AND addressgroup.`deleted` = 0 AND addressgroup.`hidden` = 0  ';
					}
				}
			}
		}
		
		if(!empty($conf['category'])/* && $_SERVER['REMOTE_ADDR'] == '80.11.134.90'*/) {
			$aCategory = t3lib_div::trimExplode(',',$conf['category'],true);
			if(is_array($aCategory) && count($aCategory)) {
				foreach($aCategory as $category) {
					$aCat = t3lib_div::trimExplode('_',$category,true);
					if(strpos($category, 'tt_address_group') !== false) {
						$whereClauseCat[] = ' (addressgroup.`uid` = rel.`uid_foreign` AND address.`uid` = rel.`uid_local` AND addressgroup.`uid` = ' . $aCat[count($aCat)-1] . ' AND address.`deleted` = 0 AND address.`hidden` = 0  AND addressgroup.`deleted` = 0 AND addressgroup.`hidden` = 0) ';
					}
				}
			}
		}
		else {
			$param = t3lib_div::_GP($conf['prefixId']);
			if(!empty($param['category'])) {
				$whereClauseCat[] = ' (addressgroup.`uid` = rel.`uid_foreign` AND address.`uid` = rel.`uid_local` AND addressgroup.`uid` = ' . $param['category'] . ' AND address.`deleted` = 0 AND address.`hidden` = 0  AND addressgroup.`deleted` = 0 AND addressgroup.`hidden` = 0) ';
			}
		}
		
		if(is_array($whereClauseCat) && count($whereClauseCat)) {
			$whereClauseCat = ' AND (' . implode(' OR ',$whereClauseCat) . ')';
		}
		else {
			$whereClauseCat = '';
		}
		
		if(!empty($conf['windowsInfoFields'])) {
			 $fields = ',' . implode(',',t3lib_div::trimExplode(',',$conf['windowsInfoFields'],true));
		}
		
		
		$addresses = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				'address.`uid` as uid, address.`name` as name, address.`tx_icsgmap3ttaddress_coordinates` as coordinates, address.`address` as address, addressgroup.`tx_icsgmap3ttaddress_picto` as picto , addressgroup.`tx_icsgmap3ttaddress_pictothumb` as thumb , addressgroup.`uid` as catid, addressgroup.`title` as catName ' . $fields,
				$tables,
				'1 ' . $whereClause . ' ' . $whereClauseCat,
				'',
				'addressgroup.`uid`'
		);
		
		if(is_array($addresses) && count($addresses)) {
			foreach($addresses as $addresse) {
				$data[$addresse['catid']][] = $addresse;
			}
		}

		return $this->initTagsListJSon($data, implode(',',t3lib_div::trimExplode(',',$conf['windowsInfoFields'],true)), 'tt_address');
		//return $this->renderTagsListJSon($conf,$data);	
		//return $data;
	}
	
	function getDynamicDataUrl($conf) {
		return null;
	}
	
	function getBehaviourInitFunction($conf) {
		return null;
	}
	
	function getFlexform($conf) {
		return $this->flexform;
	}

	/*function renderTagsListJSon($conf, $data) {
		$this->template = $this->cObj->fileResource($conf['templateFile']);
		$address = array();
		$markers = array(
			'###ICON###' => '',
		);
		$contentJSon = array();
		
		if(is_array($data) && count($data)) {
			foreach($data as $cat => $tags) {
				foreach($tags as $row) {
					if(!empty($row['coordinates'])) {
						if(!empty($row['picto'])) {
							$markers['###ICON###'] = '
							options: {
								icon: new google.maps.MarkerImage( "' . $this->uploadsPath . $row['picto'] . '")
							},';
						}
					}
					$markers['###TAG_NAME###'] = str_replace(' ','_',strtolower($row['catName']));
				}
				$contentJSon[] = $this->template2html($markers, '###LIST_OF_TAGS###');
			}
		}
		$content = implode(',',$contentJSon);
		return $content;
	}*/
	
	function initTagsListJSon($data, $fields, $table) {
		if(is_array($data) && count($data)) {
			// t3lib_div::loadTCA('tx_icsgmap3ttaddress_picto');
			tslib_fe::includeTCA();
			$uploadfolder = $GLOBALS['TCA']['tt_address_group']['columns']['tx_icsgmap3ttaddress_picto']['config']['uploadfolder'];
			$jsCodeData = array();
			$jsCode = '[' . "\r\n";
			foreach($data as $cat => $tags) {
				foreach($tags as $row) {
					if(!empty($row['coordinates'])) {
						$coordinates = t3lib_div::trimExplode(',',$row['coordinates'],true);
						$address['lat'] = $coordinates[0];
						$address['lng'] = $coordinates[1];
						$address['tag'] = $row['catName'];
						$address['icon'] = $row['picto'] ? $uploadfolder. '/' . $row['picto'] : '';
						/*$address['data'] = array(
							'name' => $row['name'],
							'address' => $row['address'],
						);*/
						
						// à continuer
						$aFields = t3lib_div::trimExplode(',',$fields);
						foreach($aFields as $windowsInfoFields) {
							if(!is_null($row[$windowsInfoFields])) {
								$address['data'][$windowsInfoFields] = $row[$windowsInfoFields];
							}
							else {
								$address['data'][$windowsInfoFields] = '';
							}
						}
						
						$jsCodeData[] = json_encode($address);
					}
				}
			}
			$jsCode .= implode(',' . "\r\n",$jsCodeData);
			$jsCode .= ']';
		}
		//$conf['windowsInfoFields']
		//var_dump($data);
		return $jsCode;
	}
	
	function getData($json) {
		return $this->data;
	}
	
	/**
	* Function to insert Javascript at Ext. Runtime
	*
	* @param string $script Input the Script Name to insert JS
	* @return
	*/
	/*function incJsFile($script,$jsCode = false, $suffix = '') {
		if(!$jsCode)
			$js = '<script src="'.$script.'" type="text/javascript"><!-- //--></script>';
		else
		{
			$js .= '<script type="text/javascript">
				'.$script.'
			</script>';
		}
		$GLOBALS['TSFE']->additionalHeaderData[$this->extKey . $suffix . $this->cObj->data['uid']] .= $js;
	}*/
	
	/**
	* Replace an HTML template with data
	*
	* @param	string		path to the html template file
	* @param	string		zone to substitute
	* @param	array		marker array
	* @return	string		html code
	*/	
	/*function template2html($values, $subpart) {
		$mySubpart = $this->cObj->getSubpart($this->template, $subpart);
		return $this->cObj->substituteMarkerArray($mySubpart, $values);
	}	*/
	
}
?>