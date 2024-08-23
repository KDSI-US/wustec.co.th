ALTER TABLE `oc_emailtemplate_config` CHANGE `emailtemplate_config_link_style` `emailtemplate_config_link_style` VARCHAR(32) NULL DEFAULT NULL;

ALTER TABLE `oc_emailtemplate_config`  ADD `emailtemplate_config_language_rtl` TINYINT(1) NULL DEFAULT '0'  AFTER `emailtemplate_config_style`;

ALTER TABLE `oc_emailtemplate_logs` ADD `emailtemplate_log_reply_to` VARCHAR(255) NOT NULL AFTER `emailtemplate_log_from`;