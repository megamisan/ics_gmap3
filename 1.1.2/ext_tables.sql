#
# Table structure for table 'tt_address'
#
CREATE TABLE tt_address (
	tx_icsgmap3ttaddress_lat decimal(10,8),
	tx_icsgmap3ttaddress_lng decimal(12,9),
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL
);

#
# Table structure for table 'tt_address_group'
#
CREATE TABLE tt_address_group (
    tx_icsgmap3ttaddress_picto text,
    tx_icsgmap3ttaddress_picto_hover text,
    tx_icsgmap3ttaddress_picto_list text,
    tx_icsgmap3ttaddress_picto_list_hover text,
);