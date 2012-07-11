<?php
/*
	This file is part of Hyla
	Copyright (c) 2004-2012 Charles Rincheval.
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


/*	+----------------------------------------------------+
 	| Répertoire contenant vos fichiers à lister         |
 	| /!\ ATTENTION, pas de slash ou anti slash de fin ! |
 	+----------------------------------------------------+
 */
define('FOLDER_ROOT', '/var/www');


/*	L'emplacement de Hyla après le nom de domaine (sans slash de fin !)
	Ex: http://ifile.free.fr/				-> mettez ''
	Ex: http://ifile.free.fr/hyla/			-> mettez '/hyla'
	Ex: http://ifile.free.fr/data/hyla		-> mettez '/data/hyla'

	Si ce champs est vide, la valeur de $_SERVER['PHP_SELF'] sera utilisée
    Laissez tel quel par défaut
 */
//define('ROOT_URL', '');



/*	+---------------------------------+
	| Connexion à la base de données |
	+---------------------------------+
	Dans SQL_HOST, il est possible de spécifier un port différent
	de la manière suivante : 'server:3300'
 */
define('SQL_HOST',	'');
define('SQL_BASE',	'');
define('SQL_USER',	'');
define('SQL_PASS',	'');

/*	+--------------------------------------------------------------+
	| Chemin d'accès aux dossiers de cache et de fichiers anonymes |
	+--------------------------------------------------------------+
    Absolu ou relatif
    Laissez commenté pour utiliser les valeurs par défaut
 */
//define('DIR_CACHE',         'sys/cache/');
//define('DIR_ANON',          'sys/anon/');

?>
