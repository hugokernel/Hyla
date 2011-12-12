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

class Plugin_dir extends plugin {

	function Plugin_dir() {
		parent::plugin();
		$this->tpl->set_root(FOLDER_PLUGINS.'dir');
		$this->tpl->set_file(array(
				'dir'	=>	'dir.tpl'));
		$this->tpl->set_block('dir', array(
				'line_comment'	=>	'Hdlline_comment',
				'line_header'	=>	'Hdlline_header',
				'line_content'	=>	'Hdlline_content',
				'line'			=>	'Hdlline',));
	}
	
	function act() {
	}
	
	function aff($aff) {

		global $sort, $start;

		$sort = (isset($_SESSION['sort'])) ? $_SESSION['sort'] : SORT_CONFIG;
		$grp = (isset($_SESSION['grp'])) ? $_SESSION['grp'] : GROUP_BY_SORT;

		if (@$aff) {
			$tab = array(
					'0' => SORT_DEFAULT,
					'1' => SORT_ALPHA | SORT_FOLDER_FIRST,
					'2' => SORT_ALPHA_R | SORT_FOLDER_FIRST,
					'3' => SORT_ALPHA_EXT | SORT_FOLDER_FIRST,
					'4' => SORT_ALPHA_EXT_R | SORT_FOLDER_FIRST,
					'5' => SORT_ALPHA,
					'6' => SORT_ALPHA_R,
					'7' => SORT_ALPHA_EXT,
					'8' => SORT_ALPHA_EXT_R);
			list($act, $value) = explode(':', $aff[0]);
			if ($act == 'sort') {
				if ($value > 0) {
					$sort = (isset($tab[$value]) ? $tab[$value] : $sort);
					$_SESSION['sort'] = $sort;
				}
			}

			if (isset($aff[1])) {
				list($act, $value) = explode(':', $aff[1]);
				if ($act == 'grp' && $value == 'ok') {
					$grp = 1;
					$_SESSION['grp'] = $grp;
				}
			} else {
				$grp = 0;
				$_SESSION['grp'] = $grp;
			}
		}

		switch ($sort) {
			case SORT_DEFAULT:
			case SORT_ALPHA | SORT_FOLDER_FIRST:
			case SORT_ALPHA_R | SORT_FOLDER_FIRST:
			case SORT_ALPHA:
			case SORT_ALPHA_R:
				$header_value = 'return $tab[$i]->name{0};';
				break;
			case SORT_ALPHA_EXT | SORT_FOLDER_FIRST:
			case SORT_ALPHA_EXT_R | SORT_FOLDER_FIRST:
			case SORT_ALPHA_EXT:
			case SORT_ALPHA_EXT_R:
				$header_value = 'return $tab[$i]->extension;';
				break;
		}

		if ($grp == 1) {
			$this->tpl->set_var('CHECKED', ' checked="checked"');
		}

		$tab = $this->obj->getDirContent($this->cobj->file, $sort, $start);

		// Listage de répertoire
		$size = sizeof($tab);
		for($i = 0, $last = null, $last_type = null; $i < $size; $i++) {
			$this->tpl->set_var('Hdlline_header');
			$this->tpl->set_var('Hdlline_content');
			$this->tpl->set_var('Hdlline_comment');

			$this->tpl->set_var(array(
					'OBJECT'			=>	obj::getUrl($this->cobj->file, AFF_INFO),
					'FILE_ICON'			=>	$tab[$i]->icon,
					'FILE_NAME'			=>	$tab[$i]->name,
					'FILE_SIZE'			=>	($tab[$i]->type == TYPE_FILE) ? get_intelli_size($tab[$i]->size) : '&nbsp;',
					'PATH_DOWNLOAD'		=>	obj::getUrl($tab[$i]->file, AFF_DOWNLOAD),
					'PATH_INFO'			=>	obj::getUrl($tab[$i]->file, AFF_INFO),
					'FOLDER_IMAGES'		=>	FOLDER_IMAGES,
					'FILE_DESCRIPTION'	=>	string::cut(eregi_replace("<br />", " ", $tab[$i]->info->description), 90),
					'NBR_COMMENT'		=>	$tab[$i]->info->nbr_comment));

			if ($tab[$i]->info->nbr_comment) {
				$this->tpl->parse('Hdlline_comment', 'line_comment', true);
			}

			if ($grp == 1) {
				$rupt = eval($header_value);
				$this->tpl->set_var(array(
						'HEADER_VALUE'		=>	(($tab[$i]->type == TYPE_DIR) ? 'Répertoire(s)' : 'Fichier(s) '),
						'HEADER_INFO_VALUE'	=>	(($tab[$i]->type == TYPE_FILE) ? $rupt : null)));
				$bool = (($tab[$i]->type == 1) && ((SORT_FOLDER_FIRST | $sort) && $last_type == $tab[$i]->type));
				if (!$bool && ($last_type != $tab[$i]->type || strtolower($last) != strtolower($rupt))) {
					$this->tpl->parse('Hdlline_header', 'line_header', true);	
					$last = $rupt;
					$last_type = $tab[$i]->type;
				}
			}
			$this->tpl->parse('Hdlline_content', 'line_content', true);
			$this->tpl->parse('Hdlline', 'line', true);
		}

		return $this->tpl->parse('OutPut', 'dir');
	}
	
	function __destruct() {
	}
}

?>
