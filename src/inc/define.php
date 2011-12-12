<?php
/*
	This file is part of iFile
	Copyright (c) 2004-2006 Charles Rincheval.
	All rights reserved

	iFile is free software; you can redistribute it and/or modify it
	under the terms of the GNU General Public License as published
	by the Free Software Foundation; either version 2 of the License,
	or (at your option) any later version.

	iFile is distributed in the hope that it will be useful, but
	WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with iFile; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */


/*	Répertoires et fichiers de travail...
	/!\ ATTENTION, le répertoire par défaut doit être le premier !
 */
define('FOLDER_TEMPLATE', 		'tpl/'.NAME_TEMPLATE);

define('FOLDER_IMAGES', 		ROOT_URL.'/img/');

define('FOLDER_PLUGINS',		'src/plugin/');
//define('FOLDER_TMP',			'tmp/');

define('FOLDER_CACHE', 			'cache/');


// /!\ Grain de sel pour le cryptage des mot de passe
define('CRYPT_SALT', 123456789);


/*	La taille des images 'vignettes'
	THUMB_SIZE_Y à 0 pour garder la proportionnalité
 */
define('THUMB_SIZE_X', 260);
define('THUMB_SIZE_Y', 0);

//define('THUMB_PREFIX', '.thumb_');


define('TABLE_LIST',	'list_object');
define('TABLE_COMMENT',	'list_comment');

/*	+--------------------------+
 	| Le décalage pour l'heure |
 	+--------------------------+
 */
define('TIME_OFFSET', 0);


/*	Log des messages d'erreurs...
	Oui : Nom du fichier de logs
	Non : null
 */
define('ERROR_FILE_LOG', 'error.log.php');


/*	Le séparateur des champs dans les URL
 */
//define('QUERY_SEP', ',');

/*	+------------------------------------------------------------------+
	| ATTENTION, les valeurs en dessous ne doivent pas être changé !!! |
	| Il s'agit des constantes indispensable pour le moteur            |
	+------------------------------------------------------------------+
 */
define('AFF_INFO',			1);
define('AFF_DOWNLOAD',		2);
define('AFF_EDIT',			4);
define('AFF_MINI',			8);
define('AFF_UPLOAD',		16);
define('AFF_LOGIN',			32);

define('ACT_ADDCOMMENT',	1);

define('TYPE_UNKNOW',		0);		// Inconnu
define('TYPE_DIR',			1);		// Répertoire
define('TYPE_FILE',			2);		// Fichier standard
define('TYPE_ARCHIVE',		3);		// Il s'agit d'un fichier contenu dans une archive

define('SORT_DEFAULT',	 	0);		// Normal
define('SORT_ALPHA',	 	1);		// A -> Z
define('SORT_ALPHA_R',	 	2);		// Z -> A
define('SORT_ALPHA_EXT', 	4);		// xxx.A -> xxx.Z
define('SORT_ALPHA_EXT_R',	8);		// xxx.Z -> xxx.A
define('SORT_FOLDER_FIRST',	16);	// Répertoire en premier

define('ROOT', 'root');

define('ADD_COMMENT', 	1);
define('EDIT_FILE',		2);
define('ADD_FILE',		4);
define('DEL_FILE',		8);
define('GEST_USER',		16);

define('NIV_0',	0);
define('NIV_1',	ADD_COMMENT);
define('NIV_2',	ADD_COMMENT | EDIT_FILE);
define('NIV_3',	ADD_COMMENT | EDIT_FILE | ADD_FILE);
define('NIV_4',	ADD_COMMENT | EDIT_FILE | ADD_FILE | DEL_FILE);
define('NIV_5',	ADD_COMMENT | EDIT_FILE | ADD_FILE | DEL_FILE | GEST_USER);

?>
