<?php
/*
	This file is part of Hyla
	Copyright (c) 2004-2006 Charles Rincheval.
	All rights reservednstall.php?etape=5

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
define('FOLDER_ROOT', '');


/*	L'emplacement de Hyla après le nom de domaine (sans slash de fin !)
	Ex: http://ifile.free.fr/				-> mettez ''
	Ex: http://ifile.free.fr/ifile/			-> mettez '/ifile'
	Ex: http://ifile.free.fr/data/ifile		-> mettez '/data/ifile'
 */
define('ROOT_URL', '');


/*	+---------------------------------+
	| Connection à la base de données |
	+---------------------------------+
 */
define('SQL_HOST',	'');
define('SQL_BASE',	'');
define('SQL_USER',	'');
define('SQL_PASS',	'');

?>
