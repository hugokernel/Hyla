<?php
/*
	This file is part of Hyla
	Copyright (c) 2004-2007 Charles Rincheval.
	All rights reserved

	Hyla is free software; you can redistribute it and/or modify it
	under the terms of the GNU General Public License as published
	by the Free Software Foundation; either version 2 of the License,
	or (at your option) any later version.

	Hyla is distributed in the hope that it will be useful, but
	WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Hyla; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */


/*	La version
 */
define('HYLA_VERSION',		'0.8.0');


/*	Le décalage pour l'heure
 */
define('TIME_OFFSET', 		0);


/*	Répertoires et fichiers de travail...
 */
define('DIR_PLUGINS_OBJ',	'src/plugin/obj/');
define('DIR_PLUGINS_AUTH',	'src/plugin/auth/');

define('DIR_IMG_PERSO',		'img/perso/');

define('DIR_TPL',			'tpl/');

define('DIR_CACHE', 		'sys/cache/');
define('DIR_ANON', 			'sys/anon');


define('FILE_INI',			DIR_CONF.'hyla.ini');
define('FILE_ICON',			DIR_CONF.'icon.ini');
define('FILE_PLUGINS',		DIR_CONF.'plugins.ini');


/*	La taille des images 'vignettes'
	THUMB_SIZE_Y à 0 pour garder la proportionnalité
 */
define('THUMB_SIZE_X', 		160);
define('THUMB_SIZE_Y', 		0);

define('DEFAULT_LNG', 'fr-FR');

define('PREFIX_TABLE',		'hyla_');

define('TABLE_OBJECT',		PREFIX_TABLE.'object');
define('TABLE_ACONTROL',	PREFIX_TABLE.'acontrol');
define('TABLE_COMMENT',		PREFIX_TABLE.'comment');
define('TABLE_USERS',		PREFIX_TABLE.'users');
define('TABLE_GRP',			PREFIX_TABLE.'group');
define('TABLE_GRP_USR',		PREFIX_TABLE.'group_user');


/*	/!\ Grain de sel pour le cryptage des mot de passe
	Attention, si vous changez cette valeur après l'installation, il vous faudra changez tous les mots de passe
 */
define('CRYPT_SALT',		123456789);

/*	+-------------------------------------------------------------------+
	| ATTENTION, les valeurs en dessous ne doivent pas être changé !!!  |
	| Il s'agit des constantes indispensable pour le bon fonctionnement |
	| du moteur...                                                      |
	+-------------------------------------------------------------------+
 */

define('MIN_PASSWORD_SIZE',		4);

define('UNAUTHORIZED_CHAR', 	'\/:*?"<>|!');

define('URL_HYLA_SITE',			'http://www.hyla-project.org/');
define('URL_TEST_VERSION',		'http://www.hyla-project.org/last_version.php');


define('DIR_ICON',				'icon.png');


define('TYPE_UNKNOW',			0);		// Inconnu
define('TYPE_DIR',				1);		// Répertoire
define('TYPE_FILE',				2);		// Fichier standard
define('TYPE_ARCHIVED',			3);		// Il s'agit d'un fichier contenu dans une archive


/*	Les tri
 */
define('SORT_DEFAULT',	 		0);		// Normal
define('SORT_NAME_ALPHA',		1);		// A -> Z
define('SORT_NAME_ALPHA_R',		2);		// Z -> A
define('SORT_EXT_ALPHA', 		4);		// xxx.A -> xxx.Z
define('SORT_EXT_ALPHA_R',		8);		// xxx.Z -> xxx.A
define('SORT_CAT_ALPHA',		16);	// Doc, Image
define('SORT_CAT_ALPHA_R',		32);	// Image, Doc
define('SORT_SIZE',				64);	// Taille - / +
define('SORT_SIZE_R',			128);	// Taille + / -
define('SORT_DATE',				256);	// Date - +
define('SORT_DATE_R',			512);	// Date + -

define('SORT_FOLDER_FIRST',		256);	// Répertoire en premier


/*	Les Id réservé
	L'id 0 est interdit, il est utilisé en interne
 */
define('ANY_ID',				1);
define('AUTHENTICATED_ID',		2);
define('ANONYMOUS_ID',			3);

/*	Les types d'utilisateurs
 */
define('USR_TYPE_SPECIAL',		0);	// All, Anonymous, Authenticated
define('USR_TYPE_GRP',			1);	// [xXxxX]
define('USR_TYPE_USER',			2);
define('USR_TYPE_SUPERVISOR',	3);
define('USR_TYPE_ADMIN',		4);



/*	Les flags
 */
define('FLAG_NONE',				0);		// Fichier normal
// 2, 4, 8 : reserved
define('FLAG_ANON',				16);	// Le fichier est anonyme
// 32 : reserved
define('FLAG_TRASH',			64);	// Le fichier est à la corbeille


/*	Access Control
 */
define('AC_NONE',				0);		// Aucun
define('AC_VIEW',				1);		// Visualisation de l'objet

// 2, 4, 8 : reserved

define('AC_ADD_COMMENT',		16);	// Ajout de commentaire

define('AC_EDIT_DESCRIPTION',	32);	// Edition de description
define('AC_EDIT_PLUGIN',		64);	// Edition des plugins
define('AC_EDIT_ICON',			128);	// Edition des icones

define('AC_ADD_FILE',			256);	// Ajout de fichier
define('AC_CREATE_DIR',			512);	// Création de dossier

define('AC_COPY',				1024);	// Copie
define('AC_MOVE',				2048);	// Déplacement !
define('AC_RENAME',				4096);	// Renommage !

define('AC_DEL_FILE',			8192);	// Suppression dossier
define('AC_DEL_DIR',			16384);	// Suppression fichier


define('ADMINISTRATOR_ONLY',	65536);

/*	Meta Access Control
 */
/*
define('AC_EDIT',	AC_EDIT_DESCRIPTION | AC_EDIT_PLUGIN | AC_EDIT_ICON);	// Édition
define('AC_ADD',	AC_ADD_FILE | AC_CREATE_DIR);							// Création
define('AC_DEL',	AC_DEL_FILE | AC_DEL_DIR);								// Suppression
define('AC_CMR',	AC_COPY | AC_MOVE | AC_RENAME);
*/

?>
