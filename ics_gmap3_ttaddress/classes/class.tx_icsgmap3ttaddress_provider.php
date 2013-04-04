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
	
	var $extKey = 'ics_gmap3_ttaddress';
	var $data = array();
	var $conf = array();
	
	function tx_icsgmap3ttaddress_provider() {
		$this->uploadsPath = 'uploads/tx_icsgmap3ttaddress/';
		$this->flexform = file_get_contents(t3lib_div::getFileAbsFileName('EXT:ics_gmap3_ttaddress/flexform_ds.xml'));
		$this->conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_icsgmap3ttaddress.'];
	}
	
	function makeQuery($conf) {
		$query = array(
			'SELECT' => '',
			'FROM' => '',
			'WHERE' => '',
			'GROUPBY' => '',
			'ORDERBY' => '',
			'LIMIT' => '',
		);
		$query['SELECT'] = 'address.`uid` as uid, address.`name` as name,
				address.`tx_icsgmap3ttaddress_lat` as lat,
				address.`tx_icsgmap3ttaddress_lng` as lng,
				address.`address` as address,
				addressgroup.`tx_icsgmap3ttaddress_picto` as picto,
				addressgroup.`tx_icsgmap3ttaddress_picto_hover` as picto_hover,
				addressgroup.`tx_icsgmap3ttaddress_picto_list` as picto_list,
				addressgroup.`tx_icsgmap3ttaddress_picto_list_hover` as picto_list_hover,
				addressgroup.`uid` as catId,
				addressgroup.`title` as catName,
				parentgroup.`uid` as catParent,
				parentgroup.`title` as catParentName';
				
		$query['FROM'] = '(`tt_address` address
			INNER JOIN `tt_address_group_mm` rel
				ON address.`uid` = rel.`uid_local`
			INNER JOIN `tt_address_group` addressgroup
				ON addressgroup.`uid` = rel.`uid_foreign`
				AND addressgroup.`deleted` = 0
				AND addressgroup.`hidden` = 0
			) LEFT OUTER JOIN `tt_address_group` parentgroup 
				ON addressgroup.`parent_group` = parentgroup.`uid` 
				AND parentgroup.`deleted` = 0 
				AND parentgroup.`hidden` = 0';
		
		$whereClause = array();
		$whereClause[] = 'address.`deleted` = 0';
		$whereClause[] = 'address.`hidden` = 0';
		
		if(!empty($conf['windowsInfoFields'])) {
			$windowsInfoFields = t3lib_div::trimExplode(',',$conf['windowsInfoFields'],true);
			
			if (in_array('tx_damttaddress_dam_image', $windowsInfoFields)) {
				$query['FROM'] = '(
					(' . $query['FROM'] . ')
					LEFT OUTER JOIN `tx_dam_mm_ref`
						ON `tx_dam_mm_ref`.`uid_foreign` = address.`uid`
						AND `tx_dam_mm_ref`.`tablenames` = \'tt_address\'
						AND `tx_dam_mm_ref`.`ident` = \'tx_damttaddress_dam_image\'
					) LEFT OUTER JOIN `tx_dam`
						ON `tx_dam`.`uid` = `tx_dam_mm_ref`.`uid_local`
						AND `tx_dam`.`file_mime_type` = \'image\'
						AND `tx_dam`.`deleted` = 0
						AND `tx_dam`.`hidden` = 0';
						
				$query['SELECT'] .= ',
					`tx_dam`.`file_path` as image_path,
					`tx_dam`.`file_name` as image_name
				';
			}
			
			$windowsInfoFields = array_map(
				create_function('$field', 'return \'address.`\' . $field . \'`\';'), 
				$windowsInfoFields);
			$query['SELECT'] .= ',' . implode(',', $windowsInfoFields);
		}
							
		
						
		if(!empty($conf['storagePid'])) {
			$aStorage = t3lib_div::trimExplode(',',$conf['storagePid'],true);
			if(is_array($aStorage) && count($aStorage)) {
				$storagePid = array();
				foreach($aStorage as $storage) {
					$storage = t3lib_div::trimExplode('_',$storage,true);
					if($storage[0] == 'pages') {
						$storagePid[] = intval($storage[1]);
					}
					else {
						$storagePid[] = intval($storage[0]);
					}
				}
				/* Multiple storage folder */
				$whereClause[] = 'address.`pid` IN (' . implode(',', $storagePid) . ')';				
			}
		}
		
		if(!empty($conf['category'])) {
			$aCategory = t3lib_div::trimExplode(',',$conf['category'],true);
			if(is_array($aCategory) && count($aCategory)) {
				foreach($aCategory as $category) {
					$aCat = t3lib_div::trimExplode('_',$category,true);
					if(strpos($category, 'tt_address_group') !== false) {
						$whereClause[] = 'addressgroup.`uid` = ' . $aCat[count($aCat)-1];
					}
				}
			}
		}
		else {
			$param = t3lib_div::_GP($conf['prefixId']);
			if(!empty($param['category'])) {
				$whereClause[] = 'addressgroup.`uid` = ' . $param['category'];		
			}
		}
		
		if(is_array($whereClauseCat) && count($whereClauseCat)) {
			$whereClause[] = '(' . implode(' OR ',$whereClauseCat) . ')';
		}		
		$query['WHERE'] = implode(' AND ', $whereClause);
		
		return $query;
	}
	
	function getStaticData($conf) {
		$this->delimiter = $conf['separator'];
		
		$queryArray = $this->makeQuery($conf);
		// Hook 
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['staticDataQuery'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['staticDataQuery'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$queryArray = $_procObj->staticDataQuery($queryArray, $conf, $this);
			}
		}
		
		$addresses = $this->getLanguageRecords($queryArray);
		if (is_array($addresses)) {			
			foreach ($addresses as $address) {
				$data[$address['catId']][] = $address;
			}
		}
		
		return $this->initTagsListJSon($data, implode(',',t3lib_div::trimExplode(',',$conf['windowsInfoFields'],true)), 'tt_address', $conf['withPath']);
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
	
	private function checkPicto($row, $field = 'picto') {
		$picto = $row[$field];
		if (empty($row[$field])) {
			$picto = $this->getParentPicto($row, $field);
		}
		return $picto;
	}
	
	private function getParentPicto($row, $field = 'picto') {
		$parent = strtok($row['catParent'], ',');
		$picto = '';
		if (!empty($this->categories[$parent][$field])) {
			$picto = $this->categories[$parent][$field];
		}else{
			if(!empty($this->categories[$parent]['catParent'])) {
				$picto = $this->getParentPicto($this->categories[$parent]);
			}
		}
		return $picto;
	}
	
	function resolvPath($id, $name, $parent) {
		if (empty($parent))
			return $name;
		$path = $name;
		$parent = strtok($parent, ',');
		if (!isset($this->categories[$parent])) {		
			$select ='`tt_address_group`.`title` as catName,
				`tt_address_group`.`uid` as catId,
				`tt_address_group`.`parent_group` as catParent,
				`tt_address_group`.`tx_icsgmap3ttaddress_picto` as picto,
				`tt_address_group`.`tx_icsgmap3ttaddress_picto_hover` as picto_hover,
				`tt_address_group`.`tx_icsgmap3ttaddress_picto_list` as picto_list,
				`tt_address_group`.`tx_icsgmap3ttaddress_picto_list_hover` as picto_list_hover';
			$from = '`tt_address_group`';
			$where = '`tt_address_group`.uid IN (' . $parent . ')';
			$tmp = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows($select,$from,$where);
			if (!empty($tmp))
				$this->categories[$parent] = $tmp[0];
			else
				$this->categories[$parent] = false;
		}
		if (!empty($this->categories[$parent])) {
			$path = $this->categories[$parent]['catName'] . $this->delimiter . $path;
			$path = $this->resolvPath($this->categories[$parent]['catId'], $path, $this->categories[$parent]['catParent']);
		}
		return $path;
	}
	
	function initTagsListJSon($data, $fields, $table, $path) {
		if(is_array($data) && count($data)) {
			// t3lib_div::loadTCA('tx_icsgmap3ttaddress_picto');
			tslib_fe::includeTCA();
			$uploadfolder = $GLOBALS['TCA']['tt_address_group']['columns']['tx_icsgmap3ttaddress_picto']['config']['uploadfolder'];
			$uploadfolder_hover = $GLOBALS['TCA']['tt_address_group']['columns']['tx_icsgmap3ttaddress_picto_hover']['config']['uploadfolder'];
			$uploadfolder_list = $GLOBALS['TCA']['tt_address_group']['columns']['tx_icsgmap3ttaddress_picto_list']['config']['uploadfolder'];
			$uploadfolder_list_hover = $GLOBALS['TCA']['tt_address_group']['columns']['tx_icsgmap3ttaddress_picto_list_hover']['config']['uploadfolder'];
			$imgTS = $this->conf['tooltip.']['image.'];
			$cObj = t3lib_div::makeInstance('tslib_cObj');
			$jsCodeData = array();
			$jsCode = '[' . "\r\n";
			$aFields = t3lib_div::trimExplode(',',$fields);
			foreach($data as $cat => $tags) {
				foreach($tags as $row) {
					if($row['lat'] && $row['lng']
						&& $row['lng'] != '0.000000000'
						&& $row['lat'] != '0.00000000') {
						$address['lat'] = $row['lat'];
						$address['lng'] = $row['lng'];
						$address['tag'] = $path ? $this->resolvPath($row['catId'], $row['catName'], $row['catParent']) : $row['catName'];
						$row['picto'] = $this->checkPicto($row);
						$row['picto_hover'] = $this->checkPicto($row, 'picto_hover');
						$row['picto_list'] = $this->checkPicto($row, 'picto_list');
						$row['picto_list_hover'] = $this->checkPicto($row, 'picto_list_hover');
						$address['icon'] = $row['picto'] ? '/' . $uploadfolder. '/' . $row['picto'] : '';
						$address['icon_hover'] = $row['picto_hover'] ? '/' . $uploadfolder_hover. '/' . $row['picto_hover'] : '';
						$address['icon_list'] = $row['picto_list'] ? '/' . $uploadfolder_list. '/' . $row['picto_list'] : '';
						$address['icon_list_hover'] = $row['picto_list_hover'] ? '/' . $uploadfolder_list_hover. '/' . $row['picto_list_hover'] : '';
						/*$address['data'] = array(
							'name' => $row['name'],
							'address' => $row['address'],
						);*/
						
						// à continuer
						foreach($aFields as $windowsInfoFields) {
							if(!is_null($row[$windowsInfoFields])) {
								$address['data'][$windowsInfoFields] = $row[$windowsInfoFields];
							}
							else {
								$address['data'][$windowsInfoFields] = '';
							}
						}
						$address['data']['tx_damttaddress_dam_image'] = '';
						if (in_array('tx_damttaddress_dam_image', $aFields) && $row['image_path']) {
							$imgTS['file'] = $row['image_path'] . $row['image_name'];
							$image = $cObj->IMG_RESOURCE( $imgTS );
							if ($image) 
								$address['data']['tx_damttaddress_dam_image'] = $image;
							else
								$address['data']['tx_damttaddress_dam_image'] = $imgTS['file'];
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
	
	/**
	 * get translate record, if exist, each field are merged
	 *
	 * @param	string	$table: database tablename
	 * @param	array	$rows: records to translate
	 * @return 	array
	 */	
	function getLanguageRecords($queryArray, $table = 'address', $record = null) {	
		if (!$record) {
			$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
				$queryArray['SELECT'], 
				$queryArray['FROM'],
				$queryArray['WHERE'] . ' AND ' . $table . '.`sys_language_uid` = 0',
				$queryArray['GROUPBY'],
				$queryArray['ORDER'],
				$queryArray['LIMIT']
			);
		} else {
			$rows = array(0 => $record);
		}
		
		if ($GLOBALS['TSFE']->sys_language_content && is_array($rows)) {
			foreach ($rows as &$row) {
				// $OLmode = ($this->sys_language_mode == 'strict' ? 'hideNonTranslated' : '');
				// $row = $GLOBALS['TSFE']->sys_page->getRecordOverlay($table, $row, $GLOBALS['TSFE']->sys_language_content, '');
				$where = $queryArray['WHERE'] . ' AND ' . $table . '.`l18n_parent` = ' . $row['uid'] . '  AND ' . $table . '.`sys_language_uid` = ' . $GLOBALS['TSFE']->sys_language_content;
				$records = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
					$queryArray['SELECT'], 
					$queryArray['FROM'], 
					$where,
					'',
					'',
					1
				);
				if (is_array($records) && !empty($records)) {
					$translate = $records[0];
					foreach ($row as $field => &$value) {
						switch ($field) {
							case 'uid':
							case 'catId':
							case 'catName':
							case 'catParent':
							case 'catParentName':
								// Nothing
							break;
							default: 
								if ($translate[$field])
									$row[$field] = $translate[$field];
							break;
						}
					}
				}
				// Control tt_address_group
				$groupFields = array();
				$groupFields[] = array('uid' => 'catId', 'title' => 'catName');
				$groupFields[] = array('uid' => 'catParent', 'title' => 'catParentName');
				foreach ($groupFields as $group) {
					if ($row[$group['uid']]) { 
						$cat = $this->getLanguageRecords(array(
							'SELECT' => '`tt_address_group`.`uid`, `tt_address_group`.`title`',
							'FROM' => '`tt_address_group`',
							'WHERE' => '1 AND `tt_address_group`.`deleted` = 0 AND `tt_address_group`.`hidden` = 0',
							'GROUPBY' => '',
							'ORDERBY' => '',
							'LIMIT' => '1',
						), 'tt_address_group', array('uid' => $row[$group['uid']], 'title' => $row[$group['title']]));
						if (is_array($cat) && !empty($cat)) {
							$row[$group['uid']] = $cat[0]['uid'];
							$row[$group['title']] = $cat[0]['title'];
						}
					}
				}
			}
		} 
		return $rows;
	}
}
?>