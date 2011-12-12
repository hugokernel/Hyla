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

$tpl->set_file('admin', 'admin.tpl');

$tpl->set_block('admin', array(
		'test_version'			=>	'Hdltest_version',
		'aff_home'				=>	'Hdlaff_home',
		'aff_conf'				=>	'Hdlaff_conf',
		'comment_line'			=>	'Hdlcomment_line',
		'comment'				=>	'Hdlcomment',
		'anon_move_dir_occ'	=>	'Hdlanon_move_dir_occ',
		'anon_move'			=>	'Hdlanon_move',
		'anon_line'				=>	'Hdlanon_line',
		'anon'					=>	'Hdlanon',
		'user_edit_password'	=>	'Hdluser_edit_password',
		'user_edit'				=>	'Hdluser_edit',
		'user_add'				=>	'Hdluser_add',
		'users_line_del'		=>	'Hdlusers_line_del',
		'users_line'			=>	'Hdlusers_line',
		'users'					=>	'Hdlusers',
		));

$msg = null;
$msg_error = null;

switch (@$curl->aff[2]) {

	#	Gestion du cache
	case 'cache':
		if (@$curl->aff[3] == 'del') {
			if (file::rmDirs(DIR_ROOT.DIR_CACHE))
				system::end(__('Cache vidé !'));
		}
		break;

	#	Les utilisateurs
	case 'users':

		$usr = new users();

		function get_perm_from_form() {
			$tab_act = array(
					'ad_add_comment'	=>	ADD_COMMENT,
					'ad_add_file'		=>	ADD_FILE,
					'ad_edit_file'		=>	EDIT_FILE,
					'ad_del_file'		=>	DEL_FILE,
					'ad_create_dir'		=>	CREATE_DIR,
					'ad_del_dir'		=>	DEL_DIR,
					'ad_admin'			=>	ADMIN,
					);
			$val = 0;
			foreach ($tab_act as $k => $v) {
				if (isset($_POST[$k])) {
					$val |= $v;
				}
			}
			return $val;
		}

		switch (@$curl->aff[3]) {

			case 'add':
				if (@$curl->aff[4] == 'save') {
					$ret = $usr->testLogin($_POST['ad_login']);
					if ($ret == -1) {
						$msg_error = view_error(__('The name is invalid !'));
						$_POST['ad_login'] = null;
					} else if (!$ret) {
						$msg_error = view_error(__('User already exists !'));
						$_POST['ad_login'] = null;
					} else if (empty($_POST['ad_password']) || empty($_POST['ad_password_bis']))
						$msg_error = view_error(__('All the fields must be filled'));
					else if ($_POST['ad_password'] != $_POST['ad_password_bis'])
						$msg_error = view_error(__('Passwords are different'));
					else {
						$id = $usr->addUser($_POST['ad_login'], $_POST['ad_password']);
						$usr->setPerm($id, get_perm_from_form());
						break;
					}
					$tpl->set_var('NAME', $_POST['ad_login']);
				}

				$tpl->set_var(array(
						'FORM_USER_SAVE'	=>	url::getPage(array('admin', 'users', 'add', 'save')),
						'ERROR'				=>	$msg_error,
						));
				$tpl->parse('Hdluser_add', 'user_add', true);
				break;

			case 'del':
				// Pas le droit de s'autosupprimer ni, de supprimer l'utilisateur anonyme
				if ($curl->aff[4] != $cuser->id && $curl->aff[4] != ANONYMOUS_ID) {
					$usr->delUser($curl->aff[4]);
				}
				break;


			case 'saveperm':
				$val = get_perm_from_form();
				if ($curl->aff[4] == $cuser->id) {
					if (!($val & ADMIN))
						$msg = view_error(__('Unable to change his own administration permission !'));
					$val |= ADMIN;
				}

				$usr->setPerm($curl->aff[4], $val);

			case 'savepassword':

				// Pas propre ça !
				if (@$curl->aff[3] == 'savepassword') {
					if (empty($_POST['ad_password']) || empty($_POST['ad_password_bis'])) {
						$msg = view_error(__('All the fields must be filled'));
					} else if ($_POST['ad_password'] != $_POST['ad_password_bis']) {
						$msg = view_error(__('Passwords are different'));
					} else if ($curl->aff[4] != ANONYMOUS_ID && isset($_POST['ad_password']) && !empty($_POST['ad_password'])) {
						$usr->setPassword($curl->aff[4], $_POST['ad_password']);
						$msg = view_status(__('Password changed !'));
					}
				}

			case 'edit':
				$tab = $usr->getUser($curl->aff[4]);

				if ($tab->id != 1)
					$tpl->parse('Hdluser_edit_password', 'user_edit_password', true);

				$tpl->set_var(array(
						'USER_NAME'					=>	$tab->name,
						'FORM_USER_EDIT_PERM'		=>	url::getPage(array('admin', 'users', 'saveperm', $curl->aff[4])),
						'FORM_USER_EDIT_PASSWORD'	=>	url::getPage(array('admin', 'users', 'savepassword', $curl->aff[4])),
						'CHECKBOX_ADD_COMMENT'		=>	($tab->perm & ADD_COMMENT) ? 'checked="checked"' : null,
						'CHECKBOX_ADD_FILE'			=>	($tab->perm & ADD_FILE) ? 'checked="checked"' : null,
						'CHECKBOX_EDIT_FILE'		=>	($tab->perm & EDIT_FILE) ? 'checked="checked"' : null,
						'CHECKBOX_DEL_FILE'			=>	($tab->perm & DEL_FILE) ? 'checked="checked"' : null,
						'CHECKBOX_CREATE_DIR'		=>	($tab->perm & CREATE_DIR) ? 'checked="checked"' : null,
						'CHECKBOX_DEL_DIR'			=>	($tab->perm & DEL_DIR) ? 'checked="checked"' : null,
						'CHECKBOX_ADMIN'			=>	($tab->perm & ADMIN) ? 'checked="checked"' : null,
						'MSG'						=>	$msg,
						));

				$tpl->parse('Hdluser_edit', 'user_edit', true);
				break;
		}

		$tab = $usr->getUsers();
		$size = sizeof($tab);
		for ($i = 0; $i < $size; $i++) {
			$tpl->set_var(array(
					'Hdlusers_line_del'	=>	null,
					'USER_ID'			=>	$tab[$i]->id,
					'USER_NAME'			=>	$tab[$i]->name,
					'PERM_ADD_COMMENT'	=>	($tab[$i]->perm & ADD_COMMENT) ? __('Ok') : __('No'),
					'PERM_ADD_FILE'		=>	($tab[$i]->perm & ADD_FILE) ? __('Ok') : __('No'),
					'PERM_EDIT_FILE'	=>	($tab[$i]->perm & EDIT_FILE) ? __('Ok') : __('No'),
					'PERM_DEL_FILE'		=>	($tab[$i]->perm & DEL_FILE) ? __('Ok') : __('No'),
					'PERM_CREATE_DIR'	=>	($tab[$i]->perm & CREATE_DIR) ? __('Ok') : __('No'),
					'PERM_DEL_DIR'		=>	($tab[$i]->perm & DEL_DIR) ? __('Ok') : __('No'),
					'PERM_ADMIN'		=>	($tab[$i]->perm & ADMIN) ? __('Ok') : __('No'),
					'ADMIN_USER_EDIT'	=>	url::getPage(array('admin', 'users', 'edit', $tab[$i]->id)),
					'ADMIN_USER_DEL'	=>	url::getPage(array('admin', 'users', 'del', $tab[$i]->id)),
					));

			if ($tab[$i]->id != 1 && $tab[$i]->id != $cuser->id)
				$tpl->parse('Hdlusers_line_del', 'users_line_del', true);

			$tpl->parse('Hdlusers_line', 'users_line', true);
		}

		$tpl->set_var(array(
//				'MSG'				=>	$msg_error,
				'ADMIN_USER_ADD'	=>	url::getPage(array('admin', 'users', 'add')),
				));

		$tpl->parse('Hdlusers', 'users', true);
		break;

	#	Les commentaires
	case 'comment':

		// Suppression d'un commentaire !
		if (@$curl->aff[3] == 'del') {
			if (intval($curl->aff[4]))
				$obj->delComment(intval($curl->aff[4]));
		}

		$tab = $obj->getLastComment();
		$size = sizeof($tab);
		for ($i = 0; $i < $size; $i++) {
			$tpl->set_var(array(
					'FILE_ICON'			=>	get_icon(file::getExtension($tab[$i]->object)),
					'PATH_INFO'			=>	url::getObj($tab[$i]->object),
					'PATH_FORMAT'		=>	format($tab[$i]->object, false),
					'COMMENT'			=>	$tab[$i]->content,
					'AUTHOR'			=>	$tab[$i]->author,
					'MAIL'				=>	(empty($tab[$i]->mail) ? (empty($tab[$i]->url) ? '#' : $tab[$i]->url) : 'mailto:'.$tab[$i]->mail),
					'URL'				=>	(empty($tab[$i]->mail) ? null : $tab[$i]->url),
					'DATE'				=>	format_date($tab[$i]->date, 1),
					'ADMIN_DEL_COMMENT'	=>	url::getPage(array('admin', 'comment', 'del', $tab[$i]->id)),
					));
			$tpl->parse('Hdlcomment_line', 'comment_line', true);
		}

		$tpl->set_var('MSG', (!$size) ? __('There are no comments !') : null);
		$tpl->parse('Hdlcomment', 'comment', true);
		break;

	#	Affichage de la configuration
	case 'conf':

		// Sauvegarde de la config !
		if (@$curl->aff[3] == 'save') {
			$ini = new iniFile(FILE_INI);

			// Général
			$ini->editVar('webmaster_mail',	$_POST['conf_webmaster_mail']);
			$ini->editVar('template', 			$_POST['conf_template']);
			$ini->editVar('lng', 				$_POST['conf_lng']);
			$ini->editVar('title', 				$_POST['conf_title']);

			// Ajout de fichiers et répertoires
			$ini->editVar('file_chmod', 		$_POST['conf_file_chmod']);
			$ini->editVar('dir_chmod', 		$_POST['conf_dir_chmod']);
			$ini->editVar('anonymous_add_file', $_POST['conf_anonymous_add_file']);
			$ini->editVar('send_mail', 		$_POST['conf_send_mail']);

			// Listage de répertoires
			$ini->editVar('sort', 				$_POST['conf_sort']);
			$ini->editVar('folder_first', 		$_POST['conf_folder_first']);
			$ini->editVar('group_by_sort', 		$_POST['conf_group_by_sort']);
			$ini->editVar('nbr_obj', 			$_POST['conf_nbr_obj']);

			// Divers
			$ini->editVar('view_hidden_file', 	$_POST['conf_view_hidden_file']);
			$ini->editVar('download_counter', 	$_POST['conf_download_counter']);

			$ini->editVar('default_plugin', 	$_POST['conf_default_plugin']);
			$ini->editVar('view_toolbar', 		$_POST['conf_view_toolbar']);

			if (!$ini->saveFile())
				$msg = __('Couldn\'t write configuration file ( %s ) !', FILE_INI);
			else
				load_config();

			unset($ini);
		}

		$tpl->set_var(array(
				'WEBMASTER_MAIL'	=>	$conf['webmaster_mail'],
				'NAME_TEMPLATE'		=>	$conf['name_template'],
				'TITLE'				=>	$conf['title'],
				'LNG'				=>	$conf['lng'],
				'FILE_CHMOD'		=>	decoct($conf['file_chmod']),
				'DIR_CHMOD'			=>	decoct($conf['dir_chmod']),
				($conf['anonymous_add_file'] ? 'CONF_ANONYMOUS_ADD_FILE_1' : 'CONF_ANONYMOUS_ADD_FILE_0')	=>	'selected="selected"',
				($conf['send_mail'] ? 'CONF_SEND_MAIL_1' : 'CONF_SEND_MAIL_0')	=>	'selected="selected"',
				'ADMIN_PAGE_SAVECONF'		=>	url::getPage(array('admin', 'conf', 'save')),
				));

		$folder_first = false;
		switch ($conf['sort_config']) {
			case SORT_DEFAULT:							$tpl->set_var('CONF_SORT_0', 'selected="selected"');	break;
			case SORT_ALPHA:							$tpl->set_var('CONF_SORT_1', 'selected="selected"');	break;
			case SORT_ALPHA | SORT_FOLDER_FIRST:		$tpl->set_var('CONF_SORT_1', 'selected="selected"');	$folder_first = true;	break;
			case SORT_ALPHA_R:							$tpl->set_var('CONF_SORT_2', 'selected="selected"');	break;
			case SORT_ALPHA_R | SORT_FOLDER_FIRST:		$tpl->set_var('CONF_SORT_2', 'selected="selected"');	$folder_first = true;	break;

			case SORT_ALPHA_EXT:						$tpl->set_var('CONF_SORT_3', 'selected="selected"');	break;
			case SORT_ALPHA_EXT | SORT_FOLDER_FIRST:	$tpl->set_var('CONF_SORT_3', 'selected="selected"');	$folder_first = true;	break;
			case SORT_ALPHA_EXT_R:						$tpl->set_var('CONF_SORT_3', 'selected="selected"');	break;
			case SORT_ALPHA_EXT_R | SORT_FOLDER_FIRST:	$tpl->set_var('CONF_SORT_4', 'selected="selected"');	$folder_first = true;	break;
				break;
		}

		$tpl->set_var(array(
				($folder_first ? 'CONF_FOLDER_FIRST_1' : 'CONF_FOLDER_FIRST_0')		=>	'selected="selected"',
				($conf['group_by_sort'] ? 'CONF_GROUP_BY_SORT_1' : 'CONF_GROUP_BY_SORT_0')	=>	'selected="selected"',
				'NBR_OBJ'		=>	$conf['nbr_obj'],
				));

		$tpl->set_var(array(
				($conf['view_hidden_file'] ? 'CONF_VIEW_HIDDEN_FILE_1' : 'CONF_VIEW_HIDDEN_FILE_0')	=>	'selected="selected"',
				($conf['download_counter'] ? 'CONF_DOWNLOAD_COUNTER_1' : 'CONF_DOWNLOAD_COUNTER_0')	=>	'selected="selected"',
				'DIR_DEFAULT_PLUGIN'	=>	$conf['dir_default_plugin'],
				($conf['view_toolbar'] ? 'CONF_VIEW_TOOLBAR_1' : 'CONF_VIEW_TOOLBAR_0')				=>	'selected="selected"',
				));

		$tpl->set_var(array(
				'ERROR'		=>	isset($msg) ? view_error($msg) : null,
				'FILE_INI'	=>	FILE_INI,
				));

		$tpl->parse('Hdlaff_conf', 'aff_conf', true);
		break;

	#	Les fichiers anonymes
	case 'anon':

		$lobj = new obj(DIR_ROOT.DIR_ANON);

		switch (@$curl->aff[3]) {
			case 'download':
				if (file::getRealFile($curl->obj, DIR_ROOT.DIR_ANON)) {
					file::sendFile(DIR_ROOT.DIR_ANON.$curl->obj);
					system::end();
				}
				break;

			case 'move':

				if (@$curl->aff[4] == 'save') {

					// On vérifie tout d'abord si l'objet de destination existe déjà
					$dest = ($_POST['mv_destination'] == '/' ? null : $_POST['mv_destination']).$curl->obj;
					if (file_exists(FOLDER_ROOT.$dest)) {
						$msg = __('The file already exists !');
						$var = url::getPage(array('admin', 'anon', 'move'), $curl->obj);
						redirect(__('Error'), $var, $msg);
						system::end();
					}

					// Sinon, on déplace !
					if ($ret = $obj->move(DIR_ROOT.DIR_ANON.$curl->obj, $_POST['mv_destination'], DIR_ROOT.DIR_ANON, PREFIX_ANON)) {
						$msg = view_status(__('%s objets was moved !', $ret));
						$var = isset($_POST['mv_redirect']) ? url::getObj($dest) : url::getPage(array('admin', 'anon'));
					} else {
						$msg = view_error(__('An error occured during move !'));
						$var = url::getPage(array('admin', 'anon'), $curl->obj);
					}

					redirect($curl->obj, $var, $msg);
					system::end();
				}

				$tpl->set_var(array(
						'FILE'				=>	$curl->obj,
						'FORM_ANON_MOVE'	=>	url::getPage(array('admin', 'anon', 'move', 'save'), $curl->obj),
						));
				$tab = file::scanDir(FOLDER_ROOT);
				foreach ($tab as $occ) {
					$tpl->set_var(array(
							'DIR_NAME'	=>	$occ,
							));
					$tpl->parse('Hdlanon_move_dir_occ', 'anon_move_dir_occ', true);
				}
				$tpl->parse('Hdlanon_move', 'anon_move', true);
				$tpl->parse('Hdlanon', 'anon', true);
				break;

			case 'del':
				$file = $lobj->getInfo($curl->obj ,false, false);
				if ($file->type != TYPE_UNKNOW) {
					$lobj->delete($file);
				}

			default:

				$lobj->_prefix = PREFIX_ANON;
				$tab = $lobj->getDirContent('/', null, 0, 10000, null);

				$size = sizeof($tab);
				for ($i = 0, $cmpt = 0; $i < $size; $i++) {

					if (is_dir($tab[$i]->realpath))
						continue;

					$tpl->set_var(array(
							'FILE_ICON'			=>	$tab[$i]->icon,
							'FILE_NAME'			=>	$tab[$i]->name,
							'FILE_SIZE'			=>	get_human_size_reading($tab[$i]->size),
							'FILE_DESCRIPTION'	=>	($tab[$i]->info->description) ? $tab[$i]->info->description : __('No description !'),
							'PATH_DOWNLOAD'		=>	url::getPage(array('admin', 'anon', 'download'), $tab[$i]->file),
							'ADMIN_ANON_DEL'	=>	url::getPage(array('admin', 'anon', 'del'), $tab[$i]->file),
							'ADMIN_ANON_MOVE'	=>	url::getPage(array('admin', 'anon', 'move'), $tab[$i]->file),
							));

					$tpl->parse('Hdlanon_line', 'anon_line', true);
					$cmpt++;
				}

				$tpl->set_var('MSG', (!$cmpt) ? __('The are no file !') : null);

				unset($lobj);
				$tpl->parse('Hdlanon', 'anon', true);
				break;
		}
		break;

	#	Test si une version plus récente existe
	case 'testver':

		if (ini_get('allow_url_fopen')) {
			$var = file::getContent(URL_TEST_VERSION);
			$res = strcmp(trim(HYLA_VERSION), trim($var));
			if ($res < 0)
				$msg = __('A new version ( %s ) is disponible !', $var);
			else
				$msg = __('You have the latest version !');
		}

		$tpl->set_var('STATUS_VERSION', $msg);

	#	Accueil
	default:

		if (ini_get('allow_url_fopen'))
			$tpl->parse('Hdltest_version', 'test_version', true);

		$tpl->set_var(array(
				'CONFIG_ALLOW_URL_FOPEN'	=>	ini_get('allow_url_fopen') ? __('Ok') : __('No'),
				'CONFIG_FILE_UPLOADS'		=>	ini_get('file_uploads') ? __('Ok') : __('No'),
				'CONFIG_UPLOAD_MAX_FILESIZE'=>	ini_get('upload_max_filesize'),

				'FILE_INI'					=>	FILE_INI,
				'DIR_CACHE'					=>	DIR_CACHE,
				'DIR_ANON'					=>	DIR_ANON,

				'ACCESS_FILE_INI'			=>	is_writable(DIR_ROOT.FILE_INI) ? __('Ok') : __('No'),
				'ACCESS_DIR_CACHE'			=>	is_writable(DIR_ROOT.DIR_CACHE) ? __('Ok') : __('No'),
				'ACCESS_DIR_ANON'			=>	is_writable(DIR_ROOT.DIR_ANON) ? __('Ok') : __('No'),

				'EXTENSION_GD'				=>	extension_loaded('gd') ? __('Ok') : __('No'),
				'EXTENSION_EXIF'			=>	extension_loaded('exif') ? __('Ok') : __('No'),
				'TEST_VERSION'				=>	url::getPage(array('admin', 'testver'))
				));

		$tpl->parse('Hdlaff_home', 'aff_home', true);
		break;
}



$tpl->set_var(array(
		'ADMIN_PAGE'			=>	url::getPage('admin'),
		'ADMIN_PAGE_CONF'		=>	url::getPage(array('admin', 'conf')),
		'ADMIN_PAGE_USERS'		=>	url::getPage(array('admin', 'users')),
		'ADMIN_PAGE_COMMENT'	=>	url::getPage(array('admin', 'comment')),
		'ADMIN_PAGE_ANON'		=>	url::getPage(array('admin', 'anon')),
		'ERROR'					=>	$msg_error,
		));

$var_tpl = $tpl->parse('OutPut', 'admin');

?>
