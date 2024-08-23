DELETE t1.* FROM oc_emailtemplate_shortcode t1 WHERE t1.emailtemplate_shortcode_id IN (SELECT * FROM (SELECT t2.emailtemplate_shortcode_id FROM oc_emailtemplate_shortcode t2 GROUP BY t2.emailtemplate_id, t2.emailtemplate_shortcode_code HAVING (COUNT(*) > 1)) AS A);

ALTER TABLE `oc_emailtemplate` ADD `emailtemplate_mail_queue` TINYINT(1) NULL DEFAULT NULL AFTER `emailtemplate_view_browser`;

ALTER TABLE `oc_emailtemplate_logs`
ADD `emailtemplate_log_is_sent` TINYINT(1) NULL DEFAULT NULL AFTER `emailtemplate_log_status`,
ADD `emailtemplate_log_protocol` ENUM('mail','smtp') NULL DEFAULT NULL,
ADD `emailtemplate_log_parameter` VARCHAR(255) NOT NULL AFTER `emailtemplate_log_protocol`,
ADD `emailtemplate_log_smtp` TEXT NOT NULL AFTER `emailtemplate_log_parameter`,
ADD `emailtemplate_log_cc` VARCHAR(255) NOT NULL AFTER `emailtemplate_log_sender`,
ADD `emailtemplate_log_bcc` VARCHAR(255) NOT NULL AFTER `emailtemplate_log_cc`,
ADD `emailtemplate_log_attachment` VARCHAR(255) NOT NULL AFTER `emailtemplate_log_bcc`,
ADD `language_id` INT(11) UNSIGNED NOT NULL AFTER `store_id`;

ALTER TABLE `oc_emailtemplate_logs` ADD INDEX(`emailtemplate_log_is_sent`);