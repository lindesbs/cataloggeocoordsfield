-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the TYPOlight *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************


-- --------------------------------------------------------

CREATE TABLE `tl_catalog_fields` (
  `geocoords_coords` varchar(60) NOT NULL default '',
  
  `geocoords_streetfield` varchar(255) NOT NULL default '',
  `geocoords_postalfield` varchar(255) NOT NULL default '',
  `geocoords_cityfield` varchar(255) NOT NULL default '',
  `geocoords_countryfield` varchar(255) NOT NULL default '',
  
  `geocoords_streetfield_rewrite` char(1) NOT NULL default '',  
  `geocoords_postalfield_rewrite` char(1) NOT NULL default '',
  `geocoords_cityfield_rewrite` char(1) NOT NULL default '',
  `geocoords_countryfield_rewrite` char(1) NOT NULL default '',
  
) TYPE=MyISAM DEFAULT CHARSET=utf8;