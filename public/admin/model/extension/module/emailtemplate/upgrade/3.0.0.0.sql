UPDATE `oc_emailtemplate` SET `emailtemplate_template` = REPLACE(`emailtemplate_template`, '.tpl', '.twig') WHERE `emailtemplate_template` LIKE '%.tpl';

UPDATE `oc_emailtemplate_config` SET `emailtemplate_config_wrapper_tpl` = REPLACE(`emailtemplate_config_wrapper_tpl`, '.tpl', '.twig') WHERE `emailtemplate_config_wrapper_tpl` LIKE '%.tpl';

UPDATE `oc_emailtemplate` SET `emailtemplate_language_files` = REPLACE(`emailtemplate_language_files`, 'extension/mail/', 'extension/module/emailtemplate/') WHERE `emailtemplate_language_files` LIKE '%extension/mail/%';