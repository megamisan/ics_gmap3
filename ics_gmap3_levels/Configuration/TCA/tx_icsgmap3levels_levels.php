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
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_icsgmap3levels_levels');
return array(
    'ctrl'        => array(
        'title'          => 'LLL:EXT:ics_gmap3_levels/Resources/Private/Language/locallang_db.xlf:tx_icsgmap3levels_levels',
        'label'          => 'title',
        'label_userFunc' => 'PlanNet\IcsGmap3Levels\Tca->getTitle',
        'tstamp'         => 'tstamp',
        'crdate'         => 'crdate',
        'cruser_id'      => 'cruser_id',
        'sortby'         => 'sorting',
        'delete'         => 'deleted',
        'enablecolumns'  => array(
            'disabled' => 'hidden',
        ),
        'iconfile'       => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('ics_gmap3_levels') . 'Resources/Public/Icons/tx_icsgmap3levels_levels.gif',
    ),
    'interface'   => array(
        'showRecordFieldList' => 'hidden,title,parent,picto,picto_map,kml,zoom',
    ),
    'feInterface' => $GLOBALS['TCA']['tx_icsgmap3levels_levels']['feInterface'],
    'columns'     => array(
        'hidden'    => array(
            'exclude' => 1,
            'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config'  => array(
                'type'    => 'check',
                'default' => '0',
            ),
        ),
        'title'     => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:ics_gmap3_levels/Resources/Private/Language/locallang_db.xlf:tx_icsgmap3levels_levels.title',
            'config'  => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'required,trim',
            ),
        ),
        'parent'    => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:ics_gmap3_levels/Resources/Private/Language/locallang_db.xlf:tx_icsgmap3levels_levels.parent',
            'config'  => array(
                'type'                => 'select',
                'items'               => array(
                    array('', 0),
                ),
                'foreign_table'       => 'tx_icsgmap3levels_levels',
                'foreign_table_where' => 'AND tx_icsgmap3levels_levels.deleted=0 AND tx_icsgmap3levels_levels.hidden=0',
                'minitems'            => 0,
                'maxitems'            => 1,
            ),
        ),
        'picto'     => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:ics_gmap3_levels/Resources/Private/Language/locallang_db.xlf:tx_icsgmap3levels_levels.picto',
            'config'  => array(
                'type'          => 'group',
                'internal_type' => 'file',
                'allowed'       => 'png,jpeg,jpg',
                'max_size'      => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],
                'show_thumbs'   => 1,
                'size'          => 1,
                'minitems'      => 0,
                'maxitems'      => 1,
            ),
        ),
        'picto_map' => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:ics_gmap3_levels/Resources/Private/Language/locallang_db.xlf:tx_icsgmap3levels_levels.picto_map',
            'config'  => array(
                'type'          => 'group',
                'internal_type' => 'file',
                'allowed'       => 'png,jpeg,jpg',
                'max_size'      => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],
                'show_thumbs'   => 1,
                'size'          => 1,
                'minitems'      => 0,
                'maxitems'      => 1,
            ),
        ),
        'kml'       => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:ics_gmap3_levels/Resources/Private/Language/locallang_db.xlf:tx_icsgmap3levels_levels.kml',
            'config'  => array(
                'type'          => 'group',
                'internal_type' => 'file',
                'allowed'       => 'kml',
                'max_size'      => 400,
                'show_thumbs'   => 0,
                'size'          => 1,
                'minitems'      => 0,
                'maxitems'      => 1,
            ),
        ),
        'zoom'      => array(
            'exclude' => 0,
            'label'   => 'LLL:EXT:ics_gmap3_levels/Resources/Private/Language/locallang_db.xlf:tx_icsgmap3levels_levels.zoom',
            'config'  => array(
                'type' => 'input',
                'size' => '5',
                'eval' => 'int',
            ),
        ),
    ),
    'types'       => array(
        '0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, parent;;;;3-3-3, picto, picto_map, kml, zoom'),
    ),
    'palettes'    => array(
        '1' => array('showitem' => ''),
    ),
);
