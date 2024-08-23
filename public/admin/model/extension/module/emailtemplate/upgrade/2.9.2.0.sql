DROP TABLE IF EXISTS `oc_emailtemplate_showcase_log`;
CREATE TABLE `oc_emailtemplate_showcase_log` (
  `customer_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `emailtemplate_showcase_log_count` smallint(6) NOT NULL,
  `emailtemplate_showcase_log_modified` datetime NOT NULL,
  PRIMARY KEY (`customer_id`, `product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

ALTER TABLE `oc_emailtemplate_config` ADD `emailtemplate_config_showcase_cycle` tinyint(1) NOT NULL DEFAULT '1';