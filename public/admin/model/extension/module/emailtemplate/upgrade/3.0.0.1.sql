DROP TABLE IF EXISTS `oc_emailtemplate_event`;
CREATE TABLE `oc_emailtemplate_event` (
  emailtemplate_event_id INT(11) NOT NULL AUTO_INCREMENT,
  emailtemplate_key VARCHAR(64) NOT NULL,
  event_id INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (emailtemplate_event_id),
  INDEX (emailtemplate_key),
  INDEX (event_id)
);