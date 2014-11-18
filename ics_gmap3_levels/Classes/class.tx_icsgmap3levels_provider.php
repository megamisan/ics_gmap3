<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 In Cité Solution <technique@in-cite.net>
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


class tx_icsgmap3levels_provider implements tx_icsgmap3_iprovider {

	var $extKey = 'ics_gmap3_levels';
	var $providerName = 'tx_icsgmap3levels_provider';

	function __construct() {
		// $this->uploadsPath = 'uploads/tx_cg41gmap3/';
		// $this->conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_cg41gmap3.'];

		// //load js & css
		// $css = '<link rel="stylesheet" type="text/css" href="/typo3conf/ext/cg41_gmap3/res/css/simple-slider.css" media="all" />'. "\n";
		// $js = '<script type="text/javascript" src="/typo3conf/ext/cg41_gmap3/res/gmap3_proximity.js"></script>'. "\n";
		// $js .= '<script type="text/javascript" src="/typo3conf/ext/cg41_gmap3/res/simple-slider.min.js"></script>'. "\n";
		// $GLOBALS['TSFE']->additionalHeaderData[$this->providerName ] = $css . $js;
	}

	function getStaticData($conf) {
		return null;	
	}
	
	function getDynamicDataUrl($conf) {
		return null;
	}
	
	function getFlexform($conf) {
		return '';
	}
	
	function makeQuery($conf, $kml = 1) {
		$query = array(
			'SELECT' => '',
			'FROM' => '',
			'WHERE' => '',
			'GROUPBY' => '',
			'ORDERBY' => '',
			'LIMIT' => '',
		);
		$query['SELECT'] = 'levels.`uid`,
				levels.`title`,
				levels.`parent`,
				levels.`zoom`,
				levels.`kml`';
				
		$query['FROM'] = '`tx_icsgmap3levels_levels` levels';
		
		$whereClause = array();
		$whereClause[] = 'levels.`deleted` = 0';
		$whereClause[] = 'levels.`hidden` = 0';
		if($kml)
			$whereClause[] = 'levels.`kml` != ""';
		
		// On récupére les conf flexform pour réduire aux catégories sélectionnées. Cette partie est à refaire proprement
		if ($conf['category']) {
			$whereClause[] = 'levels.`uid` IN (' . $conf['category'] . ')';
		}
		// *****
		
		$query['WHERE'] = implode(' AND ', $whereClause);
		$query['ORDERBY'] = 'levels.`sorting`';

		return $query;
	}
	
	function getBehaviourInitFunction($conf) {
		$this->conf['separator'] = $conf['separator'];
		$this->incJsFile(t3lib_extMgm::siteRelPath($this->extKey).'res/tx_icsgmap3_levels.js', false, '_gmap3levels_provider');
		$queryArray = $this->makeQuery($conf);
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			$queryArray['SELECT'], 
			$queryArray['FROM'],
			$queryArray['WHERE'],
			$queryArray['GROUPBY'],
			$queryArray['ORDER'],
			$queryArray['LIMIT']
		);
		// Kmls
		$kmls = array();
		if (is_array($rows) && !empty($rows)) {
			foreach ($rows as $row) {
				$path = $this->resolvPath($row['uid'], $row['title'], $row['parent']);
				$path = addslashes($path);
				$kmls[$path] = addslashes($row['kml']);
			}
		}
		
		// Zooms
		$queryArray = $this->makeQuery($conf, 0);
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			$queryArray['SELECT'], 
			$queryArray['FROM'],
			$queryArray['WHERE'],
			$queryArray['GROUPBY'],
			$queryArray['ORDER'],
			$queryArray['LIMIT']
		);
		$zooms = array();
		if (is_array($rows) && !empty($rows)) {
			foreach ($rows as $row) {
				$path = $this->resolvPath($row['uid'], $row['title'], $row['parent']);
				$path = addslashes($path);
				$zooms[$path] = addslashes($row['zoom']);
			}
		}
		$jsCode = '
			function(map) { 
				var kmls = ' . json_encode($kmls) . ';
				ics.Map.prototype.zooms = ' . json_encode($zooms) . ';
				(new ics.LevelsKml()).init(map, kmls); 
			}';
		return $jsCode;
	}
	
	/**
	 * [resolvPath description]
	 * @param  [type] $id     [description]
	 * @param  [type] $name   [description]
	 * @param  [type] $parent [description]
	 * @return [type]         [description]
	 */
	private function resolvPath($id, $name, $parent) {
		if (empty($parent))
			return $name;
		$path = $name;
		$parent = strtok($parent, ',');
		if (!isset($this->categories[$parent])) {		
			$select ='l.title as catName,
				l.uid as catId,
				l.parent as catParent';
			$from = 'tx_icsgmap3levels_levels as l';
			$where = 'l.uid IN (' . $parent . ')';
			$tmp = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows($select,$from,$where);
			if (!empty($tmp))
				$this->categories[$parent] = $tmp[0];
			else
				$this->categories[$parent] = false;
		}
		if (!empty($this->categories[$parent])) {
			$path = $this->categories[$parent]['catName'] . $this->conf['separator'] . $path;
			$path = $this->resolvPath($this->categories[$parent]['catId'], $path, $this->categories[$parent]['catParent']);
		}
		return $path;
	}
	
	/**
	* Function to insert Javascript at Ext. Runtime
	*
	* @param string $script Input the Script Name to insert JS
	* @return
	*/
	function incJsFile($script,$jsCode = false, $suffix = '') {
		if(!$jsCode)
			$js = '<script src="'.$script.'" type="text/javascript"><!-- //--></script>';
		else
		{
			$js .= '<script type="text/javascript">
				'.$script.'
			</script>';
		}
		$GLOBALS['TSFE']->additionalHeaderData[$this->extKey . $suffix . $this->cObj->data['uid']] .= $js;
	}	

}
?>