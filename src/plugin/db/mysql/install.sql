
-- phpMyAdmin SQL Dump
-- version 2.9.0.2
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Vendredi 03 Novembre 2006 à 00:53
-- Version du serveur: 5.0.22
-- Version de PHP: 4.4.2-1build1
--
-- Base de données: `hyla`
--

-- --------------------------------------------------------

--
-- Structure de la table `hyla_acontrol`
--

CREATE TABLE `hyla_acontrol` (
  `ac_obj_id` int(4) unsigned NOT NULL,
  `ac_usr_id` int(4) unsigned NOT NULL,
  `ac_rights` int(10) unsigned NOT NULL default '0',
  UNIQUE KEY `ac_obj_id` (`ac_obj_id`,`ac_usr_id`)
);

-- --------------------------------------------------------

--
-- Structure de la table `hyla_comment`
--

CREATE TABLE `hyla_comment` (
  `comment_id` int(4) unsigned NOT NULL auto_increment,
  `comment_obj_id` int(4) unsigned NOT NULL,
  `comment_author` char(255) NOT NULL default '',
  `comment_mail` char(255) NOT NULL default '',
  `comment_url` char(255) NOT NULL default '',
  `comment_date` int(10) unsigned NOT NULL default '0',
  `comment_content` text,
  PRIMARY KEY  (`comment_id`)
);

-- --------------------------------------------------------

--
-- Structure de la table `hyla_group_user`
--

CREATE TABLE `hyla_group_user` (
  `grpu_usr_id` int(4) unsigned NOT NULL,
  `grpu_grp_id` int(4) unsigned NOT NULL,
  UNIQUE KEY `GRP_USR` (`grpu_usr_id`,`grpu_grp_id`)
);

-- --------------------------------------------------------

--
-- Structure de la table `hyla_object`
--

CREATE TABLE `hyla_object` (
  `obj_id` int(4) unsigned NOT NULL auto_increment,
  `obj_site_id` int(4) unsigned NOT NULL,
  `obj_file` blob,
  `obj_date_last_update` int(10) unsigned NOT NULL default '0',
  `obj_description` text,
  `obj_plugin` char(255) NOT NULL default '',
  `obj_icon` char(255) default NULL,
  `obj_dcount` int(4) unsigned NOT NULL default '0',
  `obj_flag` int(4) unsigned NOT NULL default '0',
  `obj_id_ref` int(4) unsigned NULL default '0',
  PRIMARY KEY  (`obj_id`)
);

-- --------------------------------------------------------

--
-- Structure de la table `hyla_users`
--

CREATE TABLE `hyla_users` (
  `usr_id` int(4) unsigned NOT NULL auto_increment,
  `usr_site_id` int(4) unsigned NOT NULL,
  `usr_name` char(32) NOT NULL,
  `usr_password_hash` char(255) NOT NULL default '',
  `usr_type` tinyint(3) unsigned NOT NULL default '0',
  `usr_email` char(128) NOT NULL,
  `usr_lost_pass_token` char(16) NOT NULL,
  `usr_date_create` int(10) unsigned NOT NULL default '0',
  `usr_date_last_login` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`usr_id`),
  UNIQUE KEY `usr_name` (`usr_name`)
);



CREATE TABLE `hyla_tag` (
  `tag_id` int(4) unsigned NOT NULL auto_increment,
  `tag_obj_id` int(4) unsigned NOT NULL,
  `tag_word_id` int(4) unsigned NOT NULL,
  PRIMARY KEY  (`tag_id`)
);

CREATE TABLE `hyla_tag_word` (
  `tagw_id` int(4) unsigned NOT NULL auto_increment,
  `tagw_content` int(4) unsigned NOT NULL,
  PRIMARY KEY  (`tagw_id`)
);


CREATE TABLE `hyla_log` (
  `log_id` int(4) unsigned NOT NULL auto_increment,
  `log_site_id` int(4) unsigned NOT NULL,
  `log_type` enum('debug', 'info', 'warning', 'error', 'fatal'),
  `log_context` char(64) NOT NULL,
  `log_obj_file` text,
  `log_usr_name` char(32) NOT NULL,
  `log_msg` text,
  `log_date` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY (`log_id`)
);



CREATE TABLE `hyla_site` (
  `site_id` int(4) unsigned NOT NULL auto_increment,
  `site_name` char(64) NOT NULL,
  `site_description` char(255) NOT NULL,
  `site_url` blob,
  `site_shared_dir` blob,
  `site_status` tinyint(3) unsigned NOT NULL default '1',
  PRIMARY KEY (`site_id`)
);


-- Création des utilisateurs
INSERT INTO `hyla_users` (`usr_id`, `usr_name`, `usr_password_hash`, `usr_type`) VALUES (1, 'Any', '', 0);
INSERT INTO `hyla_users` (`usr_id`, `usr_name`, `usr_password_hash`, `usr_type`) VALUES (2, 'Authenticated', '', 0);
INSERT INTO `hyla_users` (`usr_id`, `usr_name`, `usr_password_hash`, `usr_type`) VALUES (3, 'Anonymous', '', 0);
INSERT INTO `hyla_users` (`usr_id`, `usr_name`, `usr_password_hash`, `usr_type`) VALUES (4, 'admin', '129ddkvpv8V8w', 4);
-- Le mot de passe de 'admin' est 'hyla'


-- Création de l'objet root
INSERT INTO `hyla_object` (`obj_id`, `obj_file`, `obj_date_last_update`, `obj_description`, `obj_plugin`, `obj_icon`, `obj_dcount`, `obj_flag`, `obj_id_ref`) VALUES (1, '/', 0, NULL, '', NULL, 0, 0, 0);

-- Création du droit de root
--INSERT INTO `hyla_acontrol` (`ac_obj_id`, `ac_usr_id`, `ac_rights`) VALUES (1, 1, 0);
-- For testing only :
INSERT INTO `hyla_acontrol` (`ac_obj_id`, `ac_usr_id`, `ac_rights`) VALUES (1, 1, 1);




CREATE TABLE `hyla_conf` (
  `conf_id` int(4) unsigned NOT NULL auto_increment,
  `conf_site_id` int(4) unsigned NOT NULL,

  `conf_type` enum('core', 'plugin', 'user') default 'core',
  `conf_name` char(255) NOT NULL,

  -- User id
  `conf_usr_id` int(4) unsigned NOT NULL,

  -- Plugin id
  `conf_plugin_context` char(255) NOT NULL,

  `conf_content_type` enum('null', 'bool', 'int', 'float', 'string', 'array', 'object'),
  `conf_content` text,
  PRIMARY KEY (`conf_id`),
  KEY `conf_type` (`conf_type`,`conf_name`)
);


INSERT INTO `hyla_conf` (`conf_type`, `conf_name`, `conf_content_type`, `conf_content`) VALUES ('core', 'webmaster_mail', 'string', '');
INSERT INTO `hyla_conf` (`conf_type`, `conf_name`, `conf_content_type`, `conf_content`) VALUES ('core', 'template_name', 'string', 'crystal');
INSERT INTO `hyla_conf` (`conf_type`, `conf_name`, `conf_content_type`, `conf_content`) VALUES ('core', 'style', 'string', 'css/default.css');
INSERT INTO `hyla_conf` (`conf_type`, `conf_name`, `conf_content_type`, `conf_content`) VALUES ('core', 'lng', 'string', 'fr-FR');
INSERT INTO `hyla_conf` (`conf_type`, `conf_name`, `conf_content_type`, `conf_content`) VALUES ('core', 'title', 'string', '- Hyla');
INSERT INTO `hyla_conf` (`conf_type`, `conf_name`, `conf_content_type`, `conf_content`) VALUES ('core', 'file_chmod', 'int', '765');
INSERT INTO `hyla_conf` (`conf_type`, `conf_name`, `conf_content_type`, `conf_content`) VALUES ('core', 'dir_chmod', 'int', '775');
INSERT INTO `hyla_conf` (`conf_type`, `conf_name`, `conf_content_type`, `conf_content`) VALUES ('core', 'anon_file_send', 'bool', '1');
INSERT INTO `hyla_conf` (`conf_type`, `conf_name`, `conf_content_type`, `conf_content`) VALUES ('core', 'register_user', 'bool', '0');
INSERT INTO `hyla_conf` (`conf_type`, `conf_name`, `conf_content_type`, `conf_content`) VALUES ('core', 'sort', 'int', '5');
INSERT INTO `hyla_conf` (`conf_type`, `conf_name`, `conf_content_type`, `conf_content`) VALUES ('core', 'folder_first', 'bool', '1');
INSERT INTO `hyla_conf` (`conf_type`, `conf_name`, `conf_content_type`, `conf_content`) VALUES ('core', 'group_by_sort', 'bool', '1');
INSERT INTO `hyla_conf` (`conf_type`, `conf_name`, `conf_content_type`, `conf_content`) VALUES ('core', 'nbr_obj', 'int', '15');
INSERT INTO `hyla_conf` (`conf_type`, `conf_name`, `conf_content_type`, `conf_content`) VALUES ('core', 'view_hidden_file', 'bool', '0');
INSERT INTO `hyla_conf` (`conf_type`, `conf_name`, `conf_content_type`, `conf_content`) VALUES ('core', 'download_counter', 'bool', '1');
INSERT INTO `hyla_conf` (`conf_type`, `conf_name`, `conf_content_type`, `conf_content`) VALUES ('core', 'plugin_default_dir', 'string', 'Dir');
INSERT INTO `hyla_conf` (`conf_type`, `conf_name`, `conf_content_type`, `conf_content`) VALUES ('core', 'plugin_default_url', 'string', 'default');
INSERT INTO `hyla_conf` (`conf_type`, `conf_name`, `conf_content_type`, `conf_content`) VALUES ('core', 'plugin_default_auth', 'string', 'default');
INSERT INTO `hyla_conf` (`conf_type`, `conf_name`, `conf_content_type`, `conf_content`) VALUES ('core', 'view_toolbar', 'bool', '0');
INSERT INTO `hyla_conf` (`conf_type`, `conf_name`, `conf_content_type`, `conf_content`) VALUES ('core', 'view_tree', 'bool', '1');
INSERT INTO `hyla_conf` (`conf_type`, `conf_name`, `conf_content_type`, `conf_content`) VALUES ('core', 'time_of_redirection', 'int', '4');
INSERT INTO `hyla_conf` (`conf_type`, `conf_name`, `conf_content_type`, `conf_content`) VALUES ('core', 'download_dir', 'bool', '1');
INSERT INTO `hyla_conf` (`conf_type`, `conf_name`, `conf_content_type`, `conf_content`) VALUES ('core', 'download_dir_max_filesize', 'int', '15000000');
INSERT INTO `hyla_conf` (`conf_type`, `conf_name`, `conf_content_type`, `conf_content`) VALUES ('core', 'url_encode', 'bool', '1');
INSERT INTO `hyla_conf` (`conf_type`, `conf_name`, `conf_content_type`, `conf_content`) VALUES ('core', 'fs_charset_is_utf8', 'bool', '1');
INSERT INTO `hyla_conf` (`conf_type`, `conf_name`, `conf_content_type`, `conf_content`) VALUES ('core', 'rss_nbr_obj', 'int', '10');
INSERT INTO `hyla_conf` (`conf_type`, `conf_name`, `conf_content_type`, `conf_content`) VALUES ('core', 'rss_nbr_comment', 'int', '20');

