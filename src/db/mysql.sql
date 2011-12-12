
CREATE TABLE `hyla_object` (
  `obj_id` int(4) unsigned NOT NULL auto_increment,
  `obj_object` blob,
  `obj_description` text,
  `obj_plugin` char(255) NOT NULL default '',
  `obj_dcount` int(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (`obj_id`)
) ENGINE=MyISAM COMMENT='Table des objets du système de fichiers';


CREATE TABLE `hyla_users` (
  `usr_id` int(4) unsigned NOT NULL auto_increment,
  `usr_name` char(32) NOT NULL,
  `usr_password_hash` char(255) NOT NULL default '',
  `usr_perm` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`usr_id`),
  UNIQUE KEY `usr_name` (`usr_name`)
) ENGINE=MyISAM COMMENT='Table des utilisateurs';


CREATE TABLE `hyla_comment` (
  `comment_id` int(4) unsigned NOT NULL auto_increment,
  `comment_object` blob,
  `comment_author` char(255) NOT NULL default '',
  `comment_mail` char(255) NOT NULL default '',
  `comment_url` char(255) NOT NULL default '',
  `comment_date` int(10) unsigned NOT NULL default '0',
  `comment_content` text,
  PRIMARY KEY  (`comment_id`)
) ENGINE=MyISAM COMMENT='Table des commentaires des objets' ;


INSERT INTO `hyla_users` VALUES (1, 'Anonymous', '', 1);
INSERT INTO `hyla_users` VALUES (2, 'admin', '129ddkvpv8V8w', 127);
