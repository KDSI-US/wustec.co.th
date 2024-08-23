ALTER TABLE `oc_emailtemplate` CHANGE `emailtemplate_showcase` `emailtemplate_showcase` VARCHAR(32) NOT NULL DEFAULT '0';

ALTER TABLE `oc_emailtemplate_description` ADD `emailtemplate_description_heading` VARCHAR(255) NULL DEFAULT NULL AFTER `emailtemplate_description_preview`;