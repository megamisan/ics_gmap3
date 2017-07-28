<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "ics_gmap3_levels".
 *
 * Auto generated 15-05-2014 15:14
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
	'title' => 'Gmap3 levels',
	'description' => 'Associates POIs to levels',
	'category' => 'misc',
	'author' => 'Plan.Net France',
	'author_email' => 'typo3@plan-net.fr',
	'shy' => '',
	'dependencies' => 'ics_gmap3',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'alpha',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '1.0.0',
	'constraints' =>
	array (
		'depends' =>
		array (
			'typo3'     => '7.6.0-7.7.0',
			'ics_gmap3' => '3.0.0-0.0.0',
			'tt_address' => '',
		),
		'conflicts' =>
		array (
		),
		'suggests' =>
		array (
		),
	),
	'suggests' =>
	array (
	),
);
