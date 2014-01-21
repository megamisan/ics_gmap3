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


class tx_icsgmap3pages_provider implements tx_icsgmap3_iprovider {
	
	var $extKey = 'ics_gmap3_pages';
	var $data = array();
	var $conf = array();
	
	function tx_icsgmap3pages_provider() {
		$this->uploadsPath = 'uploads/tx_icsgmap3pages/';
		$this->flexform = file_get_contents(t3lib_div::getFileAbsFileName('EXT:ics_gmap3_pages/flexform_ds.xml'));
		$this->conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_icsgmap3pages.'];
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
		$query['SELECT'] = 'p.uid as uid,p.title as title, 
							p.tx_icsgmap3pages_lat as lat, p.tx_icsgmap3pages_lng as lng,
							l.picto as picto,
							l.uid as catId,
							l.title as catName,
							l.parent as catParent';
				
		$query['FROM'] = 'pages as p LEFT JOIN tx_icsgmap3levels_levels as l ON l.uid = p.tx_icsgmap3pages_level';
		
		$whereClause = array();
		$whereClause[] = 'p.deleted = 0';
		$whereClause[] = 'p.hidden = 0';
		$whereClause[] = 'p.tx_icsgmap3pages_lat <> ""';
		$whereClause[] = 'p.tx_icsgmap3pages_lng <> ""';

		//window field
		if(!empty($conf['windowsInfoFields'])) {
			$windowsInfoFields = t3lib_div::trimExplode(',',$conf['windowsInfoFields'],true);
			
			$windowsInfoFields = array_map(
				create_function('$field', 'return \'p.`\' . $field . \'`\';'), 
				$windowsInfoFields);
			$query['SELECT'] .= ',' . implode(',', $windowsInfoFields);
		}

		if(!empty($conf['category'])) {
			$aCategory = t3lib_div::trimExplode(',',$conf['category'],true);
			if(is_array($aCategory) && count($aCategory)) {
				$whereClause[] = 'l.uid IN (' . implode(',', $aCategory) . ')';	
			}
		}
		else {
			$param = t3lib_div::_GP($conf['prefixId']);
			if(!empty($param['category'])) {
				$whereClause[] = 'l.uid = ' . $param['category'];		
			}
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
		
		$res = $GLOBALS['TYPO3_DB']->exec_SELECT_queryArray($queryArray);	
		$data = array();		
		while ($service = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$data[] = $service;
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($res);
		
		return $this->initTagsListJSon($data, implode(',',t3lib_div::trimExplode(',',$conf['windowsInfoFields'],true)), 'pages', $conf['withPath']);
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
	
	private function checkPicto($row) {
		$picto = $row['picto'];
		if (empty($row['picto'])) {
			$picto = $this->getParentPicto($row);
		}
		return $picto;
	}

	function resolveParentCategory($id,$child){
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('title','tx_icsgmap3levels_levels','1=1 AND uid='.$id);
		if($res) {
            $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
            $output = $row['title'].'#-#'.$child;
        }
        $GLOBALS['TYPO3_DB']->sql_free_result($res);
        return $output;
	}
	
	function initTagsListJSon($data, $fields, $table, $path) {
		if(is_array($data) && count($data)) {
			$jsCodeData = array();
			$jsCodeFinal = array();
			$elements = array();
			$jsCode = '[' . "\r\n";
			$aFields = t3lib_div::trimExplode(',',$fields);
			foreach($data as $row) {
				$jsCodeElements = array();
				$address = array();
				if($row['lat'] && $row['lng']
					&& $row['lng'] != '0.000000000'
					&& $row['lat'] != '0.00000000') {
					$address['lat'] = str_replace( ',' , '.' ,$row['lat']);
					$address['lng'] = str_replace( ',' , '.' ,$row['lng']);
					$address['icon'] = ($row['picto']) ? $this->uploadsPath.$row['picto'] : 'typo3conf/ext/ics_gmap3_pages/res/picto.png';
					$address['tag'] = ($row['catParent']) ? $this->resolveParentCategory($row['catParent'],$row['catName']) : $row['catName'];

					//data compilated
					$address['data']['title'] = $row['title'];
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
			$jsCode .= implode(',' . "\r\n",$jsCodeData);
			$jsCode .= ']';
			return $jsCode;
		}
	}
	
	function getData($json) {
		return $this->data;
	}

	/**
	 * [getImage description]
	 * @param  [type] $image_conf [description]
	 * @return string
	 */
	protected function getImage(&$image_conf){
		//CREATION DE L'IMAGE
		$imgTSConfig = Array();
		$path = ($image_conf['subPath']) ? $this->uploadsPath.$image_conf['subPath'] : $this->uploadsPath;
		$imgTSConfig['file'] = $path.$image_conf['image'];
		$imgTSConfig['file.']['maxW'] = $image_conf['width'];
		return $this->cObj->IMG_RESOURCE($imgTSConfig);
	}
}
?>