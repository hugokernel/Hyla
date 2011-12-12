<?php
/*
	This file is part of Hyla
	Copyright (c) 2004-2006 Charles Rincheval.
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

require 'src/conf.php';

require 'src/db/mysql.class.php';
require 'src/inc/users.class.php';
require 'src/inc/file.class.php';
require 'src/inc/obj.class.php';
require 'src/inc/url.class.php';
require 'src/inc/archive.class.php';
require 'src/lib/template.inc';

$starttime = system::chrono();

$msg_error = null;

$tab_icon = array();
load_icon_info();

/*	Connection à la base
 */
$bdd =& new db();
if (!$bdd->connect(SQL_HOST, SQL_BASE, SQL_USER, SQL_PASS))
	system::end(__('Couldn\'t connect to sql server !'));


/*	Chargement des infos de l'utilisateur courant
 */
session_start();

if (isset($_SESSION['sess_cuser']) && $_SESSION['sess_cuser']) {
	$cuser = unserialize($_SESSION['sess_cuser']);
} else {
	$usr = new users();
	$cuser = $usr->getUser(1);
	unset($usr);
	$_SESSION['sess_cuser'] = serialize($cuser);
}

$obj = new obj(FOLDER_ROOT);

$curl = url::scan();

$cobj = ($curl->aff[0] != 'page') ? $obj->getInfo($curl->obj) : new tFile;

/*	On vérifie que le chemin est bon
 */
if ($curl->aff[0] == 'obj' && $cobj->type == TYPE_UNKNOW) {
	header('HTTP/1.x 404 Not Found');
	redirect(__('Error'), '?', __('Object not found !'));
	system::end();
}


/*	Traitement des actions suivi de l'affichage correspondant
 */
include 'src/act.php';

include 'src/aff.php';


$bdd->close();

system::end();

?>
