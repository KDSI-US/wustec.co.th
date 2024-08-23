ALTER TABLE `oc_emailtemplate_config`
  ADD `emailtemplate_config_body_font_custom` VARCHAR(128) NOT NULL AFTER `emailtemplate_config_body_font_family`,
  ADD `emailtemplate_config_body_font_url` VARCHAR(128) NOT NULL AFTER `emailtemplate_config_body_font_custom`,
  ADD `emailtemplate_config_cart` TINYINT(1) NOT NULL AFTER `emailtemplate_config_shadow_bottom`,
  ADD `emailtemplate_config_showcase_setting` TEXT NULL AFTER `emailtemplate_config_showcase_selection`,
  ADD `emailtemplate_config_order_update` TEXT NULL AFTER `emailtemplate_config_order_products`,
  ADD `emailtemplate_config_cart_setting` TEXT NULL AFTER `emailtemplate_config_order_update`;

ALTER TABLE `oc_emailtemplate_description`
  ADD `emailtemplate_description_showcase_title` VARCHAR(255) NULL,
  ADD `emailtemplate_description_cart_title` VARCHAR(255) NULL,
  ADD `emailtemplate_description_order_title` VARCHAR(255) NULL;

ALTER TABLE `oc_emailtemplate`
  ADD `emailtemplate_order_product` TINYINT(1) NULL AFTER `emailtemplate_showcase`,
  ADD `emailtemplate_cart_product` TINYINT(1) NULL AFTER `emailtemplate_order_product`;