ALTER TABLE `oc_emailtemplate` ADD `emailtemplate_showcase_selection` VARCHAR(255) NULL;

ALTER TABLE `oc_emailtemplate` CHANGE `emailtemplate_showcase` `emailtemplate_showcase` VARCHAR(255) NOT NULL DEFAULT '0';