<?php
/* This file is under Git Control by KDSI. */
class ModelExtensionMpBlogMpBlogInstall extends Model {

	public function install() {

		// $this->db->query("DROP TABLE `". DB_PREFIX ."mpblogcategory`");
		// $this->db->query("DROP TABLE `". DB_PREFIX ."mpblogcategory_description`");
		// $this->db->query("DROP TABLE `". DB_PREFIX ."mpblogcategory_filter`");
		// $this->db->query("DROP TABLE `". DB_PREFIX ."mpblogcategory_path`");
		// $this->db->query("DROP TABLE `". DB_PREFIX ."mpblogcategory_to_store`");
		// $this->db->query("DROP TABLE `". DB_PREFIX ."mpblogcomment`");
		// $this->db->query("DROP TABLE `". DB_PREFIX ."mpblogpost`");
		// $this->db->query("DROP TABLE `". DB_PREFIX ."mpblogpost_description`");
		// $this->db->query("DROP TABLE `". DB_PREFIX ."mpblogpost_image`");
		// $this->db->query("DROP TABLE `". DB_PREFIX ."mpblogpost_like`");
		// $this->db->query("DROP TABLE `". DB_PREFIX ."mpblogpost_related`");
		// $this->db->query("DROP TABLE `". DB_PREFIX ."mpblogpost_relatedcategory`");
		// $this->db->query("DROP TABLE `". DB_PREFIX ."mpblogpost_relatedproduct`");
		// $this->db->query("DROP TABLE `". DB_PREFIX ."mpblogpost_to_mpblogcategory`");
		// $this->db->query("DROP TABLE `". DB_PREFIX ."mpblogpost_to_store`");
		// $this->db->query("DROP TABLE `". DB_PREFIX ."mpblograting`");
		// $this->db->query("DROP TABLE `". DB_PREFIX ."mpblogsubscribers`");
		// $this->db->query("DROP TABLE `". DB_PREFIX ."mpblogsubscribers_verification`");

		$language_query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "language");
		$languages = $language_query->rows;


		$query = $this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."mpblogcategory' ");

		if(!$query->num_rows) {

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpblogcategory` (
			`mpblogcategory_id` int(11) NOT NULL AUTO_INCREMENT,
			`image` varchar(255) DEFAULT NULL,
			`parent_id` int(11) NOT NULL DEFAULT '0',
			`viewed` int(11) NOT NULL,
			`sort_order` int(3) NOT NULL DEFAULT '0',
			`status` tinyint(1) NOT NULL,
			`date_added` datetime NOT NULL,
			`date_modified` datetime NOT NULL,
			PRIMARY KEY (`mpblogcategory_id`),
			KEY `parent_id` (`parent_id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=78 ;");

			$this->db->query("INSERT INTO `". DB_PREFIX ."mpblogcategory` (`mpblogcategory_id`, `image`, `parent_id`, `viewed`, `sort_order`, `status`, `date_added`, `date_modified`) VALUES
			(72, 'catalog/mpblog/demo-5.jpg', 0, 0, 0, 1, '2017-06-07 14:49:45', '2017-06-09 19:20:56'),
			(73, 'catalog/mpblog/demo-12.jpg', 72, 0, 0, 1, '2017-06-09 18:30:39', '2017-06-09 19:23:39'),
			(74, 'catalog/mpblog/demo-6.jpg', 72, 0, 0, 1, '2017-06-09 19:24:38', '2017-06-09 19:30:14'),
			(75, 'catalog/mpblog/demo-13.jpg', 72, 0, 0, 1, '2017-06-09 19:25:22', '2017-06-09 19:30:32'),
			(76, 'catalog/mpblog/demo-2.jpg', 72, 0, 0, 1, '2017-06-09 19:26:04', '2017-06-09 19:26:23'),
			(77, 'catalog/mpblog/demo-9.jpg', 72, 0, 0, 1, '2017-06-09 19:27:04', '2017-06-09 19:27:26');");
		}

		$query = $this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."mpblogcategory_description' ");

		if(!$query->num_rows) {

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpblogcategory_description` (
			`mpblogcategory_id` int(11) NOT NULL,
			`language_id` int(11) NOT NULL,
			`name` varchar(255) NOT NULL,
			`description` text NOT NULL,
			`meta_title` varchar(255) NOT NULL,
			`meta_description` varchar(255) NOT NULL,
			`meta_keyword` varchar(255) NOT NULL,
			PRIMARY KEY (`mpblogcategory_id`,`language_id`),
			KEY `name` (`name`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

			foreach ($languages as $language) {

			$this->db->query("INSERT INTO `". DB_PREFIX ."mpblogcategory_description` (`mpblogcategory_id`, `language_id`, `name`, `description`, `meta_title`, `meta_description`, `meta_keyword`) VALUES
			(72, ".$language['language_id'].", 'Latest Fashion', '&lt;p&gt;The\r\n German automobile manufacturer BMW launched its 6 Series in India in \r\n2007. The range of cars was quite obviously meant for those \r\ndistinguished super-rich Indian consumers. The second-generation \r\ntwo-door coupe flaunts striking exteriors, which are well complemented \r\nby spacious interiors. The sports-car-look of the vehicle gives it a \r\nsmooth look and feel and the engine gives it the energy to surge to top \r\nspeeds. &lt;br&gt;&lt;/p&gt;', 'Latest Fashion', '', ''),
			(73, ".$language['language_id'].", 'Clothes', '&lt;p&gt;Clothes&lt;/p&gt;', 'Clothes', '', ''),
			(74, ".$language['language_id'].", 'Populor', '&lt;p&gt;Populor&lt;br&gt;&lt;/p&gt;', 'Populor', '', ''),
			(75, ".$language['language_id'].", 'Women Fashion', '&lt;p&gt;Women Fashion&lt;br&gt;&lt;/p&gt;', 'Women Fashion', '', ''),
			(76, ".$language['language_id'].", 'Girls', '', 'Girls', '', ''),
			(77, ".$language['language_id'].", 'Sale Clothes', '', 'Sale Clothes', '', '');");

			}

		}

		$query = $this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."mpblogcategory_filter' ");

		if(!$query->num_rows) {

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpblogcategory_filter` (
			  `mpblogcategory_id` int(11) NOT NULL,
			  `filter_id` int(11) NOT NULL,
			  PRIMARY KEY (`mpblogcategory_id`,`filter_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

		}

		$query = $this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."mpblogcategory_path' ");

		if(!$query->num_rows) {

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpblogcategory_path` (
			  `mpblogcategory_id` int(11) NOT NULL,
			  `path_id` int(11) NOT NULL,
			  `level` int(11) NOT NULL,
			  PRIMARY KEY (`mpblogcategory_id`,`path_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;");


			$this->db->query("INSERT INTO `". DB_PREFIX ."mpblogcategory_path` (`mpblogcategory_id`, `path_id`, `level`) VALUES
			(72, 72, 0),
			(73, 72, 0),
			(73, 73, 1),
			(74, 74, 1),
			(75, 75, 1),
			(76, 76, 1),
			(76, 72, 0),
			(77, 77, 1),
			(77, 72, 0),
			(74, 72, 0),
			(75, 72, 0);");
		}

		$query = $this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."mpblogcategory_to_store' ");

		if(!$query->num_rows) {

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpblogcategory_to_store` (
			  `mpblogcategory_id` int(11) NOT NULL,
			  `store_id` int(11) NOT NULL,
			  PRIMARY KEY (`mpblogcategory_id`,`store_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;");


			$this->db->query("INSERT INTO `". DB_PREFIX ."mpblogcategory_to_store` (`mpblogcategory_id`, `store_id`) VALUES
			(72, 0),
			(73, 0),
			(74, 0),
			(75, 0),
			(76, 0),
			(77, 0);");
		}

		$query = $this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."mpblogcomment' ");

		if(!$query->num_rows) {

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpblogcomment` (
			  `mpblogcomment_id` int(11) NOT NULL AUTO_INCREMENT,
			  `mpblogpost_id` int(11) NOT NULL,
			  `customer_id` int(11) NOT NULL,
			  `author` varchar(64) NOT NULL,
			  `text` text NOT NULL,
			  `status` tinyint(1) NOT NULL DEFAULT '0',
			  `date_added` datetime NOT NULL,
			  `date_modified` datetime NOT NULL,
			  PRIMARY KEY (`mpblogcomment_id`),
			  KEY `product_id` (`mpblogpost_id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;");

			$this->db->query("INSERT INTO `". DB_PREFIX ."mpblogcomment` (`mpblogcomment_id`, `mpblogpost_id`, `customer_id`, `author`, `text`, `status`, `date_added`, `date_modified`) VALUES
			(9, 59, 1, 'Module Points', 'All blogs are very useful must read!!', 1, '2017-06-07 17:23:01', '0000-00-00 00:00:00'),
			(10, 59, 1, 'Module Points', 'Great Work!!! Keep it up Let''s Rock', 1, '2017-06-07 17:40:09', '0000-00-00 00:00:00');");

		}

		$query = $this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."mpblogpost' ");

		if(!$query->num_rows) {

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpblogpost` (
			  `mpblogpost_id` int(11) NOT NULL AUTO_INCREMENT,
			  `image` varchar(255) DEFAULT NULL,
			  `author` varchar(255) NOT NULL,
			  `sort_order` int(11) NOT NULL DEFAULT '0',
			  `video` varchar(255) NOT NULL,
			  `posttype` varchar(20) NOT NULL,
			  `status` tinyint(1) NOT NULL DEFAULT '0',
			  `likes` bigint(20) NOT NULL,
			  `viewed` bigint(20) NOT NULL DEFAULT '0',
			  `date_added` datetime NOT NULL,
			  `date_available` date NOT NULL,
			  `date_modified` datetime NOT NULL,
			  PRIMARY KEY (`mpblogpost_id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=65 ;");

			$this->db->query("INSERT INTO `". DB_PREFIX ."mpblogpost` (`mpblogpost_id`, `image`, `author`, `sort_order`, `video`, `posttype`, `status`, `likes`, `viewed`, `date_added`, `date_available`, `date_modified`) VALUES
			(59, 'catalog/mpblog/demo-3.jpg', 'admin', 1, '', 'IMAGES', 1, 0, 90, '2017-06-07 16:03:37', '2017-06-07', '2017-06-09 18:34:55'),
			(60, 'catalog/mpblog/demo-10.jpg', 'admin', 1, 'BMW-6', 'ARTICAL', 1, 0, 61, '2017-06-08 11:28:10', '2017-06-08', '2017-06-09 18:28:22'),
			(61, '', 'admin', 1, 'https://www.youtube.com/embed/LHQSh72DjEY', 'VIDEO', 1, 0, 25, '2017-06-08 12:17:31', '2017-06-08', '2017-06-10 10:54:59'),
			(64, '', 'admin', 1, 'https://www.youtube.com/watch?v=LJUQa-Iabdc', 'VIDEO', 1, 0, 2, '2017-06-10 10:57:29', '2017-06-10', '0000-00-00 00:00:00'),
			(62, 'catalog/mpblog/demo-4.jpg', 'admin', 1, 'https://www.youtube.com/embed/bNa3kQ_J_3o', 'IMAGES', 1, 0, 4, '2017-06-08 12:21:26', '2017-06-08', '2017-06-09 18:36:52'),
			(63, 'catalog/mpblog/demo-1.jpg', 'admin', 1, '', 'IMAGES', 1, 0, 31, '2017-06-08 12:34:52', '2017-06-08', '2017-06-09 18:43:40');");

		}

		$query = $this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."mpblogpost_description' ");

		if(!$query->num_rows) {


			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpblogpost_description` (
			  `mpblogpost_id` int(11) NOT NULL,
			  `language_id` int(11) NOT NULL,
			  `name` varchar(255) NOT NULL,
			  `sdescription` text NOT NULL,
			  `description` text NOT NULL,
			  `tag` text NOT NULL,
			  `meta_title` varchar(255) NOT NULL,
			  `meta_description` varchar(255) NOT NULL,
			  `meta_keyword` varchar(255) NOT NULL,
			  PRIMARY KEY (`mpblogpost_id`,`language_id`),
			  KEY `name` (`name`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

			foreach ($languages as $language) {

				$this->db->query("INSERT INTO `". DB_PREFIX ."mpblogpost_description` (`mpblogpost_id`, `language_id`, `name`, `sdescription`, `description`, `tag`, `meta_title`, `meta_description`, `meta_keyword`) VALUES
				(59, ".$language['language_id'].", 'Lorem Ipsum is not simply random text', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry''s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum', '&lt;h3&gt;The standard Lorem Ipsum passage, used since the 1500s&lt;/h3&gt;&lt;p&gt;&quot;Lorem\r\n ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod \r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim \r\nveniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea \r\ncommodo consequat. Duis aute irure dolor in reprehenderit in voluptate \r\nvelit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint \r\noccaecat cupidatat non proident, sunt in culpa qui officia deserunt \r\nmollit anim id est laborum.&quot;&lt;/p&gt;&lt;h3&gt;Section 1.10.32 of &quot;de Finibus Bonorum et Malorum&quot;, written by Cicero in 45 BC&lt;/h3&gt;&lt;p&gt;&quot;Sed\r\n ut perspiciatis unde omnis iste natus error sit voluptatem accusantium \r\ndoloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo \r\ninventore veritatis et quasi architecto beatae vitae dicta sunt \r\nexplicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut \r\nodit aut fugit, sed quia consequuntur magni dolores eos qui ratione \r\nvoluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum \r\nquia dolor sit amet, consectetur, adipisci velit, sed quia non numquam \r\neius modi tempora incidunt ut labore et dolore magnam aliquam quaerat \r\nvoluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam \r\ncorporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur?\r\n Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse \r\nquam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo \r\nvoluptas nulla pariatur?&quot;&lt;/p&gt;&lt;h3&gt;1914 translation by H. Rackham&lt;/h3&gt;&lt;p&gt;&quot;But I must explain to you how all this mistaken idea of denouncing \r\npleasure and praising pain was born and I will give you a complete \r\naccount of the system, and expound the actual teachings of the great \r\nexplorer of the truth, the master-builder of human happiness. No one \r\nrejects, dislikes, or avoids pleasure itself, because it is pleasure, \r\nbut because those who do not know how to pursue pleasure rationally \r\nencounter consequences that are extremely painful. Nor again is there \r\nanyone who loves or pursues or desires to obtain pain of itself, because\r\n it is pain, but because occasionally circumstances occur in which toil \r\nand pain can procure him some great pleasure. To take a trivial example,\r\n which of us ever undertakes laborious physical exercise, except to \r\nobtain some advantage from it? But who has any right to find fault with a\r\n man who chooses to enjoy a pleasure that has no annoying consequences, \r\nor one who avoids a pain that produces no resultant pleasure?&quot;&lt;/p&gt;&lt;h3&gt;Section 1.10.33 of &quot;de Finibus Bonorum et Malorum&quot;, written by Cicero in 45 BC&lt;/h3&gt;&lt;p&gt;&quot;At vero eos et accusamus et iusto odio dignissimos ducimus qui \r\nblanditiis praesentium voluptatum deleniti atque corrupti quos dolores \r\net quas molestias excepturi sint occaecati cupiditate non provident, \r\nsimilique sunt in culpa qui officia deserunt mollitia animi, id est \r\nlaborum et dolorum fuga. Et harum quidem rerum facilis est et expedita \r\ndistinctio. Nam libero tempore, cum soluta nobis est eligendi optio \r\ncumque nihil impedit quo minus id quod maxime placeat facere possimus, \r\nomnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem \r\nquibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet\r\n ut et voluptates repudiandae sint et molestiae non recusandae. Itaque \r\nearum rerum hic tenetur a sapiente delectus, ut aut reiciendis \r\nvoluptatibus maiores alias consequatur aut perferendis doloribus \r\nasperiores repellat.&quot;&lt;/p&gt;&lt;p&gt;&lt;br&gt;&lt;/p&gt;', 'New, Old, Populor, Latest, Random, Featured, Special, Trending', 'Lorem Ipsum is not simply random text', '', ''),
				(60, ".$language['language_id'].", 'Lorem Ipsum is simply dummy text of the printing', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry''s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.', '&lt;p&gt;\r\n&lt;/p&gt;&lt;h3&gt;Section 1.10.33 of &quot;de Finibus Bonorum et Malorum&quot;, written by Cicero in 45 BC&lt;/h3&gt;&lt;p&gt;\r\n&lt;/p&gt;&lt;p&gt;&quot;But I must explain to you how all this mistaken idea of denouncing \r\npleasure and praising pain was born and I will give you a complete \r\naccount of the system, and expound the actual teachings of the great \r\nexplorer of the truth, the master-builder of human happiness. No one \r\nrejects, dislikes, or avoids pleasure itself, because it is pleasure, \r\nbut because those who do not know how to pursue pleasure rationally \r\nencounter consequences that are extremely painful. Nor again is there \r\nanyone who loves or pursues or desires to obtain pain of itself, because\r\n it is pain, but because occasionally circumstances occur in which toil \r\nand pain can procure him some great pleasure. To take a trivial example,\r\n which of us ever undertakes laborious physical exercise, except to \r\nobtain some advantage from it? But who has any right to find fault with a\r\n man who chooses to enjoy a pleasure that has no annoying consequences, \r\nor one who avoids a pain that produces no resultant pleasure?&quot;&lt;/p&gt;&lt;p&gt;\r\n&lt;/p&gt;&lt;h3&gt;1914 translation by H. Rackham&lt;/h3&gt;&lt;p&gt;\r\n&lt;/p&gt;&lt;p&gt;&quot;Sed\r\n ut perspiciatis unde omnis iste natus error sit voluptatem accusantium \r\ndoloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo \r\ninventore veritatis et quasi architecto beatae vitae dicta sunt \r\nexplicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut \r\nodit aut fugit, sed quia consequuntur magni dolores eos qui ratione \r\nvoluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum \r\nquia dolor sit amet, consectetur, adipisci velit, sed quia non numquam \r\neius modi tempora incidunt ut labore et dolore magnam aliquam quaerat \r\nvoluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam \r\ncorporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur?\r\n Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse \r\nquam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo \r\nvoluptas nulla pariatur?&quot;&lt;/p&gt;&lt;h3&gt;The standard Lorem Ipsum passage, used since the 1500s&lt;/h3&gt;&lt;p&gt;&quot;Lorem\r\n ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod \r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim \r\nveniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea \r\ncommodo consequat. Duis aute irure dolor in reprehenderit in voluptate \r\nvelit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint \r\noccaecat cupidatat non proident, sunt in culpa qui officia deserunt \r\nmollit anim id est laborum.&quot;&lt;/p&gt;&lt;h3&gt;Section 1.10.32 of &quot;de Finibus Bonorum et Malorum&quot;, written by Cicero in 45 BC&lt;/h3&gt;&lt;p&gt;&quot;At vero eos et accusamus et iusto odio dignissimos ducimus qui \r\nblanditiis praesentium voluptatum deleniti atque corrupti quos dolores \r\net quas molestias excepturi sint occaecati cupiditate non provident, \r\nsimilique sunt in culpa qui officia deserunt mollitia animi, id est \r\nlaborum et dolorum fuga. Et harum quidem rerum facilis est et expedita \r\ndistinctio. Nam libero tempore, cum soluta nobis est eligendi optio \r\ncumque nihil impedit quo minus id quod maxime placeat facere possimus, \r\nomnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem \r\nquibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet\r\n ut et voluptates repudiandae sint et molestiae non recusandae. Itaque \r\nearum rerum hic tenetur a sapiente delectus, ut aut reiciendis \r\nvoluptatibus maiores alias consequatur aut perferendis doloribus \r\nasperiores repellat.&quot;&lt;/p&gt;&lt;p&gt;&lt;b&gt;&lt;br&gt;&lt;/b&gt;&lt;/p&gt;', '', 'Lorem Ipsum is simply dummy text of the printing', '', ''),
				(64, ".$language['language_id'].", 'HOW TO MAKE ANY BASIC OUTFIT LOOK GOOD! / FASHION HACKS', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry''s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book', '&lt;h3&gt;Section 1.10.33 of &quot;de Finibus Bonorum et Malorum&quot;, written by Cicero in 45 BC&lt;/h3&gt;&lt;p&gt;&quot;But I must explain to you how all this mistaken idea of denouncing \r\npleasure and praising pain was born and I will give you a complete \r\naccount of the system, and expound the actual teachings of the great \r\nexplorer of the truth, the master-builder of human happiness. No one \r\nrejects, dislikes, or avoids pleasure itself, because it is pleasure, \r\nbut because those who do not know how to pursue pleasure rationally \r\nencounter consequences that are extremely painful. Nor again is there \r\nanyone who loves or pursues or desires to obtain pain of itself, because\r\n it is pain, but because occasionally circumstances occur in which toil \r\nand pain can procure him some great pleasure. To take a trivial example,\r\n which of us ever undertakes laborious physical exercise, except to \r\nobtain some advantage from it? But who has any right to find fault with a\r\n man who chooses to enjoy a pleasure that has no annoying consequences, \r\nor one who avoids a pain that produces no resultant pleasure?&quot;&lt;/p&gt;&lt;h3&gt;1914 translation by H. Rackham&lt;/h3&gt;&lt;p&gt;&quot;Sed\r\n ut perspiciatis unde omnis iste natus error sit voluptatem accusantium \r\ndoloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo \r\ninventore veritatis et quasi architecto beatae vitae dicta sunt \r\nexplicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut \r\nodit aut fugit, sed quia consequuntur magni dolores eos qui ratione \r\nvoluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum \r\nquia dolor sit amet, consectetur, adipisci velit, sed quia non numquam \r\neius modi tempora incidunt ut labore et dolore magnam aliquam quaerat \r\nvoluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam \r\ncorporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur?\r\n Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse \r\nquam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo \r\nvoluptas nulla pariatur?&quot;&lt;/p&gt;&lt;h3&gt;The standard Lorem Ipsum passage, used since the 1500s&lt;/h3&gt;&lt;p&gt;&quot;Lorem\r\n ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod \r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim \r\nveniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea \r\ncommodo consequat. Duis aute irure dolor in reprehenderit in voluptate \r\nvelit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint \r\noccaecat cupidatat non proident, sunt in culpa qui officia deserunt \r\nmollit anim id est laborum.&quot;&lt;/p&gt;&lt;h3&gt;Section 1.10.32 of &quot;de Finibus Bonorum et Malorum&quot;, written by Cicero in 45 BC&lt;/h3&gt;&lt;p&gt;&quot;At vero eos et accusamus et iusto odio dignissimos ducimus qui \r\nblanditiis praesentium voluptatum deleniti atque corrupti quos dolores \r\net quas molestias excepturi sint occaecati cupiditate non provident, \r\nsimilique sunt in culpa qui officia deserunt mollitia animi, id est \r\nlaborum et dolorum fuga. Et harum quidem rerum facilis est et expedita \r\ndistinctio. Nam libero tempore, cum soluta nobis est eligendi optio \r\ncumque nihil impedit quo minus id quod maxime placeat facere possimus, \r\nomnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem \r\nquibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet\r\n ut et voluptates repudiandae sint et molestiae non recusandae. Itaque \r\nearum rerum hic tenetur a sapiente delectus, ut aut reiciendis \r\nvoluptatibus maiores alias consequatur aut perferendis doloribus \r\nasperiores repellat.&quot;&lt;/p&gt;&lt;p&gt;&lt;br&gt;&lt;/p&gt;', '', 'HOW TO MAKE ANY BASIC OUTFIT LOOK GOOD! / FASHION HACKS', 'HOW TO MAKE ANY BASIC OUTFIT LOOK GOOD! / FASHION HACKS', 'HOW TO MAKE ANY BASIC OUTFIT LOOK GOOD! / FASHION HACKS'),
				(63, ".$language['language_id'].", 'Want Edublogs for everyone?', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry''s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', '&lt;p&gt;Aficionados and brand historians will recall that BMW launched the original 6-series in 1976 as a replacement for the beloved E9 cars, which included the brand-defining 3.0 CS and CSL coupes. That sharknose 6er was an icon of ’70s and ’80s moving and shaking, with the M635CSi standing at the top of the heap. Then the 6 vanished, supplanted by the 8-series, which offered V-8 and V-12 powerplants as well as a miniaturized kidney grille to better accommodate its crucial pop-up headlights. When the 6er returned in the guise of a German Camaro fourteen years ago, it naturally first appeared as a coupe. And now it’s gone again. Kind of.&lt;br&gt;&lt;br&gt;The news was first reported by our friends at Road &amp;amp; Track. While the 6-series convertible and Gran Coupe four-door continue, coupe production for American consumption quietly stopped back in February, with no word from BMW as to whether it will restart. While cars remain on dealer lots, they’re not making any more of the things, so if you want a 6-series two-door with a fixed roof, it might be best to visit your BMW store sooner rather than later.&lt;br&gt;&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp; Mercedes Soon Will Offer Eight (!) Front-Drive Models&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp; 2017 BMW 540i xDrive Instrumented Test&lt;br&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp; BMW 6-series: Full Pricing, News, Reviews, Photos, and More&lt;br&gt;&lt;br&gt;Have the Germans defeated themselves with all these four-door coupes? Truth be told, we think the current 6er works best as a four-door or a large open car. Clearly, the masses are with us, but we do love to love a BMW coupe. Don’t you? Are you pining? We’re pining a little. We admit it.&lt;br&gt;&lt;/p&gt;', '', 'Want Edublogs for everyone?', 'Want Edublogs for everyone?', 'Want Edublogs for everyone?'),
				(62, ".$language['language_id'].", 'The standard Lorem Ipsum passage', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry''s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', '&lt;h3&gt;The standard Lorem Ipsum passage, used since the 1500s&lt;/h3&gt;&lt;p&gt;&quot;Lorem\r\n ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod \r\ntempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim \r\nveniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea \r\ncommodo consequat. Duis aute irure dolor in reprehenderit in voluptate \r\nvelit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint \r\noccaecat cupidatat non proident, sunt in culpa qui officia deserunt \r\nmollit anim id est laborum.&quot;&lt;/p&gt;&lt;h3&gt;Section 1.10.32 of &quot;de Finibus Bonorum et Malorum&quot;, written by Cicero in 45 BC&lt;/h3&gt;&lt;p&gt;&quot;Sed\r\n ut perspiciatis unde omnis iste natus error sit voluptatem accusantium \r\ndoloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo \r\ninventore veritatis et quasi architecto beatae vitae dicta sunt \r\nexplicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut \r\nodit aut fugit, sed quia consequuntur magni dolores eos qui ratione \r\nvoluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum \r\nquia dolor sit amet, consectetur, adipisci velit, sed quia non numquam \r\neius modi tempora incidunt ut labore et dolore magnam aliquam quaerat \r\nvoluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam \r\ncorporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur?\r\n Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse \r\nquam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo \r\nvoluptas nulla pariatur?&quot;&lt;/p&gt;&lt;h3&gt;1914 translation by H. Rackham&lt;/h3&gt;&lt;p&gt;&quot;But I must explain to you how all this mistaken idea of denouncing \r\npleasure and praising pain was born and I will give you a complete \r\naccount of the system, and expound the actual teachings of the great \r\nexplorer of the truth, the master-builder of human happiness. No one \r\nrejects, dislikes, or avoids pleasure itself, because it is pleasure, \r\nbut because those who do not know how to pursue pleasure rationally \r\nencounter consequences that are extremely painful. Nor again is there \r\nanyone who loves or pursues or desires to obtain pain of itself, because\r\n it is pain, but because occasionally circumstances occur in which toil \r\nand pain can procure him some great pleasure. To take a trivial example,\r\n which of us ever undertakes laborious physical exercise, except to \r\nobtain some advantage from it? But who has any right to find fault with a\r\n man who chooses to enjoy a pleasure that has no annoying consequences, \r\nor one who avoids a pain that produces no resultant pleasure?&quot;&lt;/p&gt;&lt;h3&gt;Section 1.10.33 of &quot;de Finibus Bonorum et Malorum&quot;, written by Cicero in 45 BC&lt;/h3&gt;&lt;p&gt;&quot;At vero eos et accusamus et iusto odio dignissimos ducimus qui \r\nblanditiis praesentium voluptatum deleniti atque corrupti quos dolores \r\net quas molestias excepturi sint occaecati cupiditate non provident, \r\nsimilique sunt in culpa qui officia deserunt mollitia animi, id est \r\nlaborum et dolorum fuga. Et harum quidem rerum facilis est et expedita \r\ndistinctio. Nam libero tempore, cum soluta nobis est eligendi optio \r\ncumque nihil impedit quo minus id quod maxime placeat facere possimus, \r\nomnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem \r\nquibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet\r\n ut et voluptates repudiandae sint et molestiae non recusandae. Itaque \r\nearum rerum hic tenetur a sapiente delectus, ut aut reiciendis \r\nvoluptatibus maiores alias consequatur aut perferendis doloribus \r\nasperiores repellat.&quot;&lt;/p&gt;&lt;p&gt;&lt;br&gt;&lt;/p&gt;', '', 'The standard Lorem Ipsum passage', 'The standard Lorem Ipsum passage', 'The standard Lorem Ipsum passage'),
				(61, ".$language['language_id'].", 'Fashion TRENDS of 2016-2017', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry''s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', 'It’s spring, which means it’s time to clean out the house, dust off the outdoor gear, and get rid of all those extra Bugattis crowding your garage. Don’t be a hoarder, take a queue from boxing legend Floyd Mayweather and tidy your stuff up.\r\n\r\n\r\nMayweather is offering drivers with a few extra million dollars on the side the opportunity to own a small piece of automotive history with the chance to buy one, or two, of his Bugatti Veyron Grand Sports. While he currently owns three, he’s only selling his matte white 2011 Grand Sport and the Grand Sport Vitesse he bought in 2015. Both cars only have just over 1,000 miles on the clock, making them practically new.\r\n\r\n\r\nPresumably, Mayweather is clearing some space in his garage for a new Bugatti Chiron, which has an impressive W16 engine with 1,500hp, so it’s pretty quick.\r\n\r\nIf you find yourself wavering on whether you should buy one, or both, of the Veyrons up for sale, think about how buying used is not only the environmentally smart thing to do, it’s also fiscally sensible compared to buying new! Do you really need another reason to call Luxury Auto Collection and buy these cars?', '', 'Fashion TRENDS of 2016-2017', 'Fashion TRENDS of 2016-2017', 'Fashion TRENDS of 2016-2017');");

			}

		}

		$query = $this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."mpblogpost_image' ");

		if(!$query->num_rows) {

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpblogpost_image` (
			  `mpblogpost_image_id` int(11) NOT NULL AUTO_INCREMENT,
			  `mpblogpost_id` int(11) NOT NULL,
			  `image` varchar(255) DEFAULT NULL,
			  `sort_order` int(3) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`mpblogpost_image_id`),
			  KEY `product_id` (`mpblogpost_id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=177 ;");

			$this->db->query("INSERT INTO `". DB_PREFIX ."mpblogpost_image` (`mpblogpost_image_id`, `mpblogpost_id`, `image`, `sort_order`) VALUES
			(158, 60, '', 0),
			(159, 60, '', 0),
			(164, 62, '', 0),
			(163, 62, '', 0),
			(176, 61, '', 0),
			(165, 63, 'catalog/mpblog/demo-2.jpg', 0),
			(167, 63, 'catalog/mpblog/demo-3.jpg', 0),
			(168, 63, 'catalog/mpblog/demo-8.jpg', 0),
			(160, 60, '', 0),
			(166, 63, 'catalog/mpblog/demo-4.jpg', 0);");

		}

		$query = $this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."mpblogpost_like' ");

		if(!$query->num_rows) {
			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpblogpost_like` (
			  `mpblogpost_like_id` int(11) NOT NULL AUTO_INCREMENT,
			  `mpblogpost_id` int(11) NOT NULL,
			  `like_status` int(11) NOT NULL,
			  `customer_id` int(11) NOT NULL,
			  PRIMARY KEY (`mpblogpost_like_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
		}

		$query = $this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."mpblogpost_related' ");

		if(!$query->num_rows) {

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpblogpost_related` (
			  `mpblogpost_id` int(11) NOT NULL,
			  `related_id` int(11) NOT NULL,
			  PRIMARY KEY (`mpblogpost_id`,`related_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

			$this->db->query("INSERT INTO `". DB_PREFIX ."mpblogpost_related` (`mpblogpost_id`, `related_id`) VALUES
			(59, 60),
			(59, 61),
			(60, 59),
			(60, 61),
			(60, 63),
			(61, 59),
			(61, 60),
			(61, 62),
			(61, 63),
			(62, 61),
			(62, 63),
			(63, 60),
			(63, 61),
			(63, 62);");
		}

		$query = $this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."mpblogpost_relatedcategory' ");

		if(!$query->num_rows) {

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpblogpost_relatedcategory` (
			  `mpblogpost_id` int(11) NOT NULL,
			  `related_id` int(11) NOT NULL,
			  PRIMARY KEY (`mpblogpost_id`,`related_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

			$this->db->query("INSERT INTO `". DB_PREFIX ."mpblogpost_relatedcategory` (`mpblogpost_id`, `related_id`) VALUES
			(59, 20),
			(59, 25),
			(59, 33),
			(60, 25);");

		}

		$query = $this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."mpblogpost_relatedproduct' ");

		if(!$query->num_rows) {

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpblogpost_relatedproduct` (
			  `mpblogpost_id` int(11) NOT NULL,
			  `related_id` int(11) NOT NULL,
			  PRIMARY KEY (`mpblogpost_id`,`related_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

			$this->db->query("INSERT INTO `". DB_PREFIX ."mpblogpost_relatedproduct` (`mpblogpost_id`, `related_id`) VALUES
			(59, 28),
			(59, 30),
			(59, 41),
			(60, 41);");

		}

		$query = $this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."mpblogpost_to_mpblogcategory' ");

		if(!$query->num_rows) {


			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpblogpost_to_mpblogcategory` (
			  `mpblogpost_id` int(11) NOT NULL,
			  `mpblogcategory_id` int(11) NOT NULL,
			  PRIMARY KEY (`mpblogpost_id`,`mpblogcategory_id`),
			  KEY `mpblogcategory_id` (`mpblogcategory_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

			$this->db->query("INSERT INTO `". DB_PREFIX ."mpblogpost_to_mpblogcategory` (`mpblogpost_id`, `mpblogcategory_id`) VALUES
			(59, 72),
			(60, 72),
			(61, 72),
			(62, 72),
			(63, 72),
			(64, 72);");

		}

		$query = $this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."mpblogpost_to_store' ");

		if(!$query->num_rows) {

			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpblogpost_to_store` (
			  `mpblogpost_id` int(11) NOT NULL,
			  `store_id` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`mpblogpost_id`,`store_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

			$this->db->query("INSERT INTO `". DB_PREFIX ."mpblogpost_to_store` (`mpblogpost_id`, `store_id`) VALUES
			(59, 0),
			(60, 0),
			(61, 0),
			(62, 0),
			(63, 0),
			(64, 0);");

		}

		$query = $this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."mpblograting' ");

		if(!$query->num_rows) {


			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpblograting` (
			  `mpblograting_id` int(11) NOT NULL AUTO_INCREMENT,
			  `mpblogpost_id` int(11) NOT NULL,
			  `customer_id` int(11) NOT NULL,
			  `rating` int(1) NOT NULL,
			  `status` tinyint(1) NOT NULL DEFAULT '0',
			  `date_added` datetime NOT NULL,
			  `date_modified` datetime NOT NULL,
			  PRIMARY KEY (`mpblograting_id`),
			  KEY `product_id` (`mpblogpost_id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;");

		}

		$query = $this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."mpblogsubscribers' ");

		if(!$query->num_rows) {
			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpblogsubscribers` (
			  `mpblogsubscribers_id` int(11) NOT NULL AUTO_INCREMENT,
			  `customer_id` int(11) NOT NULL,
			  `store_id` int(11) NOT NULL,
			  `language_id` int(11) NOT NULL,
			  `email` varchar(150) NOT NULL,
			  `status` tinyint(4) NOT NULL,
			  `approval_by` varchar(10) NOT NULL,
			  `date_added` datetime NOT NULL,
			  `date_modified` datetime NOT NULL,
			  PRIMARY KEY (`mpblogsubscribers_id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0 ;");
		}

		$query = $this->db->query("SHOW TABLES LIKE '". DB_PREFIX ."mpblogsubscribers_verification' ");

		if(!$query->num_rows) {
			$this->db->query("CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."mpblogsubscribers_verification` (
			  `mpblogsubscribers_verification_id` int(11) NOT NULL AUTO_INCREMENT,
			  `mpblogsubscribers_id` int(11) NOT NULL,
			  `code` varchar(20) CHARACTER SET latin1 NOT NULL,
			  `action` varchar(20) CHARACTER SET latin1 NOT NULL COMMENT 'SUBSCRIBE,UNSUBSCRIBE',
			  `status` int(11) NOT NULL COMMENT '1=code generate, 0=verified, 2=canceled',
			  `date_added` datetime NOT NULL,
			  `date_modified` datetime NOT NULL,
			  PRIMARY KEY (`mpblogsubscribers_verification_id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;");
		}
		/*
		routes: mpblog/blogs -  Mp-Blogs
		mpblog/blog -  Mp-Blog
		mpblog/blogcategory - Mp-BlogCategory
		*/
	}
}