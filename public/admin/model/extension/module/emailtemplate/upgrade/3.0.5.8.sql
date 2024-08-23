ALTER TABLE `oc_emailtemplate` CHANGE `emailtemplate_type` `emailtemplate_type` ENUM('','customer','affiliate','order','admin','other') NOT NULL;

ALTER TABLE `oc_emailtemplate_logs` ADD `emailtemplate_log_heading` VARCHAR(255) NULL DEFAULT NULL;

ALTER TABLE `oc_emailtemplate` ADD `emailtemplate_preference` ENUM('', 'essential','notification','newsletter','') NOT NULL;

ALTER TABLE `oc_emailtemplate_config` ADD `emailtemplate_config_unsubscribe` TINYINT(1) NULL DEFAULT NULL;
ALTER TABLE `oc_emailtemplate_config` ADD `emailtemplate_config_view_browser` TINYINT(1) NULL DEFAULT NULL;
ALTER TABLE `oc_emailtemplate_config` ADD `emailtemplate_config_preference_text` TEXT NULL DEFAULT NULL;

UPDATE `oc_emailtemplate` SET `emailtemplate_type` = 'customer' WHERE `emailtemplate_key` = 'admin.newsletter';

UPDATE `oc_emailtemplate_config` SET `language_id` = NULL, store_id = NULL, customer_group_id = null WHERE `emailtemplate_config_id` = 1;
