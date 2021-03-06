#
# Table structure for table 'tx_icsgmap3levels_levels'
#
CREATE TABLE tx_icsgmap3levels_levels (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	parent int(11) DEFAULT '0' NOT NULL,
	picto text,
	picto_map text,
	kml text,
	zoom int(11) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
) ENGINE=InnoDB;

#
# Table structure for table 'pages'
#
CREATE TABLE pages (
	tx_icsgmap3levels_level text
);

#
# Table structure for table 'tt_address'
#
CREATE TABLE tt_address (
	tx_icsgmap3levels_level text
);
