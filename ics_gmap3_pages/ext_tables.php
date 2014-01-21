<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}
t3lib_extMgm::addStaticFile($_EXTKEY,'static/pagessource/', 'pagessource');

t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:ics_gmap3/flexform_ds_pi2.xml');

$tempColumns = array (
    'tx_icsgmap3pages_lat' => array (        
        'exclude' => 0,		
		'label' => 'LLL:EXT:ics_gmap3_pages/locallang_db.xml:pages.tx_icsgmap3pages_lat',		
		'config' => array (
			'type' => 'input',	
			'size' => '30',
			'eval' => 'trim',
			'max' => '11',  
		)
    ),
    'tx_icsgmap3pages_lng' => array (        
		'exclude' => 0,		
		'label' => 'LLL:EXT:ics_gmap3_pages/locallang_db.xml:pages.tx_icsgmap3pages_lng',		
		'config' => array (
			'type' => 'input',	
			'size' => '30',
			'eval' => 'trim',
			'max' => '13',  
		)
    ),
);

t3lib_div::loadTCA('pages');
if (t3lib_extMgm::isLoaded('ics_coordinates_wizard')) {
	$tempColumns['tx_icsgmap3pages_lng']['config']['wizards'] = array(
		'_POSITION' => 'right',
		'googlemap' => array(
			'title' => 'LLL:EXT:ics_coordinates_wizard/locallang_db.xml:wizard.title',
			'icon' =>  'EXT:ics_coordinates_wizard/geo_popup.gif',
			'type' => 'popup',
			'script' => 'EXT:ics_coordinates_wizard/class.tx_icscoordinateswizard_wizard.php',
			'JSopenParams' => 'height=630,width=800,status=0,menubar=0,scrollbars=0',
			'lat_field' => 'tx_icsgmap3pages_lat',
			'lng_field' => 'tx_icsgmap3pages_lng',
		),
	);
}

//if levels is install
if (t3lib_extMgm::isLoaded('ics_gmap3_levels')) {
	$tempColumns['tx_icsgmap3pages_level'] = array(
		'exclude' => 0,		
		'label' => 'LLL:EXT:ics_gmap3_pages/locallang_db.xml:pages.tx_icsgmap3pages_level',		
		'config' => array (
			'type'  => 'select',
			'foreign_table'       => 'tx_icsgmap3levels_levels',
			'foreign_table_where' => 'AND tx_icsgmap3levels_levels.deleted=0 AND tx_icsgmap3levels_levels.hidden=0',
			'size' => 3,
			'autoSizeMax' => 15,
			'minitems' => 0,
			'maxitems' => 1,
			'renderMode' => 'tree',
			'treeConfig' => array(
			    'parentField' => 'parent',
			    'appearance' => array(
			        'expandAll' => true,
			        'showHeader' => true,
			    ),
			),
		)
	);
}


t3lib_extMgm::addTCAcolumns('pages',$tempColumns,1);

$indexPalettes = 0;
foreach ($TCA['pages']['palettes'] as $index => $value) {
	if ($index > $indexPalettes)
		$indexPalettes = $index;
}
$indexPalettes++;

t3lib_extMgm::addToAllTCAtypes('pages','--palette--;LLL:EXT:ics_gmap3_pages/locallang_db.xml:pages.tx_icsgmap3pages_coord;' . $indexPalettes);
$TCA['pages']['palettes'][$indexPalettes] = array('showitem' => 'tx_icsgmap3pages_level, tx_icsgmap3pages_lat, tx_icsgmap3pages_lng', 'canNotCollapse' => 1);



//require_once(t3lib_extMgm::extPath('cv_merge_flexform').'class.tx_cv_merge_flexform.php');
//tx_cv_merge_flexform::addPiFlexFormValue($_EXTKEY.'_pi1','ics_gmap3','FILE:EXT:ics_gmap3_pages/flexform_ds_pi1.xml');

?>