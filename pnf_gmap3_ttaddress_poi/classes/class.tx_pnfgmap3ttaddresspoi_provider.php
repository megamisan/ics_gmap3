<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Plan.Net <typo3@plan-net.fr>
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


class tx_pnfgmap3ttaddresspoi_provider extends tx_icsgmap3ttaddress_provider {
	
	var $extKey = 'pnf_gmap3_ttaddress_poi';
	var $data = array();
	var $conf = array();
	
	function tx_pnfgmap3ttaddresspoi_provider() {
		$this->uploadsPath = 'uploads/tx_icsgmap3ttaddress/';
		$this->flexform = file_get_contents(t3lib_div::getFileAbsFileName('EXT:pnf_gmap3_ttaddress_poi/flexform_ds.xml'));
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
				address.`tx_rggooglemap_lat` as lat,
				address.`tx_rggooglemap_lng` as lng,
				address.`address` as address,
				addressgroup.`image` as picto,
				addressgroup.`uid` as catId,
				addressgroup.`title` as catName,
				parentgroup.`uid` as catParent,
				parentgroup.`title` as catParentName';
				// addressgroup.`tx_icsgmap3ttaddress_picto_hover` as picto_hover,
				// addressgroup.`tx_icsgmap3ttaddress_picto_list` as picto_list,
				// addressgroup.`tx_icsgmap3ttaddress_picto_list_hover` as picto_list_hover,
				
		$query['FROM'] = '(`tt_address` address
			INNER JOIN `tx_rggooglemap_cat` addressgroup
				ON addressgroup.`uid` IN (address.`tx_rggooglemap_cat2`)
				AND addressgroup.`deleted` = 0
				AND addressgroup.`hidden` = 0
			) LEFT OUTER JOIN `tx_rggooglemap_cat` parentgroup 
				ON addressgroup.`parent_uid` = parentgroup.`uid` 
				AND parentgroup.`deleted` = 0 
				AND parentgroup.`hidden` = 0';
			// INNER JOIN `tx_rggooglemap_cat_mm` rel
				// ON address.`uid` = rel.`uid_local`
		
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
					if(strpos($category, 'tx_rggooglemap_cat') !== false) {
						$whereClauseCatId[] = $aCat[count($aCat)-1];
					}
				}
				$whereClauseCat[] = 'addressgroup.`uid` IN (' . implode(',', $whereClauseCatId).')';
			}
		}
		else {
			$param = t3lib_div::_GP($conf['prefixId']);
			if(!empty($param['category'])) {
				$whereClause[] = 'addressgroup.`uid` IN (' . $param['category'] . ')';		
			}
		}
		
		if(is_array($whereClauseCat) && count($whereClauseCat)) {
			$whereClause[] = '(' . implode(' OR ',$whereClauseCat) . ')';
		}		
		$query['WHERE'] = implode(' AND ', $whereClause);
		
		return $query;
	}
	
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
			$select ='`tx_rggooglemap_cat`.`title` as catName,
				`tx_rggooglemap_cat`.`uid` as catId,
				`tx_rggooglemap_cat`.`parent_uid` as catParent,
				`tx_rggooglemap_cat`.`image` as picto';
				// `tx_rggooglemap_cat`.`tx_icsgmap3ttaddress_picto_hover` as picto_hover,
				// `tx_rggooglemap_cat`.`tx_icsgmap3ttaddress_picto_list` as picto_list,
				// `tx_rggooglemap_cat`.`tx_icsgmap3ttaddress_picto_list_hover` as picto_list_hover
			$from = '`tx_rggooglemap_cat`';
			$where = '`tx_rggooglemap_cat`.uid IN (' . $parent . ')';
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
			// t3lib_div::loadTCA('tx_rggooglemap_cat');
			tslib_fe::includeTCA();
			$uploadfolder = 'uploads/tx_rggooglemap/'; // $GLOBALS['TCA']['tx_rggooglemap_cat']['columns']['image']['config']['uploadfolder'];
			// $uploadfolder_hover = $GLOBALS['TCA']['tx_rggooglemap_cat']['columns']['tx_icsgmap3ttaddress_picto_hover']['config']['uploadfolder'];
			// $uploadfolder_list = $GLOBALS['TCA']['tx_rggooglemap_cat']['columns']['tx_icsgmap3ttaddress_picto_list']['config']['uploadfolder'];
			// $uploadfolder_list_hover = $GLOBALS['TCA']['tx_rggooglemap_cat']['columns']['tx_icsgmap3ttaddress_picto_list_hover']['config']['uploadfolder'];
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
						// $row['picto_hover'] = $this->checkPicto($row, 'picto_hover');
						// $row['picto_list'] = $this->checkPicto($row, 'picto_list');
						// $row['picto_list_hover'] = $this->checkPicto($row, 'picto_list_hover');
						$address['icon'] = $row['picto'] ? '/' . $uploadfolder. '/' . $row['picto'] : '';
						// $address['icon_hover'] = $row['picto_hover'] ? '/' . $uploadfolder_hover. '/' . $row['picto_hover'] : '';
						// $address['icon_list'] = $row['picto_list'] ? '/' . $uploadfolder_list. '/' . $row['picto_list'] : '';
						// $address['icon_list_hover'] = $row['picto_list_hover'] ? '/' . $uploadfolder_list_hover. '/' . $row['picto_list_hover'] : '';
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
				// Control tx_rggooglemap_cat
				$groupFields = array();
				$groupFields[] = array('uid' => 'catId', 'title' => 'catName');
				$groupFields[] = array('uid' => 'catParent', 'title' => 'catParentName');
				foreach ($groupFields as $group) {
					if ($row[$group['uid']]) { 
						$cat = $this->getLanguageRecords(array(
							'SELECT' => '`tx_rggooglemap_cat`.`uid`, `tx_rggooglemap_cat`.`title`',
							'FROM' => '`tx_rggooglemap_cat`',
							'WHERE' => '1 AND `tx_rggooglemap_cat`.`deleted` = 0 AND `tx_rggooglemap_cat`.`hidden` = 0',
							'GROUPBY' => '',
							'ORDERBY' => '',
							'LIMIT' => '1',
						), 'tx_rggooglemap_cat', array('uid' => $row[$group['uid']], 'title' => $row[$group['title']]));
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