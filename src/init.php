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

if (!defined('HYLA_HOME'))
    exit('Fatal error !');

/*  Modifiez ici l'affichage des erreurs
 */
error_reporting(E_ALL);     // E_ALL

require HYLA_ROOT_PATH.'src/inc/error.class.php';
require HYLA_ROOT_PATH.'src/inc/l10n.class.php';
require HYLA_ROOT_PATH.'src/inc/file.class.php';
require HYLA_ROOT_PATH.'src/inc/function.inc.php';
require HYLA_ROOT_PATH.'src/core/system.class.php';
require HYLA_ROOT_PATH.'src/core/trigger.class.php';

define('START_TIME',    system::chrono());

define('DIR_CONF',      HYLA_ROOT_PATH.'conf/');
define('CONFIG_FILE',   DIR_CONF.'config.inc.php');


// Magic quote is uggly !
ini_set('magic_quotes_runtime', 0);
ini_set('magic_quotes_sybase', 0);
set_magic_quotes_runtime(0);
if (!get_magic_quotes_gpc()) {
    _array_walk_recursive($_COOKIE, 'addslashes');
    _array_walk_recursive($_POST,   'addslashes');
    _array_walk_recursive($_GET,    'addslashes');
}

//echo '<h2>'.HYLA_RUN_PATH.' - '.HYLA_ROOT_PATH.'</h2>';
require HYLA_ROOT_PATH.'src/core/define.php';

/*
if (!file_exists(CONFIG_FILE)) {
    system::end('Conf file not found !');
}
*/

// Find for sys dir
if (!file_exists(HYLA_RUN_PATH.DIR_CACHE) || !file_exists(HYLA_RUN_PATH.DIR_ANON)) {
    system::end('Sys dir or anonymous dir not found !');
}

if (!file_exists(HYLA_RUN_PATH.DIR_TPL)) {
    system::end('Tpl dir not found !');
}

require CONFIG_FILE;

require HYLA_ROOT_PATH.'src/core/users.class.php';
require HYLA_ROOT_PATH.'src/core/obj.class.php';
require HYLA_ROOT_PATH.'src/inc/string.class.php';
require HYLA_ROOT_PATH.'src/core/plugin.class.php';
require HYLA_ROOT_PATH.'src/inc/dcache.class.php';
//require HYLA_ROOT_PATH.'src/lib/class.ini.file.php';
require HYLA_ROOT_PATH.'src/core/plugins.class.php';
require HYLA_ROOT_PATH.'src/core/conf.class.php';

/*  Connecting to backend
 */
$bdd = plugins::get(PLUGIN_TYPE_DB);
$bdd->load();

require HYLA_ROOT_PATH.'src/core/log.class.php';
$log = log::getInstance();


/* Load configuration
 */
$conf = conf::getInstance();
$conf->load();

$dcache = new dcache();

/*  Fichiers de langue
 */
$l10n = new l10n($conf->get('lng'));
$l10n->sendHeader();
$l10n->setFile('general.php');
$l10n->setFile('messages.php');
$l10n->setFile('icon.php');


$tab_icon = array();
load_icon_info();

// à mettre ou ?
require HYLA_ROOT_PATH.'src/inc/basket.class.php';


?>
