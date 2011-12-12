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

require 'src/inc/image.class.php';

// Template...
$tpl = new Template(DIR_TEMPLATE);
$tpl->set_file(array(
		'index'	 	=>	'index.tpl',
		'misc'		=>	'misc.tpl',
		'comment'	=>	'comment.tpl',
		'obj'		=>	'obj.tpl',
		'toolbar'	=>	'toolbar.tpl',
		));

$tpl->set_block('index', array(
		'rss_obj'			=>	'Hdlrss_obj',
		'rss_comment'		=>	'Hdlrss_comment',
		));

$tpl->set_block('misc', array(
		'error'				=>	'Hdlerror',
		'status'			=>	'Hdlstatus',
		'suggestion'		=>	'Hdlsuggestion',
		'sort'				=>	'Hdlsort',

		'action_rename'		=>	'Hdlaction_rename',
		'action_mkdir'		=>	'Hdlaction_mkdir',
		'action_del'		=>	'Hdlaction_del',
		'action_addfile'	=>	'Hdlaction_addfile',
		'action_edit'		=>	'Hdlaction_edit',
		'action_move'		=>	'Hdlaction_move',
		'action_copy'		=>	'Hdlaction_copy',

		'aff_info'			=>	'Hdlaff_info',

		'aff_download'		=>	'Hdlaff_download',
		'aff_slideshow'		=>	'Hdlaff_slideshow',
		'aff_login'	 		=>	'Hdlaff_login',
		'aff_logout'		=>	'Hdlaff_logout',
		'aff_admin'			=>	'Hdlaff_admin',

		'toolbar'			=>	'Hdltoolbar',
		));

$tpl->set_block('comment', array(
		'comment_line'		=>	'Hdlcomment_line',
		'add_comment'		=>	'Hdladd_comment',
		'current_comment'	=>	'Hdlcurrent_comment',
		'last_comment_line' =>	'Hdllast_comment_line',
		'last_comment'		=>	'Hdllast_comment'
		));

$tpl->set_block('obj', array(
		'dir_previous_page'	=>	'Hdldir_previous_page',

		'dir_page_num_cur'	=>	'Hdldir_page_num_cur',
		'dir_page_num'		=>	'Hdldir_page_num',
		'dir_page'			=>	'Hdldir_page',

		'dir_next_page'		=>	'Hdldir_next_page',
		'dir_pagination'	=>	'Hdldir_pagination',
		'previous_page'		=>	'Hdlprevious_page',
		'next_page'			=>	'Hdlnext_page',
		'description'		=>	'Hdldescription',
		'pagination'		=>	'Hdlpagination',
		'with_tree'			=>	'Hdlwith_tree',
		'no_tree'			=>	'Hdlno_tree'
		));


$l10n->setFile('general.php');
$l10n->setFile('aff.php');

$var_tpl = null;
$start = null;
$title = null;
$plugin_run = false;



$aff = (url::getQueryAff(0) == 'obj') ? url::getQueryAff(1) : url::getQueryAff(0);

if (url::getQueryAff(1) != 'login')
	$_SESSION['sess_url'] = null;

switch ($aff) {

	#	Cas spécial : Ne nécessite pas l'affichage de l'objet courant et de ces actions courante
	case 'page':

		switch (url::getQueryAff(1)) {
			#	Affichage des derniers commentaires
			case 'lastcomment':

				acl_test(AC_VIEW);

				$tab = $obj->getLastComment();
				$size = sizeof($tab);
				for ($i = 0; $i < $size; $i++) {

					// Le bon icone
					if (is_dir(FOLDER_ROOT.$tab[$i]->object)) {
						if ($tab[$i]->object == '/')
							$icon = REAL_ROOT_URL.DIR_IMAGE.'/home.png';
						else
							$icon = (file_exists($tab[$i]->icon)) ? $tab[$i]->icon : DIR_PLUGINS_OBJ.strtolower($conf['dir_default_plugin']).'/'.DIR_ICON;
					} else {
						$icon = get_icon(file::getExtension($tab[$i]->object));
					}

					$tpl->set_var(array(
							'PATH_FORMAT'	=>	format($tab[$i]->object, false),
							'FILE_ICON'		=>	$icon,
							'PATH_INFO'		=>	url::getObj($tab[$i]->object),
							'COMMENT'		=>	$tab[$i]->content,
							'AUTHOR'		=>	$tab[$i]->author,
							'EMAIL'			=>	$tab[$i]->mail,
							'URL'			=>	($tab[$i]->url ? $tab[$i]->url : null),
							'DATE'			=>	format_date($tab[$i]->date, 1),
							'ID'			=>	$tab[$i]->id,
							));
					$tpl->parse('Hdllast_comment_line', 'last_comment_line', true);
				}

				$tpl->set_var('MSG', (!$size) ? __('There are no comments !') : (($size > 1) ? __('Comments from most recent to oldest.') : null));

				$tpl->parse('Hdllast_comment', 'last_comment', true);
				$var_tpl .= $tpl->parse('OutPut', 'comment');

				$tpl->set_var('URL_RSS_COMMENT', url::getRss($cobj->file, 'allcomment'));
				$tpl->parse('Hdlrss_comment', 'rss_comment', true);

				$title = __('Last comment');
				break;

			#	Administration
			case 'admin':
				acl_test(ADMINISTRATOR_ONLY);
				include('src/admin.php');
				$title = __('Administration');
				break;

			#	Login
			case 'login':
				include('src/login.php');
				$title = __('Login');
				break;

			default:
				system::end('Fatal error !');
		}

		break;

	#	Login !
	case 'login':
		include('src/login.php');
		break;

	#	Copier / Déplacer
	case 'copy':
		acl_test(AC_COPY);
		include('src/action.php');
		break;

	#	Renommer
	case 'rename':
		acl_test(AC_RENAME);
		include('src/action.php');
		break;

	#	Déplacer
	case 'move':
		acl_test(AC_MOVE);
		include('src/action.php');
		break;

	#	Créer un répertoire
	case 'mkdir':
		acl_test(AC_CREATE_DIR);
		include('src/action.php');
		break;

	#	Upload
	case 'upload':
		include('src/upload.php');
		break;

	#	La page de recherche
	case 'search':
		include('src/search.php');
		break;

	#	Téléchargement
	case 'download':

		$status = false;
		switch ($cobj->type) {

			#	On envoie simplement le fichier
			case TYPE_FILE:
				file::sendFile($cobj->realpath);
				$status = true;
				break;

			#	On "tar" le répertoire et on l'envoie
			case TYPE_DIR:

				// Si la configuration l'autorise
				if ($conf['download_dir']) {

					$file = null;
					if (!cache::getArchivePath($cobj->file, $file)) {
						$file = file::dirName($_SERVER['SCRIPT_FILENAME']).'/'.$file.'.tar';
						$out = archive::createFromDir($file, $cobj->realpath);
						if ($out) {
							file::sendFile($file);
							$status = true;
						} else
							redirect(__('Error'), file::downPath(url::getObj($cobj->path)), __('Dir is probably empty or not readable !'));
					}
				} else {
					redirect(__('Error'), file::downPath(url::getObj($cobj->file)), __('This functionality is disabled !'));
				}
				break;

			#	On extrait le fichier et on l'envoie
			case TYPE_ARCHIVED:
				$file = null;

				if (!cache::getFilePath($cobj->file, $file)) {
					archive::extract($cobj->realpath, $file);
				}

				// Si le fichier n'est pas trouvé dans l'archive : Erreur !
				if (!file_exists(file::dirName($_SERVER['SCRIPT_FILENAME']).'/'.$file.'/'.$cobj->target)) {
					header('HTTP/1.x 404 Not Found');
					redirect(__('Error'), url::getObj($cobj->file), __('Object not found !'));
					system::end();
				}

				file::sendFile(file::dirName($_SERVER['SCRIPT_FILENAME']).'/'.$file.'/'.$cobj->target);
				$status = true;
				break;
		}

		if ($conf['download_counter'] && $status) {
			$obj->addDownload();
		}

		system::end();
		break;

	#	Donne des infos sur l'objet
	case 'info':
		include('src/info.php');
		break;

	#	Affichage d'une image en miniature
	case 'mini':
		if ($cobj->type == TYPE_ARCHIVED) {
			$file = null;
			if (!cache::getFilePath($cobj->file, $file)) {
				archive::extract(FOLDER_ROOT.$cobj->file, $file.'/'.basename($cobj->file));
			}
		}

		$sizex = (url::getQueryAff(2) ? url::getQueryAff(2) : THUMB_SIZE_X);
		$sizey = (url::getQueryAff(3) ? url::getQueryAff(3) : 0);

		// Le cache
		if (cache::getImagePath(get_real_directory(), $sizex, $sizey, $cache_path)) {

			header('Content-type: image/'.file::getExtension(get_real_directory()));
			header('ETag: '.get_real_directory().'-('.$sizex.'x'.$sizey.')');
			header('Cache-Control: max-age=1296000, s-maxage=1296000, proxy-revalidate, must-revalidate');
			header('Date: '.gmdate('D, d M Y H:i:s', time()).' GMT');

			if ($cobj->info->date_last_update)
				header('Last-Modified: '.gmdate('D, d M Y H:i:s', $cobj->info->date_last_update).' GMT');

			header('Content-Length: '.filesize(get_real_directory()));
			header('Expires: '.gmdate('D, d M Y H:i:s', time() + 1296000).' GMT');	// Expire dans 15 jours

			readfile($cache_path);
		} else {
			image::resize(get_real_directory(), $sizex, $sizey, $cache_path);
		}

		system::end();
		break;

	#	Édition
	case 'edit':
		acl_test(AC_EDIT_DESCRIPTION, AC_EDIT_PLUGIN);
		include('src/edit.php');
		break;

	#	Affichage de l'objet
	case 'start':
		$start = url::getQueryAff(2);
		$_SESSION['sess_start'] = $start;
	default:
	case 'obj':

		url::setQueryAff(0, 'obj');

		$force_plugin = (url::getQueryAct(0) == 'force') ? url::getQueryAct(1) : null;

		if (!is_readable($cobj->realpath)) {
			$var_tpl = view_error(__('Object not readable !'));
		} else {
			// Chargement d'un plugin...
			$plugins = new plugins($cobj);

			if ($cobj->type == TYPE_FILE) {
				if ($cobj->extension)
					$plugins->search();
			} else if ($cobj->type == TYPE_DIR) {
				$plugins->info['name'] = ($cobj->info->plugin) ? $cobj->info->plugin : $conf['dir_default_plugin'];					// TODO: mettre tout ce code dans la classe plugins
				$plugins->info['dir'] = ($cobj->info->plugin) ? $cobj->info->plugin : strtolower($conf['dir_default_plugin']);
			} else if ($cobj->type == TYPE_ARCHIVED) {

				if (!cache::getFilePath($cobj->file, $file)) {
					archive::extract($cobj->realpath, $file);
				}

				// Si le fichier n'est pas trouvé dans l'archive : Erreur !
				if (!file_exists(file::dirName($_SERVER['SCRIPT_FILENAME']).'/'.$file.'/'.$cobj->target)) {
					header('HTTP/1.x 404 Not Found');
					redirect(__('Error'), url::getObj($cobj->file), __('Object not found !'));
					system::end();
				}

				// Si on tente d'ouvrir de nouveau une archive, on stop !
				if ($cobj->extension == 'zip' || $cobj->extension == 'gz' || $cobj->extension == 'tar' || $cobj->extension == 'tar.gz') {
					redirect($cobj->file, url::getObj($cobj->file), __('Not implemented !'));
					system::end();
				}

				$plugins->search();
			}

			$var_tpl_plugin = $plugins->load($force_plugin);
			$plugin_run = true;

			//	Les commentaires sont uniquement pour les fichiers ou les dossiers
			if ($cobj->type == TYPE_FILE || $cobj->type == TYPE_DIR) {
				if ($cobj->info->nbr_comment) {
					for ($i = 0; $i < $cobj->info->nbr_comment; $i++) {
						$tpl->set_var(array(
								'ID'		=>	$cobj->info->comment[$i]->id,
								'COMMENT'	=>	$cobj->info->comment[$i]->content,
								'AUTHOR'	=>	$cobj->info->comment[$i]->author,
								'EMAIL'		=>	$cobj->info->comment[$i]->mail,
								'URL'		=>	($cobj->info->comment[$i]->url ? $cobj->info->comment[$i]->url : null),
								'DATE'		=>	format_date($cobj->info->comment[$i]->date, 1)));
						$tpl->parse('Hdlcomment_line', 'comment_line', true);
					}

					// Pour éviter que ça apparaisse dans les champs du formulaire d'envoie
					$tpl->set_var(array(
							'COMMENT'	=>	null,
							'AUTHOR'	=>	$cuser->id != ANONYMOUS_ID ? $cuser->name : (string::format(@$_POST['cm_author'], false)),
							'MAIL'		=>	null,
							'URL'		=>	null,
							'DATE'		=>	null));

					if ($cobj->info->nbr_comment > 1)
						$tpl->set_var('MSG', __('Comments posted from oldest to most recent.'));
				} else
					$tpl->set_var('MSG', __('There are no comments !'));

				if (acl::ok(AC_ADD_COMMENT)) {
					$tpl->parse('Hdladd_comment', 'add_comment', true);
				}

				if (acl::ok(AC_ADD_COMMENT) || $cobj->info->nbr_comment) {
					$tpl->parse('Hdlcurrent_comment', 'current_comment', true);

					$tpl->set_var(array(
							'OBJECT'			=>	view_obj($cobj->file),

							'FORM_ADD_COMMENT'	=>	url::getCurrentObj('', 'addcomment'),
							'AUTHOR'			=>	$cuser->id != ANONYMOUS_ID ? $cuser->name : @$_POST['cm_author'],
							'EMAIL'				=>	stripslashes(strip_tags(@$_POST['cm_mail'], ENT_QUOTES)),
							'SITE'				=>	stripslashes(strip_tags(@$_POST['cm_site'] ? $_POST['cm_site'] : 'http://', ENT_QUOTES)),
							'CONTENT'			=>	stripslashes(strip_tags(@$_POST['cm_content'])),
							'ERROR'				=>	view_error($msg_error),
							'COMMENT_NBR'		=>	$cobj->info->nbr_comment,
							));
					$var_tpl_comment = $tpl->parse('OutPut', 'comment');
				}
			}
		}

		break;
}

$tpl->set_var(array(
		'DOWNLOAD_COUNT'	=>	$cobj->info->dcount ? __('%s download(s)', $cobj->info->dcount) : null,
		'FILE_ICON'			=>	$cobj->icon,
		'OBJECT'			=>	view_obj($cobj->file),
		));

// La pagination
if ($cobj->type == TYPE_FILE || $cobj->type == TYPE_ARCHIVED) {

	if ($cobj->prev)
		$tpl->parse('Hdlprevious_page', 'previous_page', true);

	if ($cobj->next)
		$tpl->parse('Hdlnext_page', 'next_page', true);

	$tpl->set_var(array(
			'OBJ_PREV'			=>	view_obj(($cobj->type == TYPE_ARCHIVED) ? $cobj->prev->target : $cobj->prev->name),
			'OBJ_NEXT'			=>	view_obj(($cobj->type == TYPE_ARCHIVED) ? $cobj->next->target : $cobj->next->name),

			'PREV_PATH'			=>	url::getObj(($cobj->type == TYPE_ARCHIVED) ? $cobj->prev->file.'!'.$cobj->prev->target : $cobj->prev->file),
			'NEXT_PATH'			=>	url::getObj(($cobj->type == TYPE_ARCHIVED) ? $cobj->next->file.'!'.$cobj->next->target : $cobj->next->file),
			'PREV_FILE_ICON'	=>	$cobj->prev->icon,
			'NEXT_FILE_ICON'	=>	$cobj->next->icon
			));

	$tpl->parse('Hdlpagination', 'pagination', true);

} else if ($cobj->type == TYPE_DIR && $aff != 'search') {

	$nbr_obj = $obj->getNbrObject();
	if ($conf['nbr_obj'] > 0 && $nbr_obj > $conf['nbr_obj']) {

		if ($start)
			$start = ($start >= $nbr_obj) ? (($nbr_obj - $conf['nbr_obj'] < 0) ? 0 : $nbr_obj - $conf['nbr_obj']) : $start;

		// Page précédente
		if ($start > 0) {
			$tpl->parse('Hdldir_previous_page', 'dir_previous_page', true);
			$page = ($start <= $conf['nbr_obj']) ? 0 : $start - $conf['nbr_obj'];
			$tpl->set_var('PREV_PATH', url::getObj($cobj->path, array('start', $page)));
		}

		// Liste des pages
		$nbr_page = ($nbr_obj / $conf['nbr_obj']);
		if ($nbr_page) {
			for ($i = 0; $i < $nbr_page; $i++) {
				$page = $i * $conf['nbr_obj'];
				$tpl->set_var(array(
						'Hdldir_page_num_cur'	=> null,
						'PAGE_NUM'	=>	$i + 1,
						'PAGE_URL'	=>	url::getObj($cobj->path, array('start', $page)),
						));
				if ($start == $page)
					$tpl->parse('Hdldir_page_num_cur', 'dir_page_num_cur', true);
				$tpl->parse('Hdldir_page_num', 'dir_page_num', true);
			}
			$tpl->parse('Hdldir_page', 'dir_page', true);
		}

		// Page suivante
		if ($start < ($nbr_obj - $conf['nbr_obj'])) {
			$tpl->parse('Hdldir_next_page', 'dir_next_page', true);
			$tpl->set_var('NEXT_PATH' , url::getObj($cobj->path, array('start', $start + $conf['nbr_obj'])));
		}

		$var_tpl_pagination = $tpl->parse('Hdldir_pagination', 'dir_pagination', true);
	}
}


/*	Génération de la barre d'outils
 */
$tpl->set_var(array(
		'URL_MKDIR'		=>	url::getObj($cobj->path, 'mkdir'),
		'URL_SEARCH'	=>	url::getObj($cobj->path, 'search'),
		'URL_UPLOAD'	=>	url::getObj($cobj->path, 'upload'),
		'URL_INFO'		=>	url::getCurrentObj('info'),
		'URL_DOWNLOAD'	=>	url::getCurrentObj('download'),
		'URL_EDIT'		=>	url::getCurrentObj('edit'),
		'URL_COPY'		=>	url::getCurrentObj('copy'),
		'URL_MOVE'		=>	url::getCurrentObj('move'),
		'URL_RENAME'	=>	url::getCurrentObj('rename'),
		'URL_DEL'		=>	url::getCurrentObj('', 'del'),
		'URL_LOGIN'		=>	url::getPage('login'),
		'URL_LOGOUT'	=>	url::getCurrentObj('', 'logout'),
		'URL_ADMIN'		=>	url::getPage('admin'),
		'URL_COMMENT'	=>	url::getPage('lastcomment'),
		));

if ($cuser->id == ANONYMOUS_ID)
	$tpl->parse('Hdlaff_login', 'aff_login', true);
if (url::getQueryAff(0) != 'page') {
	$tpl->parse('Hdlaff_info', 'aff_info', true);
	if ($conf['view_toolbar'] || acl::ok(AC_ADD_FILE))
		$tpl->parse('Hdlaction_addfile', 'action_addfile', true);
	if (($conf['view_toolbar'] || acl::ok(AC_EDIT_DESCRIPTION, AC_EDIT_PLUGIN)) && $cobj->type != TYPE_ARCHIVED)
		$tpl->parse('Hdlaction_edit', 'action_edit', true);
	if ($conf['view_toolbar'] || acl::ok(AC_CREATE_DIR))
		$tpl->parse('Hdlaction_mkdir', 'action_mkdir', true);

	// On ne peut pas déplacer ou supprimer la racine !
	if ($cobj->file != '/') {
		if (($conf['view_toolbar'] || acl::ok(AC_RENAME)) && $cobj->type != TYPE_ARCHIVED)
			$tpl->parse('Hdlaction_rename', 'action_rename', true);
		if (($conf['view_toolbar'] || acl::ok(AC_MOVE)) && $cobj->type != TYPE_ARCHIVED)
			$tpl->parse('Hdlaction_move', 'action_move', true);
		if ($conf['view_toolbar'] || acl::ok(AC_COPY))
			$tpl->parse('Hdlaction_copy', 'action_copy', true);
		if (($conf['view_toolbar'] || acl::ok(($cobj->type == TYPE_DIR) ? AC_DEL_DIR : AC_DEL_FILE)) && $cobj->type != TYPE_ARCHIVED)
			$tpl->parse('Hdlaction_del', 'action_del', true);
	}

	// Diaporama
	if ($cobj->type == TYPE_DIR) {
		$tpl->set_var('URL_SLIDESHOW', url::getCurrentObj(null, array('force', 'slideshow')));
		$tpl->parse('Hdlaff_slideshow', 'aff_slideshow', true);
	}

	// Affichage du lien pour télécharger
	if ($cobj->type != TYPE_DIR || ($cobj->type == TYPE_DIR && $conf['download_dir']))
		$tpl->parse('Hdlaff_download', 'aff_download', true);
}

if ($cuser->id != ANONYMOUS_ID) {
	$tpl->set_var('USER_NAME', $cuser->name);
	$tpl->parse('Hdlaff_logout', 'aff_logout', true);
}

if ($cuser->type == USR_TYPE_ADMIN)
	$tpl->parse('Hdlaff_admin', 'aff_admin', true);

$tpl->set_var('Hdlsort');
$tpl->parse('Hdltoolbar', 'toolbar', true);
$var_tpl_toolbar = $tpl->parse('Hdlmisc', 'misc', true);



/*	Affichage de l'objet, de l'arbre, du tri...
 */
if (url::getQueryAff(0) == 'obj') {

	if ($cobj->type == TYPE_DIR) {

		switch ($sort) {
			case SORT_NAME_ALPHA:
			case SORT_NAME_ALPHA | SORT_FOLDER_FIRST:
				$tpl->set_var('SELECT_SORT_1', 'selected="selected"');
				break;
			case SORT_NAME_ALPHA_R:
			case SORT_NAME_ALPHA_R | SORT_FOLDER_FIRST:
				$tpl->set_var('SELECT_SORT_2', 'selected="selected"');
				break;
			case SORT_EXT_ALPHA:
			case SORT_EXT_ALPHA | SORT_FOLDER_FIRST:
				$tpl->set_var('SELECT_SORT_3', 'selected="selected"');
				break;
			case SORT_EXT_ALPHA_R:
			case SORT_EXT_ALPHA_R | SORT_FOLDER_FIRST:
				$tpl->set_var('SELECT_SORT_4', 'selected="selected"');
				break;
			case SORT_CAT_ALPHA:
			case SORT_CAT_ALPHA | SORT_FOLDER_FIRST:
				$tpl->set_var('SELECT_SORT_5', 'selected="selected"');
				break;
			case SORT_CAT_ALPHA_R:
			case SORT_CAT_ALPHA_R | SORT_FOLDER_FIRST:
				$tpl->set_var('SELECT_SORT_6', 'selected="selected"');
				break;
			case SORT_SIZE:
			case SORT_SIZE | SORT_FOLDER_FIRST:
				$tpl->set_var('SELECT_SORT_7', 'selected="selected"');
				break;
			case SORT_SIZE_R:
			case SORT_SIZE_R | SORT_FOLDER_FIRST:
				$tpl->set_var('SELECT_SORT_8', 'selected="selected"');
				break;
			case SORT_DEFAULT:
			case SORT_DEFAULT | SORT_FOLDER_FIRST:
				$tpl->set_var('SELECT_SORT_0', 'selected="selected"');
				break;
			default:
				$tpl->set_var('SELECT_SORT', 'selected="selected"');
				break;
		}

		if ($grp == 1)
			$tpl->set_var('GRP_CHECKED', ' checked="checked"');

		if ($sort & SORT_FOLDER_FIRST)
			$tpl->set_var('FFIRST_CHECKED', ' checked="checked"');

		$tpl->set_var('OBJECT', url::getObj($cobj->file));

		// Ajout du plugin
		if (isset($var_tpl_plugin))
			$var_tpl .= $var_tpl_plugin;

		// Affichage de barre de tri si le plugin à retourné quelque chose...
		if (isset($var_tpl_plugin) && !empty($var_tpl_plugin) && $plugin_run && $cobj->type == TYPE_DIR) {
			$var_tpl .= $tpl->parse('Hdlsort', 'sort', true);
		}

		// Ajout de la pagination
		if (isset($var_tpl_pagination)) {
			$var_tpl .= $var_tpl_pagination;
		}

		// Ajout du ou des commentaires
		if (isset($var_tpl_comment))
			$var_tpl .= $var_tpl_comment;

		// Si le plugin n'a rien retourné
		$var_tpl = (!isset($var_tpl_plugin) && $cobj->type == TYPE_DIR && $plugin_run) ? __('There are no file !').$var_tpl : $var_tpl;

		$tpl->set_var('CONTENT', $var_tpl);

		// Affichage ou non de l'arborescence
		if ($conf['view_tree'] >= 1) {
			$tpl->set_var('TREE_ELEM', get_tree());
			$tpl->parse('Hdlwith_tree', 'with_tree', true);
		} else
			$tpl->parse('Hdlno_tree', 'no_tree', true);

	} else {

		// Ajout du plugin
		if (isset($var_tpl_plugin))
			$var_tpl .= $var_tpl_plugin;

		// Ajout du ou des commentaires
		if (isset($var_tpl_comment))
			$var_tpl .= $var_tpl_comment;

		$tpl->set_var('CONTENT', $var_tpl);

		if ($conf['view_tree'] == 2) {
			$tpl->set_var('TREE_ELEM', get_tree());
			$tpl->parse('Hdlwith_tree', 'with_tree', true);
		} else
			$tpl->parse('Hdlno_tree', 'no_tree', true);
	}

	$tpl->set_var('Hdldir_pagination');

	$var_tpl = $tpl->parse('Hdlobj', 'obj', true);
}


$endtime = system::chrono();
$totaltime = ($endtime - $starttime);


/*	Affichage de la description
 */
if (!empty($cobj->info->description)) {
	$tpl->set_var('DESCRIPTION', $cobj->info->description);
	$tpl->parse('Hdldescription', 'description', true);
}


$tpl->set_var(array(
		'URL_RSS'			=>	url::getRss($cobj->file),
		'URL_RSS_COMMENT'	=>	url::getRss($cobj->file, 'comment'),

		'STYLESHEET'		=>	get_css(),
		'STYLESHEET_PLUGIN'	=>	get_css_plugin(),

		'OBJECT_URL'		=>	format($cobj->file),
		'TOOLBAR'			=>	$var_tpl_toolbar,
		'OBJECT_TITLE'		=>	view_obj(($cobj->type == TYPE_ARCHIVED) ? $cobj->file.'!'.$cobj->target : $cobj->file),
		'CONTENT'			=>	$var_tpl,
		'TITLE'				=>	$title.' '.$conf['title'],
		'HYLA_VERSION'		=>	HYLA_VERSION,
		'DEBUG'				=>	__('Page executed in %s seconds with %s sql query', round($totaltime, 4), $bdd->getNbrQuery()),
		));

/*	Les rss
 */
$tpl->parse('Hdlrss_comment', 'rss_comment', true);
$tpl->parse('Hdlrss_obj', 'rss_obj', true);

$tpl->pparse('OutPut', 'index');

?>
