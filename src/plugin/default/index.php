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

class Plugin_default extends plugin {	// implements _plugin { plugin implements _plugin {

	function Plugin_default() {
		parent::plugin();
		$this->tpl->set_root(FOLDER_PLUGINS.'default');

		$this->tpl->set_file(array(
				'default'	 	=>	'default.tpl'));
	}
	
	function aff() {
		return $this->tpl->parse('OutPut', 'default');
	}
}

?>
