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

if (!defined('PAGE_HOME'))
	header('location: ../index.php');

require 'src/inc/string.class.php';
require 'src/inc/plugin.class.php';
require 'src/lib/XPath.class.php';
require 'src/inc/plugins.class.php';
require 'src/inc/cache.class.php';


function test_perm($niv) {
	global $cobj, $cuser;
	if (($cuser->id == ANONYMOUS_ID) && !($cuser->perm & $niv)) {
		$_SESSION['sess_url'] = $_SERVER['REQUEST_URI'];
		// Merci de procédé à une authentification
		redirect(__('Error'), url::getCurrentObj('login'), __('Thank you for authenticate you'));
		system::end();
	} else if (!($cuser->perm & $niv)) {
		// Interdit !
		redirect(__('Error'), url::getCurrentObj(), __('You cannot use this functionality !'));
		system::end();
	}
}


switch ($curl->act[0]) {

	#	On se déloggue !
	case 'logout':
		if ($cuser->id != ANONYMOUS_ID) {
			session_destroy();
			redirect('', url::getCurrentObj(), __('You are now disconnected'));
			system::end();
		}
		break;

	#	Un commentaire à été envoyé
	case 'addcomment':

		test_perm(ADD_COMMENT);

		$_POST['cm_author'] = strip_tags($_POST['cm_author']);
		$_POST['cm_author'] = ($cuser->id != ANONYMOUS_ID) ? $cuser->name : $_POST['cm_author'];

		$val = null;

		$usr = new users();
		$ret = $usr->testLogin($_POST['cm_author']);

		if ($ret == -1) {
			$msg_error = __('The name is invalid !');
		} else if (!$ret && $cuser->id == ANONYMOUS_ID) {
			$msg_error = __('User already exists !');
		} else {
			$val = verif_value(array(
					$_POST['cm_author']	=>	__('The author field is required'),
					$_POST['cm_content']	=>	__('The message field is required')), $msg_error);
		}

		unset($usr);

		// Les champs indispensables sont remplis ?
		if ($val) {
			if ($_POST['cm_site'] == 'http://')
				$_POST['cm_site'] = null;

			$_POST['cm_mail'] = string::format($_POST['cm_mail'], false);
			$_POST['cm_site'] = string::format($_POST['cm_site'], false);
			$_POST['cm_content'] = string::format($_POST['cm_content']);

			$obj->addComment($cobj, $_POST['cm_author'], $_POST['cm_mail'], $_POST['cm_site'], $_POST['cm_content']);

			// On reste en phase avec l'objet courant !
			$csize = sizeof($cobj->info->comment);
			$cobj->info->nbr_comment++;
			$cobj->info->comment[$csize] = new tComment;
			$cobj->info->comment[$csize]->author = $_POST['cm_author'];
			$cobj->info->comment[$csize]->mail = $_POST['cm_mail'];
			$cobj->info->comment[$csize]->url = $_POST['cm_site'];
			$cobj->info->comment[$csize]->content = $_POST['cm_content'];
			$cobj->info->comment[$csize]->date = system::time();

			$_POST['cm_content'] = null;
		}

		break;

	#	Modification description
	case 'setdescription':
		test_perm(EDIT_FILE);
		$obj->setDescription($cobj->file, string::format($_POST['description']));
		$cobj->info->description = stripslashes(string::format($_POST['description']));
		break;

	#	Modification plugin courant
	case 'setplugin':
		test_perm(EDIT_FILE);
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

	#	Renommage
	case 'rename':
		test_perm(($cobj->type == TYPE_DIR ? (CREATE_DIR | DEL_DIR) : (ADD_FILE | DEL_FILE)));

		// Le Renommage d'une archive n'est pas possible
		if ($cobj->type == TYPE_ARCHIVE) {
			redirect($cobj->file, url::getCurrentObj(), __('Not implemented !'));
			system::end();
		}

		// On ne peut pas renommer la racine
		if ($cobj->file == '/') {
			redirect(__('Error'), url::getCurrentObj(), __('Impossible to rename the root'));
			system::end();
		}

		// Il ne doit pas y avoir de caractère interdit !
		if (string::test($_POST['rn_newname'], UNAUTHORIZED_CHAR)) {
			$msg_error = __('There are an invalid char in the file name, unauthorized char are : %s', UNAUTHORIZED_CHAR);
			$curl->aff[1] = 'rename';
			break;
		}

		// On vérifie tout d'abord si l'objet de destination existe déjà
		$newname = ($cobj->type == TYPE_FILE) ? $cobj->path.$_POST['rn_newname'] : file::downPath($cobj->path).$_POST['rn_newname'];
		if (file_exists(FOLDER_ROOT.$newname)) {
			$msg_error = is_dir(FOLDER_ROOT.$_POST['rn_newname']) ? __('The dir already exists !') : __('The file already exists !');
			$curl->aff[1] = 'rename';
			break;
		}

		if ($obj->rename($cobj->file, $_POST['rn_newname'])) {
			$msg = view_status(__('The objet was renamed !'));
			$var = isset($_POST['rn_redirect']) ? url::getObj($newname) : url::getObj(($cobj->type == TYPE_DIR) ? file::downPath($cobj->path) : $cobj->path);
		} else {
			$msg = view_error(__('An error occured during rename !'));
			$var = url::getObj($cobj->file);
		}

		redirect($cobj->file, $var, $msg);
		system::end();
		break;

	#	Déplacement
	case 'move':
		test_perm(($cobj->type == TYPE_DIR ? (CREATE_DIR | DEL_DIR) : (ADD_FILE | DEL_FILE)));

		// Le déplacement d'une archive n'est pas possible
		if ($cobj->type == TYPE_ARCHIVE) {
			redirect($cobj->file, url::getCurrentObj(), __('Not implemented !'));
			system::end();
		}

		// On ne peut pas déplacer la racine
		if ($cobj->file == '/') {
			redirect(__('Error'), url::getCurrentObj(), __('Impossible to move the root'));
			system::end();
		}

		//	Si le répertoire de destination n'est pas valable
		if (!file::getRealDir($_POST['mv_destination'], FOLDER_ROOT)) {
			redirect(__('Error'), url::getCurrentObj(), __('An error occured during move !'));
			system::end();
		}

		// On vérifie tout d'abord si l'objet de destination existe déjà
		$dest = ($cobj->type == TYPE_FILE) ? ($_POST['mv_destination'] == '/' ? '/' : $_POST['mv_destination'].'/').$cobj->name :
				($_POST['mv_destination'] == '/' ? '/' : $_POST['mv_destination'].'/').file::getLastDir($cobj->path);

		// On test si l'objet final existe déjà ou non
		if (file_exists(FOLDER_ROOT.$dest)) {
			$msg_error = is_dir(FOLDER_ROOT.$dest) ? __('The dir already exists !') : __('The file already exists !');
			$curl->aff[1] = 'move';
			break;
		}

		// On vérifie que l'on essaie pas de copier le répertoire sur lui même !
		if ($cobj->type == TYPE_DIR && file::getRealDir($_POST['mv_destination'], FOLDER_ROOT).'/' == $cobj->file) {
			$msg_error = __('Impossible to move dir on him !');
			$curl->aff[1] = 'move';
			break;
		}

		// Sinon, on déplace !
		if ($ret = $obj->move($cobj->realpath, $_POST['mv_destination'])) {
			$msg = view_status(__('%s objets was moved !', $ret));
			$var = isset($_POST['mv_redirect']) ? url::getObj($dest) : url::getObj(($cobj->type == TYPE_DIR) ? file::downPath($cobj->path) : $cobj->path);
		} else {
			$msg = view_error(__('An error occured during move !'));
			$var = url::getObj(($cobj->type == TYPE_DIR) ? file::downPath($cobj->path) : $cobj->file);
		}

		redirect($cobj->file, $var, $msg);
		system::end();
		break;

	#	Suppression d'un objet
	case 'del':
		test_perm(($cobj->type == TYPE_DIR) ? DEL_DIR : DEL_FILE);

		// La suppression d'une archive n'est pas possible
		if ($cobj->type == TYPE_ARCHIVE) {
			redirect($cobj->file, url::getCurrentObj(), __('Not implemented !'));
			system::end();
		}

		// On ne peut pas supprimer la racine
		if ($cobj->file == '/') {
			redirect(__('Error'), url::getCurrentObj(), __('Impossible to remove the root'));
			system::end();
		}

		// Faut avoir les droits !
		if (!is_writable($cobj->realpath)) {
			redirect(__('Error'), url::getCurrentObj(), __('Impossible to remove object, check permissions !'));
			system::end();
		}

		cache::del($cobj->file);

		$msg = $obj->delete($cobj) ? view_status(__('Object was deleted')) : view_error(__('An error occured during delete !'));
		redirect($cobj->file, url::getObj((($cobj->type == TYPE_DIR) ? file::downPath($cobj->path) : $cobj->path)), $msg);
		system::end();

		break;

	#	Création d'un répertoire
	case 'mkdir':
		test_perm(CREATE_DIR);

		// Il ne doit pas y avoir de caractère interdit !
		if (string::test($_POST['mk_name'], UNAUTHORIZED_CHAR)) {
			$msg_error = __('There are an invalid char in the file name, unauthorized char are : %s', UNAUTHORIZED_CHAR);
			$curl->aff[1] = 'mkdir';
			break;
		}

		// On vérifie tout d'abord si l'objet de destination existe déjà
		$dest = $cobj->path.($cobj->path == '/' ? null : '/').$_POST['mk_name'];
		if (file_exists(FOLDER_ROOT.$dest)) {
			$msg_error = __('The dir already exists !');
			$curl->aff[1] = 'mkdir';
			break;
		}

		$msg = (mkdir(FOLDER_ROOT.$dest, $conf['dir_chmod'])) ? __('Dir created !') : __('An unknown error occured during dir creation !');
		$var = isset($_POST['mk_redirect']) ? url::getObj($dest) : url::getCurrentObj();

		redirect($cobj->file, $var, $msg);
		system::end();

		break;
}


?>
