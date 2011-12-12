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

define('ERROR_REPORT', 2);

error_reporting(E_ERROR);

set_magic_quotes_runtime(0);

if (file_exists('install.php')) {
	exit('Vous devez supprimer le fichier install.php !');
}

if (!file_exists('conf/config.inc.php')) {
	exit('Le fichier de configuration est introuvable !');
} else
	require 'conf/config.inc.php';

require 'src/lib/class.ini.file.php';
if (function_exists('parse_ini_file'))
	$tab = parse_ini_file('conf/ifile.ini');
else
	$tab = iniFile::read('conf/ifile.ini', true);

// Chargement de la configuration
define('MAIL_WEBMASTER',		$tab['webmaster_mail']);
define('DIR_DEFAULT_PLUGIN',	$tab['default_plugin']);
define('NAME_TEMPLATE',			$tab['template']);
define('VIEW_HIDDEN_FILE',		$tab['view_hidden_file']);
define('FILE_CHMOD',			octdec($tab['file_chmod']));
define('FOLDER_CHMOD',			octdec($tab['dir_chmod']));
define('LNG',					$tab['lng']);
define('NAVIG_TITLE',			$tab['title']);
define('CREATE_THUMB',			$tab['create_thumb']);
define('URL_SCAN',				$tab['url_scan']);
define('ENABLED_CMPT',			$tab['download_counter']);
define('GROUP_BY_SORT',			$tab['group_by_sort']);

require 'src/inc/define.php';
$sort_tab = array(0	=> SORT_DEFAULT, 1 => SORT_ALPHA, 2 => SORT_ALPHA_R, 3 => SORT_ALPHA_EXT, 4 => SORT_ALPHA_EXT_R);
$sort_value = $sort_tab[$tab['sort']];
if ($tab['folder_first'])
	$sort_value |= SORT_FOLDER_FIRST;
define('SORT_CONFIG', $sort_value);

unset($tab, $sort_tab);

define('IFILE_VERSION', '0.4.1');

?>
