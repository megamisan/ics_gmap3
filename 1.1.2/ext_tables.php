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
	'sys_language_uid' => array(
		'exclude' => 1,
		'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
		'config'  => array(
			'type'                => 'select',
			'foreign_table'       => 'sys_language',
			'foreign_table_where' => 'ORDER BY sys_language.title',
			'items'               => array(
				array('LLL:EXT:lang/locallang_general.php:LGL.allLanguages', -1),
				array('LLL:EXT:lang/locallang_general.php:LGL.default_value', 0)
			)
		)
	),
	'l18n_parent' => array(
		'displayCond' => 'FIELD:sys_language_uid:>:0',
		'exclude'     => 1,
		'label'       => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
		'config'      => array(
			'type'  => 'select',
			'items' => array(
				array('', 0),
			),
			'foreign_table'       => 'tt_address_group',
			'foreign_table_where' => 'AND tt_address_group.uid=###REC_FIELD_l18n_parent### AND tt_address_group.sys_language_uid IN (-1,0)',
		)
	),
	'l18n_diffsource' => array(
		'config'=> array(
			'type' => 'passthrough'
		)
	),
);

t3lib_div::loadTCA('tt_address');
if (t3lib_extMgm::isLoaded('ics_coordinates_wizard')) {
	$tempColumns['tx_icsgmap3ttaddress_lng']['config']['wizards'] = array(
		'_POSITION' => 'right',
		'googlemap' => array(
			'title' => 'LLL:EXT:ics_coordinates_wizard/locallang_db.xml:wizard.title',
			'icon' =>  'EXT:ics_coordinates_wizard/geo_popup.gif',
			'type' => 'popup',
			'script' => 'EXT:ics_coordinates_wizard/class.tx_icscoordinateswizard_wizard.php',
			'JSopenParams' => 'height=630,width=800,status=0,menubar=0,scrollbars=0',
			'lat_field' => 'tx_icsgmap3ttaddress_lat',
			'lng_field' => 'tx_icsgmap3ttaddress_lng',
		),
	);
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
$TCA['tt_address']['ctrl']['languageField'] = 'sys_language_uid';
$TCA['tt_address']['ctrl']['transOrigPointerField'] = 'l18n_parent';
$TCA['tt_address']['ctrl']['transOrigDiffSourceField'] = 'l18n_diffsource';

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
    'tx_icsgmap3ttaddress_picto_hover' => array (        
        'exclude' => 0,        
        'label' => 'LLL:EXT:ics_gmap3_ttaddress/locallang_db.xml:tt_address_group.tx_icsgmap3ttaddress_picto_hover',        
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
    'tx_icsgmap3ttaddress_picto_list' => array (        
        'exclude' => 0,        
        'label' => 'LLL:EXT:ics_gmap3_ttaddress/locallang_db.xml:tt_address_group.tx_icsgmap3ttaddress_picto_list',        
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
    'tx_icsgmap3ttaddress_picto_list_hover' => array (        
        'exclude' => 0,        
        'label' => 'LLL:EXT:ics_gmap3_ttaddress/locallang_db.xml:tt_address_group.tx_icsgmap3ttaddress_picto_list_hover',        
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
$indexPalettes = 0;
foreach ($TCA['tt_address_group']['palettes'] as $index => $value) {
	if ($index > $indexPalettes)
		$indexPalettes = $index;
}
$indexPalettes++;
t3lib_extMgm::addToAllTCAtypes('tt_address_group','--palette--;LLL:EXT:ics_gmap3_ttaddress/locallang_db.xml:tt_address_group.tx_icsgmap3ttaddress_pictos;' . $indexPalettes . ',--palette--;LLL:EXT:ics_gmap3_ttaddress/locallang_db.xml:tt_address_group.tx_icsgmap3ttaddress_pictos_list;' . ($indexPalettes + 1));
$TCA['tt_address_group']['palettes'][$indexPalettes] = array('showitem' => 'tx_icsgmap3ttaddress_picto, tx_icsgmap3ttaddress_picto_hover', 'canNotCollapse' => 1);
$TCA['tt_address_group']['palettes'][$indexPalettes+1] = array('showitem' => 'tx_icsgmap3ttaddress_picto_list, tx_icsgmap3ttaddress_picto_list_hover', 'canNotCollapse' => 1);

//require_once(t3lib_extMgm::extPath('cv_merge_flexform').'class.tx_cv_merge_flexform.php');
//tx_cv_merge_flexform::addPiFlexFormValue($_EXTKEY.'_pi1','ics_gmap3','FILE:EXT:ics_gmap3_ttaddress/flexform_ds_pi1.xml');

?>