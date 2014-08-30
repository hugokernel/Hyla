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

define('PAGE_HOME', true);

require 'src/init.php';

/*  Connection à la base
 */
$bdd = new db();
if (!$bdd->connect(SQL_HOST, SQL_USER, SQL_PASS, SQL_BASE)) {
    system::end(__('Couldn\'t connect to sql server !'));
}

/*  Chargement des infos de l'utilisateur courant
 */
$auth = plugins::get(PLUGIN_TYPE_AUTH, $conf['plugin_default_auth']);
$auth->load();
$cuser = $auth->getUser();


/*  Chargement du dossier de partage et des droits
 */
$obj = new obj(FOLDER_ROOT);
$obj->loadRights();


/*  Url plugin loader
 */
$url = plugins::get(PLUGIN_TYPE_URL, $conf['plugin_default_url']);
$url->setRootUrl(REAL_ROOT_URL);
$url->load();
if (!$url->getParam('obj') && $url->getParam('aff') != 'page') {
    $url->setParamObj('/');
    $url->setParam('aff', 0, 'obj');
}


/*  On vérifie que le chemin est bon
 */
$cobj = ($url->getParam('aff', 0) != 'page') ? $obj->getInfo($url->getParam('obj'), true, true) : new tFile;
if (!$cobj && $url->getParam('aff', 0) == 'obj' && ($cobj->type == TYPE_UNKNOW || !($obj->getCUserRights4Path($cobj->path) & AC_VIEW))) {
    if (!$cobj || $cobj->file == '/') {
        if ($cuser->id != ANONYMOUS_ID && ($obj->getCUserRights4Path('/') & AC_VIEW)) {
            header('HTTP/1.x 404 Not Found');

            // Si l'utilisateur est loggué, on redirige vers l'admin
            if (!is_readable(FOLDER_ROOT)) {
                redirect(__('Error'), $url->linkToPage('admin').'#configuration', __('Object not found !'));
            } else {
                redirect(__('Error'), $url->linkToObj('/'), __('Object not found !'));
            }
        } else {
            header('HTTP/1.x 401 Authorization Required');
            $_SESSION['sess_url'] = $_SERVER['REQUEST_URI'];
            redirect(__('Error'), $url->linkToPage('login'), __('You do not have the rights sufficient to reach the resource !'));
        }
    } else {
        header('HTTP/1.x 404 Not Found');
        redirect(__('Error'), $url->linkToObj('/'), __('Object not found !'));
    }
    system::end();
}

/*  Interdit de lire la config
 */
if (file::isInPath($cobj->realpath, file::dirName(__FILE__).'/'.DIR_CONF)) {
    system::end(__('You do not have the rights sufficient to reach the resource !'));
}

/*  Traitement des actions suivi de l'affichage correspondant
 */
include 'src/act.php';

include 'src/aff.php';


$bdd->close();

system::end();

?>
