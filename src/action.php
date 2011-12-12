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
	WITHOUT ANY WARRANTY; without even the implied warcranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Hyla; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

if (!defined('PAGE_HOME'))
	header('location: ../index.php');

$tpl->set_file('action', 'action.tpl');

$tpl->set_block('action', array(
		'dir_copy_occ'	=>	'Hdldir_copy_occ',
		'copy'			=>	'Hdlcopy',
		'dir_move_occ'	=>	'Hdldir_move_occ',
		'move'			=>	'Hdlmove',
		'mkdir'		=>	'Hdlmkdir',
		'rename'		=>	'Hdlrename',
		));


switch ($aff) {

	case 'mkdir':
		$tpl->set_var(array(
				'FORM_MKDIR'	=>	url::getCurrentObj('', 'mkdir'),
				'ERROR'			=>	(isset($msg_error)) ? view_error($msg_error) : null,
				));
		$tpl->parse('Hdlmkdir', 'mkdir', true);
		break;

	case 'move':

		if ($cobj->type == TYPE_ARCHIVE) {
			redirect(__('Error'), url::getCurrentObj(), __('Not implemented !'));
			system::end();
		}

		if ($cobj->file == '/') {
			redirect(__('Error'), url::getCurrentObj(), __('Impossible to move the root'));
			system::end();
		}

		$tpl->set_var('FORM_MOVE', url::getCurrentObj('', 'move'));

	case 'copy':
		$tab = file::scanDir(FOLDER_ROOT, $conf['view_hidden_file']);

		// La racine
		if ($cobj->path != '/' && file::downPath($cobj->file) != '/') {
			$tpl->set_var('DIR_NAME', '/');
			$tpl->parse('Hdldir_'.$aff.'_occ', 'dir_'.$aff.'_occ', true);
		}

		// Les autres répertoires
		foreach ($tab as $occ) {
			if ($occ == $cobj->path || ($cobj->type == TYPE_DIR && $occ == file::downPath($cobj->path) || ($occ{1} == '.' && !$conf['view_hidden_file'])))
				continue;
			$tpl->set_var('DIR_NAME', $occ);
			$tpl->parse('Hdldir_'.$aff.'_occ', 'dir_'.$aff.'_occ', true);
		}
		unset($tab);
		$tpl->set_var('ERROR', (isset($msg_error)) ? view_error($msg_error) : null);
		$tpl->parse('Hdl'.$aff, $aff, true);
		break;

	case 'rename':

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

		$tpl->set_var(array(
				'FORM_RENAME'	=>	url::getCurrentObj('', 'rename'),
				'ERROR'			=>	(isset($msg_error)) ? view_error($msg_error) : null,
				));
		$tpl->parse('Hdlrename', 'rename', true);
		break;
}

$tpl->set_var(array(
		'OBJECT'	=>	$cobj->file,
		));

$msg_error = null;

$var_tpl = $tpl->parse('OutPut', 'action');

?>
