ALTER TABLE `oc_emailtemplate_config` ADD `emailtemplate_config_body_font_source` text,
  ADD `emailtemplate_config_css_custom` text;

ALTER TABLE `oc_emailtemplate` ADD `payment_method` VARCHAR (128),
ADD `shipping_method` VARCHAR (128);