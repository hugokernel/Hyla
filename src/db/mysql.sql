
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
  `usr_name` char(32) NOT NULL,
  `usr_password_hash` char(255) NOT NULL default '',
  `usr_type` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`usr_id`),
  UNIQUE KEY `usr_name` (`usr_name`)
);



-- Création des utilisateurs
INSERT INTO `hyla_users` VALUES (1, 'Any', '', 0);
INSERT INTO `hyla_users` VALUES (2, 'Authenticated', '', 0);
INSERT INTO `hyla_users` VALUES (3, 'Anonymous', '', 0);
INSERT INTO `hyla_users` VALUES (4, 'admin', '129ddkvpv8V8w', 4);
-- Le mot de passe de 'admin' est 'hyla'


-- Création de l'objet root
INSERT INTO `hyla_object` (`obj_id`, `obj_file`, `obj_date_last_update`, `obj_description`, `obj_plugin`, `obj_icon`, `obj_dcount`, `obj_flag`, `obj_id_ref`) VALUES (1, '/', 0, NULL, '', NULL, 0, 0, 0);

-- Création du droit de root
INSERT INTO `hyla_acontrol` (`ac_obj_id`, `ac_usr_id`, `ac_rights`) VALUES (1, 1, 0);



