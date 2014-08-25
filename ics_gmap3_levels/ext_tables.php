<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_extMgm::allowTableOnStandardPages('tx_icsgmap3levels_levels');

$TCA['tx_icsgmap3levels_levels'] = array(
	'ctrl' => array(
		'title'     => 'LLL:EXT:ics_gmap3_levels/locallang_db.xml:tx_icsgmap3levels_levels',
		'label'     => 'title',
		'label_userFunc' => 'tx_icsgmap3levels_tca->getTitle',
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY) . 'icon_tx_icsgmap3levels_levels.gif',
	),
);


$tempColumns = array();
$tempColumns['tx_icsgmap3levels_level'] = array(
	'exclude' => 0,
	'label' => 'LLL:EXT:ics_gmap3_levels/locallang_db.xml:tx_icsgmap3levels_levels',
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


t3lib_extMgm::addTCAcolumns('pages',$tempColumns,1);

$indexPalettes = 0;
foreach ($TCA['pages']['palettes'] as $index => $value) {
	if ($index > $indexPalettes)
		$indexPalettes = $index;
}
$indexPalettes++;

t3lib_extMgm::addToAllTCAtypes('pages','--palette--;LLL:EXT:ics_gmap3_levels/locallang_db.xml:tx_icsgmap3levels_levels;' . $indexPalettes);
$TCA['pages']['palettes'][$indexPalettes] = array('showitem' => 'tx_icsgmap3levels_level', 'canNotCollapse' => 1);


t3lib_extMgm::addTCAcolumns('tt_address',$tempColumns,1);

$indexPalettes = 0;
foreach ($TCA['tt_address']['palettes'] as $index => $value) {
	if ($index > $indexPalettes)
		$indexPalettes = $index;
}
$indexPalettes++;

t3lib_extMgm::addToAllTCAtypes('tt_address','--palette--;LLL:EXT:ics_gmap3_levels/locallang_db.xml:tx_icsgmap3levels_levels;' . $indexPalettes);
$TCA['tt_address']['palettes'][$indexPalettes] = array('showitem' => 'tx_icsgmap3levels_level', 'canNotCollapse' => 1);

?>