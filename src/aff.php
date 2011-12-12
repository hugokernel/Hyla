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

require 'src/inc/cache.class.php';
require 'src/inc/graphic.class.php';



// Pour les paramètres supplémentaires ( ?aff=mini|500,/ )
//@list($aff, $param) = explode('|', $aff);


/*	Renvoie la directory
 */
function get_directory() {
	global $cobj;
	cache::getFilePath($cobj->file, $file);
	return ($cobj->type == TYPE_ARCHIVE) ? dirname($_SERVER['SCRIPT_FILENAME']).'/'.$file.'/'.$cobj->target : $cobj->realpath;
}


//print_r($cobj);


switch ($aff) {

	#	Login !
	case 'login':
		include('src/login.php');
		break;

	#	Upload
	case 'upload':
		test_right(ADD_FILE);
		include('src/upload.php');
		break;

	#	Affichage de la page de recherche
	case 'search':
		include('src/search.php');
		break;

	#	Affichage des derniers commentaires
	case 'lastcomment':
		$tab = $obj->getLastComment();
		$size = sizeof($tab);
		for ($i = 0; $i < $size; $i++) {
			$tpl->set_var(array(
					'FILE_ICON'	=>	get_icone_from_ext(file::getExtension($tab[$i]->object)),
					'PATH_INFO'	=>	obj::getUrl($tab[$i]->object, AFF_INFO),
					'COMMENT'	=>	$tab[$i]->content,
					'AUTHOR'	=>	$tab[$i]->author,
					'MAIL'		=>	(empty($tab[$i]->mail) ? (empty($tab[$i]->url) ? '#' : $tab[$i]->url) : 'mailto:'.$tab[$i]->mail),
					'URL'		=>	(empty($tab[$i]->mail) ? null : $tab[$i]->url),
					'DATE'		=>	format_date($tab[$i]->date, 1),
					'FOLDER_IMAGES'		=>	FOLDER_IMAGES,));
			$tpl->parse('Hdllast_comment_line', 'last_comment_line', true);
		}

		$tpl->parse('Hdllast_comment', 'last_comment', true);
		$var_tpl .= $tpl->parse('OutPut', 'comment');
		break;

	#	Affichage d'une image en miniature
	case 'mini':

		if ($cobj->type == TYPE_ARCHIVE) {
			$file = null;
			if (!cache::getFilePath($cobj->file, $file)) {
				$zip = new PclZip(FOLDER_ROOT.$cobj->file);
				$out = $zip->extract($file.'/'.basename($cobj->file));
			}
		}

		graphic::image_resize(get_directory(), (!empty($param) ? $param : THUMB_SIZE_X), 0, (CREATE_THUMB) ? $cobj->file : null);
		exit;
		break;

	#	Edition
	case 'edit':
		test_right(EDIT_FILE);
		if ($cobj->type == TYPE_DIR) {
			$tab = plugins::getDirPlugins();

			$tpl->set_var('PLUGIN_DEFAULT_CHECKED', (!$cobj->info->plugin) ? 'value="default" checked="checked"' : 'value="default"');		

			foreach($tab as $occ) {
				$name = strtolower($occ['name']);
				$tpl->set_var('PLUGIN_CHECKED', ($cobj->info->plugin == $name) ? 'value="'.$name.'" checked="checked"' : 'value="'.$name.'"');		
				$tpl->set_var(array(
						'PLUGIN_NAME'			=>	$occ['name'],
						'PLUGIN_DESCRIPTION'	=>	$occ['description']));
				$tpl->parse('Hdlplugin', 'plugin', true);
			}
			$tpl->parse('Hdledit_plugins', 'edit_plugins', true);
		}

		$tpl->set_var(array(
				'DEFAULT_PLUGIN'	=>	DIR_DEFAULT_PLUGIN,
				'OBJECT'			=>	$cobj->file,
				'DESCRIPTION'		=>	(@$cobj->info->description) ? string::unFormat($cobj->info->description) : null));
		$tpl->parse('Hdledit_description', 'edit_description', true);
		$var_tpl .= $tpl->parse('OutPut', 'edit');
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

			#	On "zip" le répertoire et on l'envoie
			case TYPE_DIR:
				$file = null;
				if (!cache::getArchivePath($cobj->file, $file)) {
					$out = archive::createFromDir(dirname($_SERVER['SCRIPT_FILENAME']).'/'.$file.'.zip', $cobj->realpath);
					if ($out) {
						file::sendFile(dirname($_SERVER['SCRIPT_FILENAME']).'/'.$file.'.zip');
						$status = true;
					}
				}
				break;

			#	On extrait le fichier et on l'envoie
			case TYPE_ARCHIVE:
				$file = null;
				if (!cache::getFilePath($cobj->file, $file)) {
					$zip = new PclZip($cobj->realpath);
					$out = $zip->extract($file);
				}

				// Si le fichier n'est pas trouvé dans l'archive : Erreur !
				if (!file_exists(dirname($_SERVER['SCRIPT_FILENAME']).'/'.$file.'/'.$cobj->target)) {
					redirect(traduct('error'), obj::getUrl($cobj->file, AFF_INFO), traduct('errornotfound'));
					exit();
				}

				file::sendFile(dirname($_SERVER['SCRIPT_FILENAME']).'/'.$file.'/'.$cobj->target);
				$status = true;
				break;
		}

		if (ENABLED_CMPT && $status) {
			$obj->addDownload();
		}

		exit;
		break;

	#	Affichage de l'objet
	default:
	case 'info':

		// Chargement d'un plugin...
		$plugins = new plugins();

		if ($cobj->type == TYPE_FILE) {
			if ($cobj->extension)
				$plugins->search();
		} else if ($cobj->type == TYPE_DIR) {
			$plugins->info['name'] = ($cobj->info->plugin) ? $cobj->info->plugin : DIR_DEFAULT_PLUGIN;					// TODO: mettre tout ce code dans la classe plugins
			$plugins->info['dir'] = ($cobj->info->plugin) ? $cobj->info->plugin : strtolower(DIR_DEFAULT_PLUGIN);
		} else if ($cobj->type == TYPE_ARCHIVE) {

//			if (!cache::getArchivePath($cobj->file, $file)) {
			if (!cache::getFilePath($cobj->file, $file)) {
				$zip = new PclZip($cobj->realpath);
				$out = $zip->extract($file);
			}

			// Si le fichier n'est pas trouvé dans l'archive : Erreur !
			if (!file_exists(dirname($_SERVER['SCRIPT_FILENAME']).'/'.$file.'/'.$cobj->target)) {
				redirect(traduct('error'), obj::getUrl($cobj->file, AFF_INFO), traduct('errornotfound'));
				exit();
			}

			// Si on tente d'ouvrir de nouveau une archive, on stop !
			if ($cobj->extension == 'zip') {
				redirect($cobj->file, obj::getUrl($cobj->file, AFF_INFO), traduct('notimplemented'));
				exit;
			}

	/*	Oui, il serait possible de faire du récursif et ainsi pouvoir se balader dans un zip contenu dans un zip ...
		... mais bon, y'a d'autre priorité pour le moment
	 */
/*
	$tab_a = explode('!', $cobj->target);
	if ($tab_a) {

		foreach ($tab_a as $y) {
			$zip = new PclZip(dirname($_SERVER['SCRIPT_FILENAME']).'/'.$file.'/'.$y);
			$file = cache::getFilePath($obj->file.'/'.$y);
			$out = $zip->extract($file);
		}

	}
*/

			$plugins->search();
		}

		$var_tpl .= $plugins->load();

		//	Les commentaires sont uniquement pour les fichiers
		if ($cobj->type == TYPE_FILE) {
			if ($cobj->info->nbr_comment) {
				for ($i = 0; $i < $cobj->info->nbr_comment; $i++) {
					$tpl->set_var(array(
							'COMMENT'	=>	$cobj->info->comment[$i]->content,
							'AUTHOR'	=>	$cobj->info->comment[$i]->author,
							'MAIL'		=>	(empty($cobj->info->comment[$i]->mail) ? (empty($cobj->info->comment[$i]->url) ? '#' : $cobj->info->comment[$i]->url) : 'mailto:'.$cobj->info->comment[$i]->mail),
							'URL'		=>	(empty($cobj->info->comment[$i]->mail) ? null : $cobj->info->comment[$i]->url),
							'DATE'		=>	format_date($cobj->info->comment[$i]->date, 1)));
					$tpl->parse('Hdlcomment_line', 'comment_line', true);
				}

				// Pour éviter que ça apparaisse dans les champs du formulaire d'envoie
				$tpl->set_var(array(
						'COMMENT'	=>	null,
						'AUTHOR'	=>	null,
						'MAIL'		=>	null,
						'URL'		=>	null,
						'DATE'		=>	null));
			}

			$tpl->parse('Hdlcurrent_comment', 'current_comment', true);

			$tpl->set_var(array(
					'AUTHOR'		=>	string::format(@$_POST['cm_author'], false),
					'MAIL'			=>	string::format(@$_POST['cm_mail'], false),
					'SITE'			=>	(string::format(@$_POST['cm_site'] ? $_POST['cm_site'] : 'http://')),
					'CONTENT'		=>	string::format(@$_POST['cm_content']),
					'ERROR'			=>	view_error($msg_error),
					'OBJECT'		=>	$cobj->file,
					'COMMENT_NBR'	=>	$cobj->info->nbr_comment));
			$var_tpl .= $tpl->parse('OutPut', 'comment');
		}

		break;
}

$tpl->set_var(array(
		'DOWNLOAD_COUNT'	=>	$cobj->info->dcount ? $cobj->info->dcount.traduct('downloadcount') : null,
		'FOLDER_IMAGES'		=>	FOLDER_IMAGES,
		'DESCRIPTION'		=>	($aff != 'lastcomment') ? ((@$cobj->info->description) ? $cobj->info->description : traduct('nodescription')) : traduct('lastcomment'),
		'FILE_ICON'			=>	$cobj->icon,
		'OBJECT'			=>	$cobj->file,
		'PATH'				=>	$cobj->path,
		'OBJECT_URL'		=>	format($cobj->file),
		));


// La pagination, c'est uniquement pour les fichiers et fichiers archivés pour le moment !
if ($cobj->type != TYPE_DIR) {

	if ($cobj->prev)
		$tpl->parse('Hdlprevious_page', 'previous_page', true);

	if ($cobj->next)
		$tpl->parse('Hdlnext_page', 'next_page', true);

	$tpl->set_var(array(
			'OBJ_PREV'			=>	($cobj->type == TYPE_ARCHIVE) ? $cobj->prev->target : $cobj->prev->name,
			'OBJ_NEXT'			=>	($cobj->type == TYPE_ARCHIVE) ? $cobj->next->target : $cobj->next->name,
			'PREV_PATH'			=>	($cobj->type == TYPE_ARCHIVE) ? $cobj->prev->file.'!'.$cobj->prev->target : $cobj->prev->file,
			'NEXT_PATH'			=>	($cobj->type == TYPE_ARCHIVE) ? $cobj->next->file.'!'.$cobj->next->target : $cobj->next->file,
			'PREV_FILE_ICON'	=>	$cobj->prev->icon,
			'NEXT_FILE_ICON'	=>	$cobj->next->icon));
	$tpl->parse('Hdlpagination', 'pagination', true);
}

$tpl->set_var('CONTENT', $var_tpl);
$var_tpl = $tpl->parse('Hdlobj', 'obj', true);

?>
