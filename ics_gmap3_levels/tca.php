<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$TCA['tx_icsgmap3levels_levels'] = array(
	'ctrl' => $TCA['tx_icsgmap3levels_levels']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'hidden,title,parent,picto,picto_map,kml,zoom'
	),
	'feInterface' => $TCA['tx_icsgmap3levels_levels']['feInterface'],
	'columns' => array(
		'hidden' => array(		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array(
				'type'    => 'check',
				'default' => '0'
			)
		),
		'title' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_gmap3_levels/locallang_db.xml:tx_icsgmap3levels_levels.title',		
			'config' => array(
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required,trim',
			)
		),
		'parent' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_gmap3_levels/locallang_db.xml:tx_icsgmap3levels_levels.parent',		
			'config' => array (
				'type'  => 'select',
				'items' => array(
					array('', 0), 
				),
				'foreign_table'       => 'tx_icsgmap3levels_levels',
				'foreign_table_where' => 'AND tx_icsgmap3levels_levels.deleted=0 AND tx_icsgmap3levels_levels.hidden=0',
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'picto' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_gmap3_levels/locallang_db.xml:tx_icsgmap3levels_levels.picto',		
			'config' => array (
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => 'png,jpeg,jpg',	
				'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],	
				'show_thumbs' => 1,	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'picto_map' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_gmap3_levels/locallang_db.xml:tx_icsgmap3levels_levels.picto_map',		
			'config' => array (
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => 'png,jpeg,jpg',	
				'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],	
				'show_thumbs' => 1,	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'kml' => array (        
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_gmap3_levels/locallang_db.xml:tx_icsgmap3levels_levels.kml',		
			'config' => array (
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => 'kml',	
				'max_size' => 400,	
				'show_thumbs' => 0,	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'zoom' => array(		
			'exclude' => 0,		
			'label' => 'LLL:EXT:ics_gmap3_levels/locallang_db.xml:tx_icsgmap3levels_levels.zoom',		
			'config' => array(
				'type' => 'input',	
				'size' => '5',	
				'eval' => 'int',
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'hidden;;1;;1-1-1, title;;;;2-2-2, parent;;;;3-3-3, picto, picto_map, kml, zoom')
	),
	'palettes' => array(
		'1' => array('showitem' => '')
	)
);
?>