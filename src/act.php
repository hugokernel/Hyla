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

if (!defined('PAGE_HOME'))
	header('location: ../index.php');

require 'src/inc/string.class.php';
require 'src/inc/plugin.class.php';
require 'src/lib/XPath.class.php';
require 'src/inc/plugins.class.php';

//$act = @$_REQUEST['act'];
//echo '-'.$act;

function test_right($niv) {
	global $cobj;
	if (!@$_SESSION['sess_niv']) {
		$_SESSION['sess_page'] = $_SERVER['REQUEST_URI'];
		// Merci de procédé à une authentification
		redirect(traduct('error'), obj::getCurrentUrl(AFF_LOGIN), traduct('thankauth'));
		exit;
	} else if ($_SESSION['sess_niv'] < $niv) {
		// Interdit !
		redirect(traduct('error'), obj::getCurrentUrl(), traduct('funcforbidden'));
		exit;
	}	
}

switch ($act) {

	#	On se déloggue !
	case 'logout':
		if (@$_SESSION['sess_niv'] == NIV_5) {
			$_SESSION['sess_name'] = null;
			$_SESSION['sess_niv'] = 0;
			$_SESSION['sess_page'] = null;
			redirect('', obj::getCurrentUrl(), traduct('logout'));
			exit;
		}
		break;

	#	Un commentaire à été envoyé
	case 'addcomment':
		$val = verif_value(array(
				$_POST['cm_author']		=>	traduct('emptyauthor'),
				$_POST['cm_content']	=>	traduct('emptycontent')), $msg_error);

		if ($val) {
			if ($_POST['cm_site'] == 'http://')
				$_POST['cm_site'] = null;
			$obj->addComment($cobj->file, string::format($_POST['cm_author'], false), string::format($_POST['cm_mail'], false), string::format($_POST['cm_site'], false), string::format($_POST['cm_content']));
			$_POST['cm_content'] = null;
		}

		break;

	#	Modification description
	case 'setdescription':
		test_right(EDIT_FILE);
		$obj->setDescription($cobj->file, string::format($_POST['description']));
		$cobj->info->description = stripslashes(string::format($_POST['description']));
		break;

	#	Modification plugin courant
	case 'setplugin':
		test_right(EDIT_FILE);
		if ($cobj->type = TYPE_DIR) {
			$plugin_name = $_POST['pg_name'];
			if ($plugin_name == 'default')
				$obj->setPlugin($cobj->file, null);
			else {
				// On vérifie que le plugin existe bien
				$tab = plugins::getDirPlugins();
				foreach ($tab as $occ) {
					if (strtolower($occ['name']) == $plugin_name) {
						$obj->setPlugin($cobj->file, $plugin_name);
						break;
					}
				}
			}
		}

		break;

	#	Suppression d'un objet
	case 'del':
		test_right(DEL_FILE);

		// On ne peut pas supprimer la racine
		if ($cobj->file == '/') {
			redirect(traduct('error'), obj::getCurrentUrl(), traduct('delroot'));
			exit;
		}

		// On ne supprime pas un fichier dans une archive
		if ($cobj->type == TYPE_ARCHIVE || $cobj->type == TYPE_DIR) {
			redirect($cobj->file, obj::getUrl($cobj->file, AFF_INFO), traduct('notimplemented'));
			exit;
		}

		if ($cobj->type == TYPE_FILE) {
			$msg = unlink($cobj->realpath) ? view_status(traduct('delok')) : view_error(traduct('delerror'));
			$obj->delete();
			redirect($cobj->file, obj::getUrl($cobj->path, AFF_INFO), $msg);
			exit;
		}

		break;
}


?>
