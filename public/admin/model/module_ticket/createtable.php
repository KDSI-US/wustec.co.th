<?php
class ModelModuleTicketCreatetable extends Model {
	public function Createtable() {
		$query = $this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."ticketdepartment'");
		if(!$query->num_rows) {
			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."ticketdepartment` (`ticketdepartment_id` int(11) NOT NULL AUTO_INCREMENT,`icon_class` varchar(20) NOT NULL,`sort_order` int(3) NOT NULL DEFAULT '0',`status` tinyint(1) NOT NULL DEFAULT '1',PRIMARY KEY (`ticketdepartment_id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0");

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."ticketdepartment_description` (`ticketdepartment_id` int(11) NOT NULL,`language_id` int(11) NOT NULL,`title` varchar(255) NOT NULL,`sub_title` varchar(255) NOT NULL,PRIMARY KEY (`ticketdepartment_id`,`language_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8");

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."ticketknowledge_article` (`ticketknowledge_article_id` int(11) NOT NULL AUTO_INCREMENT,`ticketknowledge_section_id` int(11) NOT NULL,`sort_order` int(3) NOT NULL DEFAULT '0',`status` tinyint(1) NOT NULL DEFAULT '1',PRIMARY KEY (`ticketknowledge_article_id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0");

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."ticketknowledge_article_description` (`ticketknowledge_article_id` int(11) NOT NULL,`language_id` int(11) NOT NULL,`title` varchar(255) NOT NULL,`sub_title` varchar(255) NOT NULL,`banner_title` varchar(255) NOT NULL,`meta_title` varchar(255) NOT NULL,`meta_description` varchar(255) NOT NULL,`meta_keyword` varchar(255) NOT NULL,`description` text NOT NULL,PRIMARY KEY (`ticketknowledge_article_id`,`language_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8");

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."ticketknowledge_article_related` (`ticketknowledge_article_id` int(11) NOT NULL,`related_id` int(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8");

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."ticketknowledge_section` (`ticketknowledge_section_id` int(11) NOT NULL AUTO_INCREMENT,`icon_class` varchar(20) NOT NULL,`sort_order` int(3) NOT NULL DEFAULT '0',`status` tinyint(1) NOT NULL DEFAULT '1',PRIMARY KEY (`ticketknowledge_section_id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0");

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."ticketknowledge_section_description` (`ticketknowledge_section_id` int(11) NOT NULL,`language_id` int(11) NOT NULL,`title` varchar(255) NOT NULL,`sub_title` varchar(255) NOT NULL,`banner_title` varchar(255) NOT NULL,`meta_title` varchar(255) NOT NULL,`meta_description` varchar(255) NOT NULL,`meta_keyword` varchar(255) NOT NULL,`description` text NOT NULL,PRIMARY KEY (`ticketknowledge_section_id`,`language_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8");

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."ticketrequest` (`ticketrequest_id` int(11) NOT NULL AUTO_INCREMENT,`ticketuser_id` int(11) NOT NULL,`ticketdepartment_id` int(11) NOT NULL,`email` varchar(96) NOT NULL,`subject` varchar(255) NOT NULL,`message` text NOT NULL,`attachments` text NOT NULL,`ticketstatus_id` int(11) NOT NULL,`ip` varchar(40) NOT NULL,`date_added` datetime NOT NULL,`date_modified` datetime NOT NULL,PRIMARY KEY (`ticketrequest_id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0");

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."ticketrequest_chat` (`ticketrequest_chat_id` int(11) NOT NULL AUTO_INCREMENT,`ticketrequest_id` int(11) NOT NULL,`client_type` varchar(255) NOT NULL COMMENT 'staff, client',`ticketuser_id` int(11) NOT NULL,`message_from_user_id` int(11) NOT NULL COMMENT 'client_ticketuser_id, staff_user_id',`message` text NOT NULL,`attachments` text NOT NULL,`date_added` datetime NOT NULL,PRIMARY KEY (`ticketrequest_chat_id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0");
			
			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."ticketstatus` (`ticketstatus_id` int(11) NOT NULL AUTO_INCREMENT,`sort_order` int(11) NOT NULL,`bgcolor` varchar(255) NOT NULL,`textcolor` varchar(255) NOT NULL,PRIMARY KEY (`ticketstatus_id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0");

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."ticketstatus_description` (`ticketstatus_id` int(11) NOT NULL,`language_id` int(11) NOT NULL,`name` varchar(32) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8");

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."ticketuser` (`ticketuser_id` int(11) NOT NULL AUTO_INCREMENT,`language_id` int(11) NOT NULL,`store_id` int(11) NOT NULL,`name` varchar(32) NOT NULL,`email` varchar(96) NOT NULL,`image` varchar(255) NOT NULL,`password` varchar(40) NOT NULL,`salt` varchar(9) NOT NULL,`status` tinyint(1) NOT NULL,`ip` varchar(40) NOT NULL,`date_added` datetime NOT NULL,`date_modified` datetime NOT NULL,PRIMARY KEY (`ticketuser_id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0");

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."ticketvideo` (`ticketvideo_id` int(11) NOT NULL AUTO_INCREMENT,`sort_order` int(3) NOT NULL DEFAULT '0',`status` tinyint(1) NOT NULL DEFAULT '1',`url` text NOT NULL,PRIMARY KEY (`ticketvideo_id`)) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0");

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."ticketvideo_description` (`ticketvideo_id` int(11) NOT NULL,`language_id` int(11) NOT NULL,`title` varchar(255) NOT NULL,`sub_title` varchar(255) NOT NULL,PRIMARY KEY (`ticketvideo_id`,`language_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8");
		}
	}	
}