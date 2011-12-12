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

class Plugin_swf extends plugin {

	function Plugin_swf() {
		parent::plugin();

		$this->tpl->set_root(DIR_PLUGINS.'swf');
		$this->tpl->set_file('swf', 'swf.tpl');
	}
	
	function aff() {
		$this->tpl->set_var('OBJECT_DOWNLOAD',	url::getCurrentObj('download'));
		return $this->tpl->parse('OutPut', 'swf');
	}
}

?>
