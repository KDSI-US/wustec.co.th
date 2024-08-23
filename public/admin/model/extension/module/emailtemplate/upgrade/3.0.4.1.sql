ALTER TABLE `oc_emailtemplate_config`
  ADD `emailtemplate_config_logo_resize` TINYINT(1) NULL AFTER `emailtemplate_config_logo_height`,
  ADD `emailtemplate_config_header_padding` VARCHAR(32) NULL AFTER `emailtemplate_config_header_html`,
  ADD `emailtemplate_config_header_status` TINYINT(1) NULL AFTER `emailtemplate_config_head_text`,
  ADD `emailtemplate_config_footer_status` TINYINT(1) NULL AFTER `emailtemplate_config_footer_padding`;

UPDATE `oc_emailtemplate_config` SET emailtemplate_config_header_status = 1, emailtemplate_config_footer_status = 1;