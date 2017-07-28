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
namespace PlanNet\IcsGmap3Levels;

use PlanNet\IcsGmap3\IProvider;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class Provider
 * @package PlanNet\IcsGmap3Levels
 */
class Provider implements IProvider {

    /** @var ContentObjectRenderer */
    public $cObj;
    /** @var array */
    protected $categories;
    /** @var array */
    protected $conf;
    /** @var string */
    protected $extKey = 'ics_gmap3_levels';

    /**
     * Provider constructor.
     */
    function __construct() {
    }

    /**
     * @return array
     */
    public function getConf() {
        return $this->conf;
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
     * @return string
     */
    function getFlexform() {
        return '';
    }

    /**
     * @param array $conf
     * @param int $kml
     * @return array
     */
    function makeQuery($conf, $kml = 1) {
        $query = array(
            'SELECT'  => '',
            'FROM'    => '',
            'WHERE'   => '',
            'GROUPBY' => '',
            'ORDERBY' => '',
            'LIMIT'   => '',
        );
        $query['SELECT'] = 'levels.`uid`,
				levels.`title`,
				levels.`parent`,
				levels.`zoom`,
				levels.`sorting`,
				levels.`kml`';
        $query['FROM'] = '`tx_icsgmap3levels_levels` levels';
        $whereClause = array();
        $whereClause[] = 'levels.`deleted` = 0';
        $whereClause[] = 'levels.`hidden` = 0';
        if ($kml) {
            $whereClause[] = 'levels.`kml` != ""';
        }
        // On récupére les conf flexform pour réduire aux catégories sélectionnées. Cette partie est à refaire proprement
        if ($conf['category']) {
            $whereClause[] = 'levels.`uid` IN (' . $conf['category'] . ')';
        }
        // *****
        $query['WHERE'] = implode(' AND ', $whereClause);
        $query['ORDERBY'] = 'levels.`sorting`';
        return $query;
    }

    /**
     * @param array $conf
     * @return string
     */
    function getBehaviourInitFunction($conf) {
        $this->conf['separator'] = $conf['separator'];
        $this->incJsFile(ExtensionManagementUtility::siteRelPath($this->extKey) . 'Resources/Public/JavaScript/tx_icsgmap3_levels.js', FALSE, '_gmap3levels_provider');
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
                $kmls[$path] = $row['kml'];
            }
        }
        // Pré-selection de levels via paramètres GET
        // Exemple : &tx_icsgmap3_pi1[selectedLevels]=1,8
        // Où 1 et 8 sont les uids des levels sélectionnés
        $selectedLevels = GeneralUtility::_GP($conf['prefixId'])['selectedLevels'];
        if (!is_null($selectedLevels)) {
            $selectedLevels = GeneralUtility::intExplode(',', $selectedLevels, TRUE);
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
        $levels = array();
        if (is_array($rows) && !empty($rows)) {
            foreach ($rows as $row) {
                $path = $this->resolvPath($row['uid'], $row['title'], $row['parent']);
                $levels[$path] = array(
                    'sorting'  => intval($row['sorting']),
                    'zoom'     => intval($row['zoom']),
                    'selected' => FALSE,
                );
                if (!is_null($selectedLevels)) {
                    $levels[$path]['selected'] = (((in_array($row['uid'], $selectedLevels) && $row['parent'] != 0) || in_array($row['parent'], $selectedLevels)) ? TRUE : FALSE);
                }
            }
        }
        $jsCode = '
			function(map) {
				var kmls = ' . json_encode($kmls) . ';
				ics.Map.prototype.levels = ' . json_encode($levels) . ';
				(new ics.LevelsKml()).init(map, kmls);
			}';
        return $jsCode;
    }

    /**
     * @param int $id
     * @param string $name
     * @param int $parent
     * @return string
     */
    private function resolvPath($id, $name, $parent) {
        if (empty($parent)) {
            return $name;
        }
        $path = $name;
        $parent = strtok($parent, ',');
        if (!isset($this->categories[$parent])) {
            $select = 'l.title as catName,
				l.uid as catId,
				l.parent as catParent';
            $from = 'tx_icsgmap3levels_levels as l';
            $where = 'l.uid IN (' . intval($parent) . ')';
            $tmp = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows($select, $from, $where);
            if (!empty($tmp)) {
                $this->categories[$parent] = $tmp[0];
            } else {
                $this->categories[$parent] = FALSE;
            }
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

?>
