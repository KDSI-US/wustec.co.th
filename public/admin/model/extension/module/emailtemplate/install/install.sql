DROP TABLE IF EXISTS `oc_emailtemplate`;
CREATE TABLE `oc_emailtemplate`(
  `emailtemplate_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `emailtemplate_key` varchar(32) NOT NULL,
  `emailtemplate_label` varchar(255) NOT NULL,
  `emailtemplate_type` enum('','customer','affiliate','order','admin','other') NOT NULL,
  `emailtemplate_preference` ENUM('', 'essential','notification','newsletter','') NOT NULL,
  `emailtemplate_template` varchar(255) NULL DEFAULT NULL,
  `emailtemplate_mail_to` varchar(255) NULL DEFAULT NULL,
  `emailtemplate_mail_cc` varchar(255) NULL DEFAULT NULL,
  `emailtemplate_mail_bcc` varchar(255) NULL DEFAULT NULL,
  `emailtemplate_mail_from` varchar(255) NULL DEFAULT NULL,
  `emailtemplate_mail_html` tinyint(1) NOT NULL DEFAULT '1',
  `emailtemplate_mail_plain_text` tinyint(1) NOT NULL DEFAULT '0',
  `emailtemplate_mail_sender` varchar(255) NULL DEFAULT NULL,
  `emailtemplate_mail_replyto` varchar(255) NULL DEFAULT NULL,
  `emailtemplate_mail_replyto_name` varchar(255) NULL DEFAULT NULL,
  `emailtemplate_mail_attachment` varchar(255) NULL DEFAULT NULL,
  `emailtemplate_language_files` varchar(255) NULL DEFAULT NULL,
  `emailtemplate_wrapper_tpl` varchar(255) NULL DEFAULT NULL,
  `emailtemplate_status` tinyint(1) NOT NULL DEFAULT '1',
  `emailtemplate_default` tinyint(1) NOT NULL DEFAULT '1',
  `emailtemplate_shortcodes` tinyint(1) NOT NULL DEFAULT '0',
  `emailtemplate_showcase` VARCHAR(32) NOT NULL DEFAULT '0',
  `emailtemplate_showcase_selection` varchar(255) NULL,
  `emailtemplate_order_product` tinyint(1) NULL DEFAULT NULL,
  `emailtemplate_cart_product` tinyint(1) NULL DEFAULT NULL,
  `emailtemplate_condition` TEXT NULL DEFAULT NULL,
  `emailtemplate_modified` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `emailtemplate_log` tinyint(1) NULL DEFAULT NULL,
  `emailtemplate_mail_queue` tinyint(1) NULL DEFAULT NULL,
  `emailtemplate_send_max` tinyint(1) NULL DEFAULT NULL,
  `payment_method` VARCHAR(128) NULL DEFAULT NULL,
  `shipping_method` VARCHAR(128) NULL DEFAULT NULL,
  `emailtemplate_config_id` int(11) unsigned NULL DEFAULT NULL,
  `store_id` int(11) NULL DEFAULT NULL,
  `customer_group_id` int(11) NULL DEFAULT NULL,
  `order_status_id` int(11) NULL DEFAULT NULL,
  `event_id` int(11) NULL DEFAULT NULL,
  PRIMARY KEY(`emailtemplate_id`),
  INDEX (`emailtemplate_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `oc_emailtemplate_description`;
CREATE TABLE `oc_emailtemplate_description`(
  `emailtemplate_id` int(11) unsigned NOT NULL,
  `language_id` int(11) unsigned NOT NULL,
  `emailtemplate_description_subject` varchar(120) NULL DEFAULT NULL,
  `emailtemplate_description_preview` varchar(255) NULL DEFAULT NULL,
  `emailtemplate_description_heading` varchar(255) NULL DEFAULT NULL,
  `emailtemplate_description_content1` longtext NULL DEFAULT NULL,
  `emailtemplate_description_content2` longtext NULL DEFAULT NULL,
  `emailtemplate_description_content3` longtext NULL DEFAULT NULL,
  `emailtemplate_description_comment` longtext NULL DEFAULT NULL,
  `emailtemplate_description_showcase_title` varchar(255) NULL DEFAULT NULL,
  `emailtemplate_description_cart_title` varchar(255) NULL DEFAULT NULL,
  `emailtemplate_description_order_title` varchar(255) NULL DEFAULT NULL,
  PRIMARY KEY(`emailtemplate_id`, `language_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `oc_emailtemplate_shortcode`;
CREATE TABLE `oc_emailtemplate_shortcode`(
  `emailtemplate_shortcode_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `emailtemplate_shortcode_code` varchar(255) NOT NULL,
  `emailtemplate_shortcode_type` enum('language', 'auto', 'auto_serialize') NOT NULL DEFAULT 'language',
  `emailtemplate_shortcode_example` TEXT NOT NULL,
  `emailtemplate_id` int(11) unsigned NOT NULL,
  PRIMARY KEY(`emailtemplate_shortcode_id`),
  INDEX (`emailtemplate_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `oc_emailtemplate_logs`;
CREATE TABLE `oc_emailtemplate_logs` (
  `emailtemplate_log_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `emailtemplate_key` varchar(32) DEFAULT NULL,
  `emailtemplate_id` int(11) UNSIGNED DEFAULT NULL,
  `emailtemplate_config_id` int(11) UNSIGNED DEFAULT NULL,
  `customer_id` int(11) UNSIGNED DEFAULT NULL,
  `customer_group_id` int(11) UNSIGNED DEFAULT NULL,
  `language_id` int(11) UNSIGNED DEFAULT NULL,
  `order_id` int(11) UNSIGNED DEFAULT NULL,
  `store_id` int(11) UNSIGNED DEFAULT NULL,
  `emailtemplate_log_added` datetime DEFAULT NULL,
  `emailtemplate_log_sent` datetime DEFAULT NULL,
  `emailtemplate_log_read` datetime DEFAULT NULL,
  `emailtemplate_log_to` varchar(96) NOT NULL,
  `emailtemplate_log_from` varchar(96) NOT NULL,
  `emailtemplate_log_reply_to` varchar(96) DEFAULT NULL,
  `emailtemplate_log_cc` varchar(255) DEFAULT NULL,
  `emailtemplate_log_bcc` varchar(255) DEFAULT NULL,
  `emailtemplate_log_sender` varchar(32) DEFAULT NULL,
  `emailtemplate_log_subject` varchar(120) DEFAULT NULL,
  `emailtemplate_log_heading` varchar(64) DEFAULT NULL,
  `emailtemplate_log_content` longtext,
  `emailtemplate_log_enc` varchar(32) DEFAULT NULL,
  `emailtemplate_log_is_sent` tinyint(1) DEFAULT NULL,
  PRIMARY KEY(`emailtemplate_log_id`),
  INDEX (`customer_id`),
 INDEX (`emailtemplate_id`),
 INDEX (`emailtemplate_key`),
 INDEX (`emailtemplate_log_is_sent`),
 INDEX (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `oc_emailtemplate_showcase_log`;
CREATE TABLE `oc_emailtemplate_showcase_log` (
  `customer_id` int(11) unsigned NOT NULL,
  `product_id` int(11) unsigned NOT NULL,
  `emailtemplate_showcase_log_count` smallint(6) NOT NULL,
  `emailtemplate_showcase_log_modified` datetime NOT NULL,
  PRIMARY KEY (`customer_id`, `product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

DROP TABLE IF EXISTS `oc_emailtemplate_event`;
CREATE TABLE `oc_emailtemplate_event` (
  emailtemplate_event_id INT(11) NOT NULL AUTO_INCREMENT,
  emailtemplate_key VARCHAR(64) NOT NULL,
  event_id INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (emailtemplate_event_id),
  INDEX (emailtemplate_key),
  INDEX (event_id)
);
