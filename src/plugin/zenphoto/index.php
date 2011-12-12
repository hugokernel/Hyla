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


class Plugin_zenphoto extends plugin { // implements _plugin {

	function Plugin_zenphoto() {
		parent::plugin();

		$this->tpl->set_root(DIR_PLUGINS.'zenphoto');
		$this->tpl->set_file('zenphoto', 'zenphoto.tpl');
		
		$this->tpl->set_block('zenphoto', array(
//				'gallery_comment'		=>	'Hdlgallery_comment',
				'gallery_line_img'		=>	'Hdlgallery_line_img',
				'gallery_line_other'	=>	'Hdlgallery_line_other',
				'gallery_line'			=>	'Hdlgallery_line',
				'gallery_line_cat'		=>	'Hdlgallery_line_cat',
				'gallery'				=>	'Hdlgallery',
				));
	}
	
	function aff($paff) {

		global $sort, $start, $conf;
		
		$sort = $_SESSION['sess_sort'];
		$grp = $_SESSION['sess_grp'];

		switch ($sort) {
			case SORT_DEFAULT:
			case SORT_ALPHA:
			case SORT_ALPHA | SORT_FOLDER_FIRST:
				$header_value = 'return $tab[$i]->name{0};';
				break;				
			case SORT_ALPHA_R:
			case SORT_ALPHA_R | SORT_FOLDER_FIRST:
				$header_value = 'return $tab[$i]->name{0};';
				break;
			case SORT_ALPHA_EXT:
			case SORT_ALPHA_EXT | SORT_FOLDER_FIRST:
				$header_value = 'return $tab[$i]->extension;';
				break;
			case SORT_ALPHA_EXT_R:
			case SORT_ALPHA_EXT_R | SORT_FOLDER_FIRST:
				$header_value = 'return $tab[$i]->extension;';
				break;
			case SORT_ALPHA_CAT:
			case SORT_ALPHA_CAT | SORT_FOLDER_FIRST:
				$header_value = 'return $tab[$i]->cat;';
				break;
			case SORT_ALPHA_CAT_R:
			case SORT_ALPHA_CAT_R | SORT_FOLDER_FIRST:
				$header_value = 'return $tab[$i]->cat;';
				break;
			case SORT_SIZE:
			case SORT_SIZE | SORT_FOLDER_FIRST:
				$header_value = 'return get_human_size_reading($tab[$i]->size, 0);';
				break;
			case SORT_SIZE_R:
			case SORT_SIZE_R | SORT_FOLDER_FIRST:
				$header_value = 'return get_human_size_reading($tab[$i]->size, 0);';
				break;
		}


		$tab = $this->obj->getDirContent($this->cobj->file, $sort, $start);
		
		if ($tab) {

			// Listage de répertoire
			$size = sizeof($tab);
			for ($i = 0, $last = null, $last_type = null; $i < $size; $i++) {			

				if ($tab[$i]->name == '..')
					continue;

				$this->tpl->set_var('Hdlgallery_line');
				$this->tpl->set_var('Hdlgallery_line_cat');

				$this->tpl->set_var('Hdlgallery_line_img');
				$this->tpl->set_var('Hdlgallery_line_other');
				$this->tpl->set_var('Hdlgallery_comment');
			
				$this->tpl->set_var('ACTION',	(is_file(FOLDER_ROOT.'/'.$tab[$i]->path)) ? 'dl' : 'list');

				$this->tpl->set_var(array(
						'FILE_ICON'			=>	$tab[$i]->icon,
						'FILE_NAME'			=>	$tab[$i]->name,
						'FILE_SIZE'			=>	get_human_size_reading($tab[$i]->size),
						'PATH'				=>	url::getObj($tab[$i]->file),
						'OBJECT_MINI'		=>	url::getObj($tab[$i]->file, array('mini', '220')),
						'NBR_COMMENT'		=>	$tab[$i]->info->nbr_comment,
						'FILE_DESCRIPTION'	=>	($tab[$i]->info->description) ? string::cut(string::unformat($tab[$i]->info->description), 90) : __('No description !')));
			
				if ($tab[$i]->info->nbr_comment)
					$this->tpl->parse('Hdlgallery_comment', 'gallery_comment', true);

				if ($tab[$i]->extension == 'jpg' || $tab[$i]->extension == 'jpeg' || $tab[$i]->extension == 'gif' || $tab[$i]->extension == 'png')
					$this->tpl->parse('Hdlgallery_line_img', 'gallery_line_img', true);
				else
					$this->tpl->parse('Hdlgallery_line_other', 'gallery_line_other', true);

				// Utilisé pour le groupage par catégorie
				if ($grp == 1) {
					$rupt = eval($header_value);
					if ($sort & SORT_ALPHA_CAT || $sort & SORT_ALPHA_CAT_R)
						$this->tpl->set_var('HEADER_INFO_VALUE', (($tab[$i]->type == TYPE_FILE) ? $rupt : __('Dir(s) ')));
					else {
						$this->tpl->set_var(array(
								'HEADER_VALUE'		=>	(($tab[$i]->type == TYPE_DIR) ? __('Dir(s) ') : __('File(s) ')),
								'HEADER_INFO_VALUE'	=>	(($tab[$i]->type == TYPE_FILE) ? $rupt : null)));
					}
					$bool = (($tab[$i]->type == 1) && ((SORT_FOLDER_FIRST | $sort) && $last_type == $tab[$i]->type));
					if (!$bool && ($last_type != $tab[$i]->type || strtolower($last) != strtolower($rupt))) {
						$this->tpl->parse('Hdlgallery_line_cat', 'gallery_line_cat', true);
						$last = $rupt;
						$last_type = $tab[$i]->type;
					}
				}

				$this->tpl->parse('Hdlgallery_line', 'gallery_line', true);
				$this->tpl->parse('Hdlgallery', 'gallery', true);
			}

			return $this->tpl->parse('OutPut', 'zenphoto');
		}
	}
}

?>
