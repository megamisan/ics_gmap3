<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}
t3lib_extMgm::addStaticFile($_EXTKEY,'static/addresssource/', 'addresssource');

t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:ics_gmap3/flexform_ds_pi2.xml');

$tempColumns = array (
    'tx_icsgmap3ttaddress_lat' => array (        
        'exclude' => 0,		
		'label' => 'LLL:EXT:ics_gmap3_ttaddress/locallang_db.xml:tt_address.tx_icsgmap3ttaddress_lat',		
		'config' => array (
			'type' => 'input',	
			'size' => '30',
			'eval' => 'trim',
			'max' => '11',  
		)
    ),
    'tx_icsgmap3ttaddress_lng' => array (        
		'exclude' => 0,		
		'label' => 'LLL:EXT:ics_gmap3_ttaddress/locallang_db.xml:tt_address.tx_icsgmap3ttaddress_lng',		
		'config' => array (
			'type' => 'input',	
			'size' => '30',
			'eval' => 'trim',
			'max' => '13',  
		)
    ),
);

t3lib_div::loadTCA('tt_address');
if (t3lib_extMgm::isLoaded('rggooglemap')) {
	$tempColumns['tx_icsgmap3ttaddress_lng']['config']['wizards'] = array(
		'_POSITION' => 'right',
		'googlemap' => array(
			'title' => 'LLL:EXT:rggooglemap/locallang_db.xml:wizard.title',
			'icon' =>  'EXT:rggooglemap/mod1/moduleicon.gif',
			'type' => 'popup',
			'script' => 'EXT:rggooglemap/class.tx_rggooglemap_wizard.php',
			'JSopenParams' => 'height=630,width=800,status=0,menubar=0,scrollbars=0',
			'lat_field' => 'tx_icsgmap3ttaddress_lat',
			'lng_field' => 'tx_icsgmap3ttaddress_lng',
		),
	);
	
	$TCA['tt_address']['types']['1']['showitem'] = str_replace(
		array('tx_rggooglemap_lng;;;;1-1-1, tx_rggooglemap_lat,',
			'tx_rggooglemap_display,',
			'tx_rggooglemap_cat2,',
			'tx_rggooglemap_ce'
		), '', $TCA['tt_address']['types']['1']['showitem']);
	
}

t3lib_extMgm::addTCAcolumns('tt_address',$tempColumns,1);

$indexPalettes = 0;
foreach ($TCA['tt_address']['palettes'] as $index => $value) {
	if ($index > $indexPalettes)
		$indexPalettes = $index;
}
$indexPalettes++;

t3lib_extMgm::addToAllTCAtypes('tt_address','--palette--;LLL:EXT:ics_gmap3_ttaddress/locallang_db.xml:tt_address.tx_icsgmap3ttaddress_coord;' . $indexPalettes);
$TCA['tt_address']['palettes'][$indexPalettes] = array('showitem' => 'tx_icsgmap3ttaddress_lat, tx_icsgmap3ttaddress_lng', 'canNotCollapse' => 1);
	

$tempColumns = array (
    'tx_icsgmap3ttaddress_picto' => array (        
        'exclude' => 0,        
        'label' => 'LLL:EXT:ics_gmap3_ttaddress/locallang_db.xml:tt_address_group.tx_icsgmap3ttaddress_picto',        
        'config' => array (
            'type' => 'group',
            'internal_type' => 'file',
            'allowed' => 'gif,png,jpeg,jpg',    
            'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],    
            'uploadfolder' => 'uploads/tx_icsgmap3ttaddress',
            'size' => 1,    
            'minitems' => 0,
            'maxitems' => 1,
        )
    ),
);


t3lib_div::loadTCA('tt_address_group');
t3lib_extMgm::addTCAcolumns('tt_address_group',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('tt_address_group','tx_icsgmap3ttaddress_picto;;;;1-1-1');

//require_once(t3lib_extMgm::extPath('cv_merge_flexform').'class.tx_cv_merge_flexform.php');
//tx_cv_merge_flexform::addPiFlexFormValue($_EXTKEY.'_pi1','ics_gmap3','FILE:EXT:ics_gmap3_ttaddress/flexform_ds_pi1.xml');

?>