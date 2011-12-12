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
				'gallery_line'			=>	'Hdlgallery_line'
				));
	}
	
	function aff($paff) {

		global $sort, $start, $conf;
		
		$sort = isset($_SESSION['sess_sort']) ? $_SESSION['sess_sort'] : $conf['sort_config'];

		$tab = $this->obj->getDirContent($this->cobj->file, $sort, $start);
		
		// Listage de répertoire
		$size = sizeof($tab);
		for ($i = 0; $i < $size; $i++) {			

			if ($tab[$i]->name == '..')
				continue;

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
					'FILE_DESCRIPTION'	=>	($tab[$i]->info->description) ? string::cut(eregi_replace("<br />", " ", $tab[$i]->info->description), 90) : __('No description !')));
			
			if ($tab[$i]->info->nbr_comment)
				$this->tpl->parse('Hdlgallery_comment', 'gallery_comment', true);

			if ($tab[$i]->extension == 'jpg' || $tab[$i]->extension == 'jpeg' || $tab[$i]->extension == 'gif' || $tab[$i]->extension == 'png')
				$this->tpl->parse('Hdlgallery_line_img', 'gallery_line_img', true);
			else
				$this->tpl->parse('Hdlgallery_line_other', 'gallery_line_other', true);

			$this->tpl->parse('Hdlgallery_line', 'gallery_line', true);
		}

		return $this->tpl->parse('OutPut', 'zenphoto');
	}
}

?>
