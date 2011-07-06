<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}
t3lib_extMgm::addStaticFile($_EXTKEY,'static/addresssource/', 'addresssource');

t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:ics_gmap3/flexform_ds_pi2.xml');

$tempColumns = array (
    'tx_icsgmap3ttaddress_coordinates' => array (        
        'exclude' => 0,        
        'label' => 'LLL:EXT:ics_gmap3_ttaddress/locallang_db.xml:tt_address.tx_icsgmap3ttaddress_coordinates',        
        'config' => array (
            'type' => 'input',    
            'size' => '30',
        )
    ),
);


t3lib_div::loadTCA('tt_address');
t3lib_extMgm::addTCAcolumns('tt_address',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('tt_address','tx_icsgmap3ttaddress_coordinates;;;;1-1-1');

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