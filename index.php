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

/*  Here, you can modify site id
 */
if (!defined(HYLA_SITE_ID)) {
    define('HYLA_SITE_ID', 1);
}

/*  /!\ DO NOT EDIT NEXT LINE /!\
 */
define('HYLA_HOME', true);
define('HYLA_ROOT_PATH', dirname(__FILE__).'/');
define('HYLA_RUN_PATH',  dirname($_SERVER['SCRIPT_FILENAME']).'/');

require HYLA_ROOT_PATH.'src/init.php';

// Create obj
$obj = obj::getInstance(HYLA_SITE_ID);
$site_info = $obj->load();

if ($site_info['url']) {
    $dir = $site_info['url'];
} else {
    $dir = file::dirName($_SERVER['SCRIPT_NAME']);
    $dir .= ($dir != '/') ? '/' : null;
}

define('HYLA_ROOT_URL', $dir);

run_tpl();

/*  Loading current user information
 */
$auth = plugins::get(PLUGIN_TYPE_AUTH);
$auth->load();
$cuser = $auth->getUser();


/*
dlog('Root path : '.HYLA_ROOT_PATH);
dlog('Root url : '.HYLA_ROOT_URL);
dlog('Run path : '.HYLA_RUN_PATH);
//dbug($conf);
*/


/*    Loading shared dir and acl
 */
$obj = obj::getInstance();
$id = $obj->datasource->register('FOLDER_ROOT', array(&$obj, 'wrapper'), $site_info['shared_dir']);
$obj->loadRights();


/*
//define('LOG_TYPE', LOG_TYPE_FIREBUG | LOG_TYPE_SYSTEM); // | LOG_TYPE_OUT);  // | LOG_TYPE_BDD);
//define('LOG_VIEWER_L_DEBUG', LOG_TYPE_FIREBUG);
dbug(array('toto', 'tat'));
dlog('toto');
dlog('information', L_INFO);
dlog('warning',     L_WARNING);
dlog('error',       L_ERROR);
*/



/*  Web service or not ?
 */
if (isset($_REQUEST['method'])) {

//    error_reporting(E_ERROR);

    /*  Run action
     */
    require HYLA_ROOT_PATH.'src/inc/ws.class.php';

    $format = (isset($_REQUEST['format'])) ? $_REQUEST['format'] : null;
    switch ($format) {
        case 'raw':
            $context = null;
            break;
        case 'json':
        default:
            $context = 'json';
            break;
    }

    $ret = ws::run($_GET['method'], $_REQUEST);
    if (system::isError($ret)) {
        out($_GET['method'].' : '.$ret->msg, -1);
        system::end();
    }

    out($ret);

    /*
    echo '/*';
    dlog(__('Executed in %s seconds with %s sql query', round((system::chrono() - START_TIME), 4), $bdd->getQueryCount()));
    echo '*-/';
    */

    system::end();
}

/*  Url plugin loader
 */
$url = plugins::get(PLUGIN_TYPE_URL);
$url->setRootUrl(HYLA_ROOT_URL);
$url->load();
if (!$url->getParam('obj') && $url->getParam('aff') != 'page') {
    $url->setParamObj('/');
    $url->setParam('aff', 0, 'obj');
}

$cobj = $obj->setCurrentObj($url->getParam('obj'));

/*  Not read config file !
 */
if (file::isInPath($cobj->realpath, HYLA_ROOT_PATH.DIR_CONF)) {
    system::end(__('You do not have the rights sufficient to reach the resource !'));
}


$basket = basket::getInstance();
$basket->restore();

include(HYLA_ROOT_PATH.'src/aff.php');

$bdd->close();

system::end();

?>
