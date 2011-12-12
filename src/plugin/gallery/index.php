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


class Plugin_gallery extends plugin { // implements _plugin {

	function Plugin_gallery() {
		parent::plugin();

		$this->tpl->set_root(FOLDER_PLUGINS.'gallery');
		$this->tpl->set_file(array(
				'gallery'	=>	'gallery.tpl'));
		
		$this->tpl->set_block('gallery', array(
				'gallery_comment'		=>	'Hdlgallery_comment',
				'gallery_line_img'		=>	'Hdlgallery_line_img',
				'gallery_line_other'	=>	'Hdlgallery_line_other',
				'gallery_line'			=>	'Hdlgallery_line',
				'gal'					=>	'Hdlgal'));

	}
	
	function act() {
	}
	
	function aff($paff) {

		global $sort, $start;
		
		$sort = (@$_SESSION['sort']) ? $_SESSION['sort'] : (SORT_ALPHA | SORT_FOLDER_FIRST);

		$tab = $this->obj->getDirContent($this->cobj->file, $sort, $start);
		
		// Listage de répertoire
		$size = sizeof($tab);
		for($i = 0, $cmpt = 1; $i < $size; $i++, $cmpt++) {			
//			$ext = file::getExtension($tab[$i]['name']);
		
			if ($tab[$i]->name == '..') {
				$cmpt--;
				continue;
			}

			$this->tpl->set_var('Hdlgallery_line_img');
			$this->tpl->set_var('Hdlgallery_line_other');
			$this->tpl->set_var('Hdlgallery_comment');
			
			$this->tpl->set_var('ACTION',	(is_file(FOLDER_ROOT.'/'.$tab[$i]->path)) ? 'dl' : 'list');
			$this->tpl->set_var(array(
					'FILE_ICON'			=>	$tab[$i]->icon,
					'FILE_NAME'			=>	$tab[$i]->name,
					'FILE_SIZE'			=>	get_intelli_size($tab[$i]->size),
					'PATH'				=>	obj::getUrl($tab[$i]->file, AFF_INFO),
					'OBJECT_MINI'		=>	obj::getUrl($tab[$i]->file, AFF_MINI, THUMB_SIZE_X / 1.2),
					'NBR_COMMENT'		=>	$tab[$i]->info->nbr_comment,
					'FILE_DESCRIPTION'	=>	($tab[$i]->info->description) ? string::cut(eregi_replace("<br />", " ", $tab[$i]->info->description), 90) : traduct('nodescription')));
			
			if ($tab[$i]->info->nbr_comment) {
				$this->tpl->parse('Hdlgallery_comment', 'gallery_comment', true);
			}


			if ($tab[$i]->extension == 'jpg' || $tab[$i]->extension == 'jpeg' || $tab[$i]->extension == 'gif' || $tab[$i]->extension == 'png')
				$this->tpl->parse('Hdlgallery_line_img', 'gallery_line_img', true);
			else
				$this->tpl->parse('Hdlgallery_line_other', 'gallery_line_other', true);

			$this->tpl->parse('Hdlgallery_line', 'gallery_line', true);


			if (!($cmpt % 4)) {
				$this->tpl->parse('Hdlgal', 'gal', true);
				$this->tpl->set_var('Hdlgallery_line');
			}
				}

		$this->tpl->parse('Hdlgal', 'gal', true);

		return $this->tpl->parse('OutPut', 'gallery');
	}
	
	function __destruct() {
	}
}

?>
