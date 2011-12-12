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

require 'src/conf.php';

require 'lng/'.LNG.'/main.php';

require 'src/inc/function.inc.php';
require 'src/inc/system.class.php';

require 'src/lib/template.class.php';

require 'src/inc/file.class.php';

require 'src/db/mysql.class.php';
require 'src/inc/obj.class.php';

require 'src/inc/archive.class.php';

define('PAGE_HOME', true);

$starttime = system::chrono();

// Template...
$tpl = new Template(FOLDER_TEMPLATE);
$tpl->set_file(array(
		'index'	 		=>	'index.tpl',
		'edit'			=>	'edit.tpl',
		'misc'			=>	'misc.tpl',
		'comment'		=>	'comment.tpl',
		'obj'			=>	'obj.tpl'
		));

$tpl->set_block('index', array(
		'action_rename'		=>	'Hdlaction_rename',
		'action_mkdir'		=>	'Hdlaction_mkdir',
		'action_del'		=>	'Hdlaction_del',
		'action_addfile'	=>	'Hdlaction_addfile',
		'action_edit'		=>	'Hdlaction_edit',
		'action_comment'	=>	'Hdlaction_comment',
		'aff_login'	 		=>	'Hdlaff_login',
		'aff_logout'		=>	'Hdlaff_logout',
		'aff_admin'			=>	'Hdlaff_admin'));

$tpl->set_block('edit', array(
		'plugin'			=>	'Hdlplugin',
		'edit_plugins'		=>	'Hdledit_plugins',
		'edit_description'	=>	'Hdledit_description'
		));

$tpl->set_block('misc', array(
		'mkdir'			=>	'Hdlmkdir',
		'rename'		=>	'Hdlrename',
		'error'			=>	'Hdlerror',
		'status'		=>	'Hdlstatus'
		));

$tpl->set_block('comment', array(
		'comment_line'		=>	'Hdlcomment_line',
		'current_comment'	=>	'Hdlcurrent_comment',
		'last_comment_line'	=>	'Hdllast_comment_line',
		'last_comment'		=>	'Hdllast_comment'
		));

$tpl->set_block('obj', array(
		'previous_page'	=>	'Hdlprevious_page',
		'next_page'		=>	'Hdlnext_page',
		'pagination'	=>	'Hdlpagination'));

$msg_error = null;

// Connection à la base
$bdd =& new db();
$id_bdd = $bdd->connect(SQL_HOST, SQL_BASE, SQL_USER, SQL_PASS);

session_start();

$aff = null;	// L'affichage
$param = null;	// Paramètre d'affichage
$act = null;	// L'action
$pact = null;	// L'affichage pour le plugin
$paff = null;	// L'affichage pour le plugin
$obj = new obj(FOLDER_ROOT);

$cobj = $obj->scanUrl();


/*	On vérifie que le chemin est bon
 */
if ($cobj->type == TYPE_UNKNOW) {
	redirect(traduct('error'), '?', traduct('errornotfound'));
	exit();
}

$var_tpl = null;

/*	Traitement des actions suivi de l'affichage correspondant
 */
include 'src/act.php';

include 'src/aff.php';

$endtime = system::chrono();
$totaltime = ($endtime - $starttime);


if (@$_SESSION['sess_niv'] == NIV_5) {
	$tpl->parse('Hdlaction_addfile', 'action_addfile', true);
	$tpl->parse('Hdlaction_edit', 'action_edit', true);
	$tpl->parse('Hdlaff_logout', 'aff_logout', true);
} else
	$tpl->parse('Hdlaff_login', 'aff_login', true);

$tpl->set_var(array(
		'FOLDER_IMAGES'		=>	FOLDER_IMAGES,
		'OBJECT'			=>	$cobj->file,
		'OBJ'				=>	$var_tpl,
		'NAVIG_TITLE'		=>	NAVIG_TITLE,
		'IFILE_VERSION'		=>	IFILE_VERSION,
		'DEBUG'				=>	$bdd->getNbrQuery().' requête(s), exécutée(s) en '.round($totaltime, 4).' seconde(s)'));

$tpl->pparse('OutPut', 'index');

$bdd->close();

?>
