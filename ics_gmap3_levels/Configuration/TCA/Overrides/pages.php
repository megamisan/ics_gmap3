<?php
/*
 * (c) 2017 Plan.Net France <typo3@plan-net.fr>
 *
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
$tempColumns = array();
$tempColumns['tx_icsgmap3levels_level'] = array(
    'exclude' => 0,
    'label'   => 'LLL:EXT:ics_gmap3_levels/Resources/Private/Language/locallang_db.xlf:tx_icsgmap3levels_levels',
    'config'  => array(
        'type'                => 'select',
        'foreign_table'       => 'tx_icsgmap3levels_levels',
        'foreign_table_where' => 'AND tx_icsgmap3levels_levels.deleted=0 AND tx_icsgmap3levels_levels.hidden=0',
        'size'                => 3,
        'autoSizeMax'         => 15,
        'minitems'            => 0,
        'maxitems'            => 1,
        'renderMode'          => 'tree',
        'treeConfig'          => array(
            'parentField' => 'parent',
            'appearance'  => array(
                'expandAll'  => TRUE,
                'showHeader' => TRUE,
            ),
        ),
    ),
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages', $tempColumns, 1);
$indexPalettes = 0;
foreach ($GLOBALS['TCA']['pages']['palettes'] as $index => $value) {
    if ($index > $indexPalettes) {
        $indexPalettes = $index;
    }
}
$indexPalettes++;
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('pages', '--palette--;LLL:EXT:ics_gmap3_levels/Resources/Private/Language/locallang_db.xlf:tx_icsgmap3levels_levels;' . $indexPalettes, '', 'after:tx_pnfdata_geoloc_address');
$GLOBALS['TCA']['pages']['palettes'][$indexPalettes] = array('showitem' => 'tx_icsgmap3levels_level', 'canNotCollapse' => 1);
