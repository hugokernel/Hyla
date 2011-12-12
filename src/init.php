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

if (!defined('PAGE_HOME'))
	header('location: ../index.php');

require 'src/inc/l10n.class.php';
require 'src/db/mysql.class.php';
require 'src/inc/users.class.php';
require 'src/inc/file.class.php';
require 'src/inc/obj.class.php';
require 'src/inc/url.class.php';
require 'src/lib/template.class.php';
require 'src/inc/system.class.php';
require 'src/inc/function.inc.php';

$starttime = system::chrono();

$msg_error = null;

define('DIR_CONF',		'conf/');
define('CONFIG_FILE',	DIR_CONF.'config.inc.php');

/*	Modifiez ici l'affichage des erreurs
 */
error_reporting(E_ERROR);		// E_ERROR | E_USER_ERROR


/*	Atrapper les erreurs
 */
//register_shutdown_function(array('system', 'timeOut'));

ini_set('magic_quotes_runtime', 0);
ini_set('magic_quotes_sybase', 0);
set_magic_quotes_runtime(0);


// En attendant de trouver mieux...
if (!get_magic_quotes_gpc()) {
	array_walk_recursive($_COOKIE, 'addslashes');
	array_walk_recursive($_POST, 'addslashes');
	array_walk_recursive($_GET, 'addslashes');
}


/*	Vérification de sécurité
 */
if (basename($_SERVER['PHP_SELF']) != 'install.php') {
	if (!file_exists('conf/lock')) {
		if (file_exists('install.php'))
			header('location: install.php');
		else
			system::end('Install Hyla before : <a href="install.php">install.php</a>');
	}

	if (!file_exists(CONFIG_FILE)) {
		system::end('The configuration file doesn\'t exist !');
	} else
		require CONFIG_FILE;
}

define('DIR_ROOT', file::dirName($_SERVER['SCRIPT_FILENAME']).'/');

require 'src/inc/define.php';
require 'src/lib/class.ini.file.php';
require 'src/lib/XPath.class.php';

$conf = array();
load_config();


/*	Rss ou non ?
 */
if (basename($_SERVER['PHP_SELF']) == 'rss.php') {
	$current_tpl = 'rss';
} else {
	$current_tpl = (file_exists(DIR_TPL.$conf['name_template']) ? $conf['name_template'] : 'default');
}
define('DIR_TEMPLATE', 	DIR_TPL.$current_tpl);


/*	ROOT_URL est spécifié dans config.inc.php ?
 */
if (!defined('ROOT_URL') || ROOT_URL == null) {
	$dirname = file::dirName($_SERVER['PHP_SELF']);
	$dirname = ($dirname == '/') ? null : $dirname;
	define('REAL_ROOT_URL', $dirname);
	unset($dirname);
}

$styles = array();

// Entête envoyé en premier
$xfile = DIR_TEMPLATE.'/info.xml';
if (file_exists($xfile)) {
	$xml =& new XPath($xfile);
	$res = $xml->match('/template');
	if ($res) {
		header($xml->getData('/template/header'));

		// Récupération du dossier contenant les images du modèle
		$dir_img = $xml->getData('/template/img-src');
		$dir_img = ($dir_img) ? str_replace('%s', $current_tpl, $dir_img) : './tpl/default/img';
		define('DIR_IMAGE', $dir_img);

		// Inclusion du fichier de fonctions du modèle
		$file_func = $xml->getData('/template/php-function');
		$file_func = ($file_func) ? str_replace('%s', $current_tpl, $file_func) : './tpl/default/function.php';
		require $file_func;

		// Récupération des différents style du modèle
		$res = $xml->match('/template/stylesheets/stylesheet');
		if ($res) {
			foreach ($res as $occ) {
				add_stylesheet(DIR_TEMPLATE.'/'.$xml->getData($occ.'/href'), $xml->getData($occ.'/title'), $xml->getData($occ.'/type'), $xml->getData($occ.'/media'));
			}
		}
	}
	unset($xml, $res, $dir_img);
}


/*	Fichiers de langue
 */
$l10n = new l10n($conf['lng']);
$l10n->sendHeader();
$l10n->setFile('general.php');
$l10n->setFile('messages.php');
$l10n->setFile('icon.php');


$tab_icon = array();
load_icon_info();

?>
