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

/*  Modifiez ici l'affichage des erreurs
 */
error_reporting(E_ERROR);     // E_ALL

require 'src/inc/l10n.class.php';
require 'src/db/mysql.class.php';
require 'src/inc/users.class.php';
require 'src/inc/file.class.php';
require 'src/inc/plugin.class.php';
require 'src/inc/obj.class.php';
require 'src/lib/template.class.php';
require 'src/inc/system.class.php';
require 'src/inc/function.inc.php';
require 'src/inc/dcache.class.php';

$starttime = system::chrono();

$msg_error = null;

define('DIR_CONF',      'conf/');
define('CONFIG_FILE',   DIR_CONF.'config.inc.php');


/*  Atrapper les erreurs
 */
//register_shutdown_function(array('system', 'timeOut'));

ini_set('magic_quotes_runtime', 0);
ini_set('magic_quotes_sybase', 0);
set_magic_quotes_runtime(0);


// En attendant de trouver mieux...
if (!get_magic_quotes_gpc()) {
    _array_walk_recursive($_COOKIE, 'addslashes');
    _array_walk_recursive($_POST, 'addslashes');
    _array_walk_recursive($_GET, 'addslashes');
}


/*  Vérification de sécurité
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

$dcache = new dcache();

/*  Rss ou non ?
 */
if (basename($_SERVER['PHP_SELF']) == 'rss.php') {
    $current_tpl = 'rss';
} else {
    $current_tpl = (file_exists(DIR_TPL.$conf['name_template']) ? $conf['name_template'] : 'default');
}
define('DIR_TEMPLATE',  DIR_TPL.$current_tpl);


/*  ROOT_URL est spécifié dans config.inc.php ?
 */
if (!defined('ROOT_URL') || ROOT_URL == null) {
    $dir = file::dirName($_SERVER['SCRIPT_NAME']);
    if ($dir != '/') {
        $dir .= '/';
    }
//echo '<h2>'.$dir.'</h2>';
    define('REAL_ROOT_URL', $dir);
}

$page_headers = array();
$page_styles = array();

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

        $dir = REAL_ROOT_URL;
        if ($dir{strlen($dir) - 1} == '/' && substr($dir_img, 0, 1) == '/') {
            define('DIR_IMAGE', substr($dir, 0, strlen($dir) - 1).$dir_img);
        } else {
            define('DIR_IMAGE', $dir.$dir_img);
        }

        // Inclusion du fichier de fonctions du modèle
        $file_func = $xml->getData('/template/php-function');
        $file_func = ($file_func) ? str_replace('%s', $current_tpl, $file_func) : './tpl/default/function.php';
        require $file_func;

        // Récupération des différents style du modèle
        $res = $xml->match('/template/stylesheets/stylesheet');
        if ($res) {
            foreach ($res as $occ) {
                $css_href = $xml->getData($occ.'/href');

                // If stylesheet is in another dir, no include root dir
                if ($css_href{0} != '/' && substr($css_href, 0, 7) != 'http://') {
                    $css_href = REAL_ROOT_URL.DIR_TEMPLATE.'/'.$css_href;
                }

                add_stylesheet($css_href, $xml->getData($occ.'/title'), $xml->getData($occ.'/type'), $xml->getData($occ.'/media'));
            }
        }
    } else {
        system::end(__('Unable to load xml file "%s"', $xfile));
    }
    unset($xml, $res, $dir_img);
}


/*  Fichiers de langue
 */
$l10n = new l10n($conf['lng']);
$l10n->sendHeader();
$l10n->setFile('general.php');
$l10n->setFile('messages.php');
$l10n->setFile('icon.php');


$tab_icon = array();
load_icon_info();

require 'src/inc/plugins.class.php';
require 'src/inc/plugin_auth.class.php';
require 'src/inc/plugin_url.class.php';

?>
