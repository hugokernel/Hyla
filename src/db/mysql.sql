

CREATE TABLE list_object (
	obj_id				int(4) unsigned NOT NULL auto_increment,
	obj_object			text,
	obj_description		text,
	obj_plugin			char(255) NOT NULL default '',
	obj_dcount			int(4) unsigned NOT NULL default 0,
	PRIMARY KEY  (obj_id)
) TYPE=MyISAM COMMENT='Table des objets du système de fichiers';


CREATE TABLE list_comment (
	comment_id				int(4) unsigned NOT NULL auto_increment,
	comment_object			text,

	comment_author			char(255) NOT NULL default '',
	comment_mail			char(255) NOT NULL default '',
	comment_url				char(255) NOT NULL default '',
	comment_date			int(10) unsigned NOT NULL default '0',
	comment_content			text,

	PRIMARY KEY  (comment_id)
) TYPE=MyISAM COMMENT='Table des commentaires des objets';
