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

class Plugin_flv extends plugin {

	function Plugin_flv() {
		parent::plugin();

		$this->tpl->set_root(FOLDER_PLUGINS.'flv');
		$this->tpl->set_file(array(
				'flv'	 	=>	'flv.tpl'));
	}
	
	function aff() {

		if (!cache::getFilePath($this->cobj->file, $out)) {
			copy($this->cobj->realpath, dirname($_SERVER['SCRIPT_FILENAME']).'/'.$out);
		}

		$this->tpl->set_var(array(
				'OBJECT_URL'		=>	ROOT_URL.'/'.$out,
				'OBJECT_DOWNLOAD'	=>	obj::getCurrentUrl(AFF_DOWNLOAD)));
		
		return $this->tpl->parse('OutPut', 'flv');
	}
}

?>
