ALTER TABLE `oc_emailtemplate` ADD `emailtemplate_subscribed` TINYINT(1) NULL DEFAULT NULL AFTER `emailtemplate_shortcodes`;
ALTER TABLE `oc_emailtemplate` ADD `emailtemplate_unsubscribe` TINYINT(1) NULL DEFAULT NULL AFTER `emailtemplate_shortcodes`;
ALTER TABLE `oc_emailtemplate_config` ADD `emailtemplate_config_unsubscribe_text` TEXT NULL DEFAULT NULL AFTER `emailtemplate_config_view_browser_text`;

UPDATE `oc_emailtemplate` SET `emailtemplate_subscribed` = 1, `emailtemplate_unsubscribe` = 1 WHERE emailtemplate_key = 'admin.newsletter';
UPDATE `oc_emailtemplate_config` SET `emailtemplate_config_unsubscribe_text` = 'YToxOntpOjE7czoxMzU6IiZsdDtkaXYmZ3Q7SWYgeW91IGRvIG5vdCB3aXNoIHRvIHJlY2VpdmUgb3VyIGVtYWlscyBhbmQgdXBkYXRlcywgcGxlYXNlICZsdDthIGhyZWY9JnF1b3Q7JXMmcXVvdDsmZ3Q7dW5zdWJzY3JpYmUmbHQ7L2EmZ3Q7LiZsdDsvZGl2Jmd0OyI7fQ==';