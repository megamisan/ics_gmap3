<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "ics_gmap3_ttaddress".
 *
 * Auto generated 21-03-2013 15:13
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'AddressSource (tt_address)',
	'description' => 'Address source (tt_address) for ics_gmap3',
	'category' => 'plugin',
	'author' => 'In Cite Solution',
	'author_email' => 'technique@in-cite.net',
	'shy' => '',
	'dependencies' => 'ics_gmap3,tt_address',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '1.1.2',
	'constraints' => array(
		'depends' => array(
			'ics_gmap3' => '',
			'tt_address' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:17:{s:9:"ChangeLog";s:4:"078e";s:16:"ext_autoload.php";s:4:"3638";s:12:"ext_icon.gif";s:4:"1bdc";s:17:"ext_localconf.php";s:4:"fb46";s:14:"ext_tables.php";s:4:"adc3";s:14:"ext_tables.sql";s:4:"1bc3";s:15:"flexform_ds.xml";s:4:"e94c";s:13:"locallang.xml";s:4:"096f";s:16:"locallang_db.xml";s:4:"1c3d";s:10:"README.txt";s:4:"ee2d";s:47:"classes/class.tx_icsgmap3ttaddress_provider.php";s:4:"5d6f";s:49:"classes/class.ux_tx_icsgmap3_taglist_provider.php";s:4:"d67c";s:64:"classes/helpers/class.tx_icsgmap3_tt_address_flexform_helper.php";s:4:"a0a5";s:19:"res/createMarker.js";s:4:"f98b";s:30:"res/gmap3_taglist_ttaddress.js";s:4:"f3e4";s:34:"static/addresssource/constants.txt";s:4:"68e7";s:30:"static/addresssource/setup.txt";s:4:"c468";}',
	'suggests' => array(
	),
);

?>