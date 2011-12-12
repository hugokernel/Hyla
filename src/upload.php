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
	WITHOUT ANY WARRANTY; without even the implied warcranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with iFile; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

if (!defined('PAGE_HOME'))
	header('location: ../index.php');

$tpl->set_file('upload', 'upload.tpl');

$tpl->set_block('upload', array(
		'from_url'		=>	'Hdlfrom_url',
		'form_upload'	=>	'Hdlform_upload'));

if (ini_get('allow_url_fopen'))
	$tpl->parse('Hdlfrom_url', 'from_url', true);

// Un envoie à été fait...
if (isset($_POST['Submit'])) {

	$ierror = 0;
	$msg_error = null;

	$size = sizeof($_FILES['ul_file_local']['name']);
	for ($i = 0; $i < $size; $i++) {

		if ($_POST['ul_file_method'][$i] == 'local' && empty($_FILES['ul_file_local']['name'][$i]))
			continue;

		if ($_POST['ul_file_method'][$i] == 'local') {

			$up_name = $_FILES['ul_file_local']['name'][$i];
			$destinationname = FOLDER_ROOT.$cobj->path.'/'.$up_name;

			switch ($_FILES['ul_file_local']['error'][$i]) {
				case UPLOAD_ERR_FORM_SIZE:
				case UPLOAD_ERR_INI_SIZE:		$msg_error = traduct('errorfilesizetoobig');	break;
				case UPLOAD_ERR_PARTIAL:
				case UPLOAD_ERR_NO_FILE:		$msg_error = traduct('erroruploadfile');		break;
				case UPLOAD_ERR_OK:

					// Si le fichier existe déjà...
					if (file_exists($destinationname)) {
						$msg_error = traduct('errorfilealreadyexists');
						break;
					}

					// On déplace le fichier ou il faut, on met la description si elle existe et on quitte !
					if (is_writable(FOLDER_ROOT.$cobj->path)) {
	
						if (move_uploaded_file($_FILES['ul_file_local']['tmp_name'][$i], $destinationname)) {

							// Mouais, pas convaincu !
							if (system::getOS() == 'Linux') {
								chmod($destinationname, FILE_CHMOD);
							}
					
							if (!empty($_POST['ul_description'][$i])) {
								$obj->setDescription($cobj->file.$up_name, string::format($_POST['ul_description'][$i]));
							}

							$tab_ok[$i]['name'] = $up_name;
						}

					} else {
						$msg_error = traduct('notwritable');
						break;
					}

					// On teste enfin si le fichier à bien été créé
					if (!file_exists($destinationname)) {
						$msg_error = traduct('uploaderrorunknow');
					}
			}

		} else {

			if (!ini_get('allow_url_fopen')) {
				redirect($cobj->file, obj::getCurrentUrl(AFF_UPLOAD), traduct('errorallowurlfopen'));
				exit;
			} else {

				$up_name = basename($_POST['ul_file_fromurl'][$i]);
				$destinationname = FOLDER_ROOT.$cobj->path.'/'.$up_name;

				// Si le fichier existe déjà...
				if (file_exists(FOLDER_ROOT.$cobj->path.'/'.$up_name)) {
					$msg_error = traduct('errorfilealreadyexists');
					break;
				} else if ($var = file::getContent($_POST['ul_file_fromurl'][$i])) {

					file::putContent($destinationname, $var);

					// Mouais, pas convaincu !
					if (system::getOS() == 'Linux') {
						chmod($destinationname, FILE_CHMOD);
					}

					if (!empty($_POST['ul_description'][$i])) {
						$obj->setDescription($cobj->file.$up_name, string::format($_POST['ul_description'][$i]));
					}

					$tab_ok[$i]['name'] = $up_name;
				} else
					$msg_error = traduct('uploaderrorunknow');
			}
		}

		if ($msg_error) {
			$tab_error[$ierror]['error'] = $msg_error;
			$tab_error[$ierror]['name'] = $up_name;
			$tab_error[$ierror]['description'] = $_POST['ul_description'][$i];
			$msg_error = null;
			$ierror++;
		}
	}

	$rapport = null;
	if (@$tab_ok) {
		foreach ($tab_ok as $occ) {
			$rapport .= $occ['name'].' ';
		}
	}

	// Pas d'erreur, tout niquel, on redirige !
	if (@!$tab_error && @$tab_ok) {
		redirect($cobj->file, obj::getCurrentUrl(), traduct('uploadok').view_status(traduct('uploadfileok').$rapport));
		exit;
	}
}


$nbr_form = (!@$_GET['file']) ? ((isset($_POST['Submit']) && @$tab_error) ? sizeof($tab_error) : 1) : $_GET['file'];
for ($i = 0; $i < $nbr_form; $i++) {
	$tpl->set_var(array(
			'STATUS'			=>	(@$rapport) ? view_status(traduct('uploadfileok').$rapport) : null,
			'ERROR'				=>	(@$tab_error) ? view_error($tab_error[$i]['name'].' : '.$tab_error[$i]['error']) : null,
			'NUM'				=>	$i,
			'FILE_DESCRIPTION'	=>	(@$tab_error[$i]['error']) ? $tab_error[$i]['description'] : null));
	$tpl->parse('Hdlform_upload', 'form_upload', true);
}

$tpl->set_var(array(
		'OBJECT'		=>	$cobj->file,
		'MAX_FILESIZE'	=>	ini_get('upload_max_filesize')));

$var_tpl = $tpl->parse('OutPut', 'upload');

?>
