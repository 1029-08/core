ALTER TABLE xlite_modules CHANGE version version varchar(12) NOT NULL DEFAULT '0';
CREATE TABLE xlite_country_currencies (
	currency_id 	int(11) 		auto_increment,
	code 			varchar(3)		NOT NULL default '',
	name			varchar(50)		NOT NULL default '',
	exchange_rate	decimal(12,4)	NOT NULL default '0.00',
	price_format	varchar(50)		NOT NULL default '$ %s',
	base			int(1)			NOT NULL default '0',
	enabled			int(1)			NOT NULL default '0',
	order_by		int(11)			NOT NULL default '0',
	countries		text			NOT NULL default '',
	PRIMARY KEY (currency_id),
    KEY orderby (order_by)
) TYPE=MyISAM;

INSERT INTO xlite_config VALUES ('country_currency','Display default currency & customer\'s national currency only ( [*] this option does not have effect when a customer is not logged in)','Y','MultiCurrency',10,'checkbox');
