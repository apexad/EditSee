<?php
//config table
$project7->db->_query("CREATE TABLE IF NOT EXISTS `".$table_prefix."config` (
  `option` varchar(32) NOT NULL,
  `data` varchar(255) NOT NULL,
  PRIMARY KEY (`option`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

$project7->db->_query("INSERT INTO `".$table_prefix."config` (`option`, `data`) VALUES
('es_description', 'A brand new EditSee site'),
('es_main_url', 'http://".$_SERVER['HTTP_HOST'].str_replace('index.php','',$_SERVER['REQUEST_URI'])."'),
('es_theme', 'adapt'),
('es_title', '".$project7->db->_escape_string($site_title)."'),
('es_posts_per_page', '5'),
('es_homepage', '!posts!'),
('es_postpage', 'posts'),
('es_email_comments', '0'),
('es_show_post_author', '0');");


//comments table
$project7->db->_query("CREATE TABLE IF NOT EXISTS `".$table_prefix."comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `linked_post_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `comment` text NOT NULL,
  `date_entered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_deleted` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;");

$project7->db->_query("INSERT INTO `".$table_prefix."comments` (`comment_id`, `linked_post_id`, `name`, `email`, `comment`, `date_entered`, 
`date_deleted`, `deleted`) VALUES
(1, 1, 'sample', 'sample@editsee.com', 'This is a sample comment!', '2011-07-17 10:08:15', '0000-00-00 00:00:00', 0);");

//custom table
$project7->db->_query("CREATE TABLE IF NOT EXISTS `".$table_prefix."custom` (
  `section` varchar(32) NOT NULL,
  `label` varchar(255) NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`section`,`label`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

$project7->db->_query("INSERT INTO `".$table_prefix."custom` (`section`, `label`, `data`) VALUES ('footer', 'Custom Footer', '');");

//links table
$project7->db->_query("CREATE TABLE IF NOT EXISTS `".$table_prefix."links` (
  `link_id` int(11) NOT NULL AUTO_INCREMENT,
  `link_order` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `title` varchar(140) NOT NULL,
  `nofollow` tinyint(1) NOT NULL,
  `target` varchar(6) NOT NULL,
  `date_deleted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`link_id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");


$project7->db->_query("INSERT INTO `".$table_prefix."links` (`link_order`, `url`, `title`, `nofollow`, `target`, `date_deleted`, `deleted`) VALUES
(1, 'http://editsee.com/', 'EditSee', 0, '_self', '0000-00-00 00:00:00', 0),
(2, 'http://www.xajax-project.org/', 'Xajax', 1, '_self', '0000-00-00 00:00:00', 0),
(3, 'http://www.nicedit.com', 'nicEdit', 1, '_blank', '0000-00-00 00:00:00', 0),
(4, 'http://www.google.com', 'Google', 0, '_blank', '0000-00-00 00:00:00', 0);");

//post table
$project7->db->_query("CREATE TABLE IF NOT EXISTS `".$table_prefix."post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `title` varchar(140) NOT NULL,
  `featured_image` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `urltag` varchar(140) NOT NULL,
  `comments` tinyint(1) NOT NULL,
  `type` char(4) NOT NULL,
  `in_nav` tinyint(1) NOT NULL,
  `page_order` int(11) NOT NULL,
  `draft` int(11) NOT NULL,
  `date_entered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_deleted` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `urltag` (`type`,`urltag`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");


$project7->db->_query("INSERT INTO `".$table_prefix."post` (`user_id`, `title`, `featured_image`, `content`, `urltag`, `comments`, `type`, `in_nav`, `page_order`, `draft`, 
`date_entered`, 
`date_deleted`, `deleted`) VALUES
(1, 'Hello World', '', '<p>This is your first EditSee post.  Be sure to change it!</p>', 'hello-world', 0, 'post', 0, 0, 0, '2011-04-30 22:00:00', '0000-00-00 00:00:00', 0),
(1, 'About', '', '<p>This is an about page.  Tell the world about yourself, or change it.</p>', 'about', 0, 'page', 1, 1, 0, '2011-04-30 22:00:00', '0000-00-00 00:00:00', 0)");

//post_tags
$project7->db->_query("CREATE TABLE IF NOT EXISTS `".$table_prefix."post_tags` (
  `post_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  `type` varchar(3) NOT NULL COMMENT '''cat'' or ''tag''',
  PRIMARY KEY (`post_id`,`tag_id`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;");

$project7->db->_query("INSERT INTO `".$table_prefix."post_tags` (`post_id`, `tag_id`, `type`) VALUES (1, 1, 'cat'),(2, 1, 'cat');");

//tags table
$project7->db->_query("CREATE TABLE IF NOT EXISTS `".$table_prefix."tags` (
  `tag_id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(255) NOT NULL,
  `type` varchar(3) NOT NULL COMMENT '''cat'' or ''tag''',
  PRIMARY KEY (`tag_id`),
  UNIQUE KEY `tag_and_type` (`tag`,`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;");

$project7->db->_query("INSERT INTO `".$table_prefix."tags` (`tag_id`, `tag`, `type`) VALUES (1, 'General', 'cat');");
?>
