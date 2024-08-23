ALTER TABLE `oc_emailtemplate_logs` CHANGE `emailtemplate_log_cc` `emailtemplate_log_cc` varchar(255) NULL DEFAULT NULL;
ALTER TABLE `oc_emailtemplate_logs` ADD COLUMN `emailtemplate_log_bcc` varchar(255) NULL AFTER `emailtemplate_log_cc`;
