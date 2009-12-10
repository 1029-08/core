ALTER TABLE xlite_modules CHANGE version version varchar(12) NOT NULL DEFAULT '0';
INSERT INTO xlite_config VALUES ('width','','100','AustraliaPost',0,'');
INSERT INTO xlite_config VALUES ('height','','10','AustraliaPost',0,'');
INSERT INTO xlite_config VALUES ('length','','100','AustraliaPost',0,'');
INSERT INTO xlite_config VALUES ('currency_rate','','1','AustraliaPost',0,'');

--INSERT INTO xlite_shipping VALUES (300,'aupost','L','Australia Post Air Mail',1,0);
--INSERT INTO xlite_shipping VALUES (301,'aupost','L','Australia Post Economy Air',1,0);
INSERT INTO xlite_shipping VALUES (302,'aupost','L','Australia Post Express Parcels',1,0);
INSERT INTO xlite_shipping VALUES (303,'aupost','L','Australia Post Regular Parcels',1,0);
--INSERT INTO xlite_shipping VALUES (304,'aupost','L','Australia Post Sea Mail',1,0);
INSERT INTO xlite_shipping VALUES (310,'aupost','I','Australia Post Air Mail',1,0);
--INSERT INTO xlite_shipping VALUES (311,'aupost','I','Australia Post Economy Air',1,0);
--INSERT INTO xlite_shipping VALUES (312,'aupost','I','Australia Post Express Parcels',1,0);
--INSERT INTO xlite_shipping VALUES (313,'aupost','I','Australia Post Regular Parcels',1,0);
INSERT INTO xlite_shipping VALUES (314,'aupost','I','Australia Post Sea Mail',1,0);
INSERT INTO xlite_shipping VALUES (305,'aupost','I','Australia Post Express Courier International Document',1,0);
INSERT INTO xlite_shipping VALUES (306,'aupost','I','Australia Post Express Courier International Merchandise',1,0);
INSERT INTO xlite_shipping VALUES (307,'aupost','I','Australia Post Express Post International',1,0);
-- not supported by Australia Post, see https://bt.crtdev.local/view.php?id=51462
DELETE FROM xlite_shipping WHERE shipping_id = 300;
DELETE FROM xlite_shipping WHERE shipping_id = 301;
DELETE FROM xlite_shipping WHERE shipping_id = 304;
DELETE FROM xlite_shipping WHERE shipping_id = 311;
DELETE FROM xlite_shipping WHERE shipping_id = 312;
DELETE FROM xlite_shipping WHERE shipping_id = 313;

CREATE TABLE xlite_aupost_cache
(
    weight decimal(12,2) not null,
    origin_zipcode varchar(12) not null,
    dest_zipcode varchar(12) not null,
    dest_country varchar(40) not null,
    height decimal(12,2) not null,
    width decimal(12,2) not null,
    length decimal(12,2) not null,
    rates varchar(255) not null,
    shipping_dates varchar(255) not null,
    date int not null,
    primary key 
    (
        weight,
        origin_zipcode,
        dest_zipcode,
        dest_country,
        height,
        width,
        length
    )
);

