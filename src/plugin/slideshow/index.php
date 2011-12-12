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

class Plugin_slideshow extends plugin {

	function Plugin_slideshow() {
		parent::plugin();

		$this->tpl->set_root(DIR_PLUGINS.'slideshow');
		$this->tpl->set_file('slideshow', 'slideshow.tpl');

		$this->tpl->set_block('slideshow', array(
				'image_cache'		=>	'Hdlimage_cache',
				'image_thumb'		=>	'Hdlimage_thumb',
				'mode_manual'		=>	'Hdlmode_manual',
				'previous_slide'	=>	'Hdlprevious_slide',
				'next_slide'		=>	'Hdlnext_slide',
				'header_mode_auto'	=>	'Hdlheader_mode_auto',
				'mode_auto'			=>	'Hdlmode_auto',
				));
	}
	
	function aff() {

	}

	function fullscreen($paff) {

		global $obj, $curl, $conf;

		$start = url::getAff(2);
		if (!$start)
			$start = 0;

		$tab = $this->obj->getDirContent($this->cobj->file, $_SESSION['sess_sort'], $start, 3);

		// Listage de répertoire
		$size = sizeof($tab);
		for($i = 1, $last = null, $last_type = null; $i < $size; $i++) {
			$this->tpl->set_var(array(
					'IMAGE_CACHE'		=>	url::getObj($tab[$i]->file, array('mini', 800)),
					));
			$this->tpl->parse('Hdlimage_cache', 'image_cache', true);
		}

		if ($paff) {
			list($act, $val) = explode(':', $paff);
			if ($act == 'mode')
				$mode = $val;
			$this->saveVar('mode', $val);
		} else
			$mode = $this->getVar('mode');

		if ($mode == 'auto')
			$this->tpl->parse('Hdlmode_auto', 'mode_auto', true);
		else
			$this->tpl->parse('Hdlmode_manual', 'mode_manual', true);

		if ($tab[0]->extension == 'jpg' || $tab[0]->extension == 'jpeg' || $tab[0]->extension == 'gif' || $tab[0]->extension == 'png') {
			$this->tpl->set_var('IMAGE', url::getObj($tab[0]->file, array('mini', 800), array('force', 'slideshow')));
			$this->tpl->parse('Hdlimage_thumb', 'image_thumb', true);
		}

		$this->tpl->set_var(array(
				'PREV_IMAGE'	=>	url::getCurrentObj(array('start', ($start - 1)), array('force', 'slideshow')),

				'FILE_ICON'		=>	$tab[0]->icon,
				'NAME'			=>	$tab[0]->name,

				'DESCRIPTION'	=>	($tab[0]->info->description) ? string::cut(eregi_replace("<br />", " ", $tab[0]->info->description), 90) : __('No description !'),

				'NEXT_IMAGE'	=>	url::getCurrentObj(array('start', ($start + 1)), array('force', 'slideshow')),

				'URL_STOP'		=>	url::getCurrentObj(),
				'URL_AUTO'		=>	url::getCurrentObj(array('start', $start), array('force', 'slideshow'), null, 'mode:auto'),
				'URL_MANUAL'	=>	url::getCurrentObj(array('start', $start), array('force', 'slideshow'), null, 'mode:manual'),
				));

		$nbr_obj = $obj->getNbrObject();

		if ($start > 0)
			$this->tpl->parse('Hdlprevious_slide', 'previous_slide', true);

		if ($start + 1 < $nbr_obj) {
			$this->tpl->parse('Hdlnext_slide', 'next_slide', true);
			if ($mode == 'auto')
				$this->tpl->parse('Hdlheader_mode_auto', 'header_mode_auto', true);
		}

		return $this->tpl->parse('OutPut', 'slideshow');		
	}
}

?>
