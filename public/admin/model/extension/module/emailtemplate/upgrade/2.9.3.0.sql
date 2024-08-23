ALTER TABLE `oc_emailtemplate_config` CHANGE `emailtemplate_config_header_border_color` `emailtemplate_config_header_border_top` VARCHAR(128);

ALTER TABLE `oc_emailtemplate_config` CHANGE `emailtemplate_config_email_width` `emailtemplate_config_email_width` VARCHAR(12);

ALTER TABLE `oc_emailtemplate_config`
  ADD `emailtemplate_config_body_font_family` VARCHAR(128) NOT NULL AFTER `emailtemplate_config_body_bg_color`,
  ADD `emailtemplate_config_body_font_size` SMALLINT(6) NOT NULL AFTER `emailtemplate_config_body_font_family`,
  ADD `emailtemplate_config_header_border_bottom` VARCHAR(128) AFTER `emailtemplate_config_header_border_top`,
  ADD `emailtemplate_config_header_border_right` VARCHAR(128) AFTER `emailtemplate_config_header_border_bottom`,
  ADD `emailtemplate_config_header_border_left` VARCHAR(128) AFTER `emailtemplate_config_header_border_right`,
  ADD `emailtemplate_config_header_border_radius` VARCHAR(64) AFTER `emailtemplate_config_header_border_left`,
  ADD `emailtemplate_config_header_html` TEXT NULL DEFAULT NULL AFTER `emailtemplate_config_header_bg_color`,

  ADD `emailtemplate_config_footer_font_size` SMALLINT(6) NOT NULL AFTER `emailtemplate_config_footer_font_color`,
  ADD `emailtemplate_config_footer_bg_color` VARCHAR(32) AFTER `emailtemplate_config_footer_font_color`,

  ADD `emailtemplate_config_page_spacing` VARCHAR(32) AFTER `emailtemplate_config_body_section_bg_color`,
  ADD `emailtemplate_config_footer_spacing` VARCHAR(32) AFTER `emailtemplate_config_footer_section_bg_color`,
  ADD `emailtemplate_config_header_spacing` VARCHAR(32) AFTER `emailtemplate_config_header_section_bg_color`,

  ADD `emailtemplate_config_footer_border_radius` VARCHAR(64) AFTER `emailtemplate_config_footer_section_bg_color`,
  ADD `emailtemplate_config_footer_border_top` VARCHAR(128) AFTER `emailtemplate_config_footer_section_bg_color`,
  ADD `emailtemplate_config_footer_border_bottom` VARCHAR(128) AFTER `emailtemplate_config_footer_border_top`,
  ADD `emailtemplate_config_footer_border_left` VARCHAR(128) AFTER `emailtemplate_config_footer_border_bottom`,
  ADD `emailtemplate_config_footer_border_right` VARCHAR(128) AFTER `emailtemplate_config_footer_border_left`,

  ADD `emailtemplate_config_page_border_radius` VARCHAR(64) AFTER `emailtemplate_config_page_bg_color`,
  ADD `emailtemplate_config_page_border_top` VARCHAR(128) AFTER `emailtemplate_config_page_bg_color`,
  ADD `emailtemplate_config_page_border_bottom` VARCHAR(128) AFTER `emailtemplate_config_page_border_top`,
  ADD `emailtemplate_config_page_border_left` VARCHAR(128) AFTER `emailtemplate_config_page_border_bottom`,
  ADD `emailtemplate_config_page_border_right` VARCHAR(128) AFTER `emailtemplate_config_page_border_left`,

  ADD `emailtemplate_config_showcase_border_top` VARCHAR(128) AFTER `emailtemplate_config_showcase_section_bg_color`,
  ADD `emailtemplate_config_showcase_border_bottom` VARCHAR(128) AFTER `emailtemplate_config_showcase_border_top`,
  ADD `emailtemplate_config_showcase_border_right` VARCHAR(128) AFTER `emailtemplate_config_showcase_border_bottom`,
  ADD `emailtemplate_config_showcase_border_left` VARCHAR(128) AFTER `emailtemplate_config_showcase_border_right`,
  ADD `emailtemplate_config_showcase_border_radius` VARCHAR(64) AFTER `emailtemplate_config_showcase_section_bg_color`,
  ADD `emailtemplate_config_showcase_bg_color` VARCHAR(32) AFTER `emailtemplate_config_showcase_related`,

  ADD `emailtemplate_config_order_products` text AFTER `emailtemplate_config_showcase_border_right`,

  ADD `emailtemplate_config_page_padding` VARCHAR(32) AFTER `emailtemplate_config_page_spacing`,
  ADD `emailtemplate_config_link_style` TEXT AFTER `emailtemplate_config_order_products`;

UPDATE `oc_emailtemplate_config` SET `emailtemplate_config_page_padding` = '30, 30, 40, 30' WHERE `emailtemplate_config_page_padding` IS NULL;

UPDATE `oc_emailtemplate_config` SET `emailtemplate_config_body_font_family` = 'Arial, Helvetica, sans-serif' WHERE `emailtemplate_config_body_font_family` = '';

UPDATE `oc_emailtemplate_config` SET `emailtemplate_config_body_font_size` = '14' WHERE `emailtemplate_config_body_font_size` = '';

UPDATE `oc_emailtemplate_config` SET `emailtemplate_config_footer_font_size` = '11' WHERE `emailtemplate_config_footer_font_size` = '';