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

require 'src/lib/template.inc';

class tpl {

	var	$_tpl;

	/*	Le constructeur
	 */
	function tpl($root) {
		$this->_tpl = new Template();
		$this->_tpl->set_var('DIR_TEMPLATE', ROOT_URL.'/'.$root);
		return $this->_tpl->set_root($root);
	}

	/*	Spécifie le chemin des tpl par défaut
	 */
	function setRoot($root) {
		return $this->_tpl->set_root($root);
	}

	/*	Spécifie les fichiers
	 */
	function setFile($varname, $filename = '') {
		return $this->_tpl->set_file($varname, $filename);
	}

	/*	Les blocs
	 */
	function setBlock($parent, $varname, $name = '') {
		if (is_array($varname)) {
			foreach ($varname as $varname => $name)
				$this->_tpl->set_block($parent, $varname, $name);
		} else
			return $this->_tpl->set_block($parent, $varname, $name);
	}

	/*	"Radio block"
	 */
	function setRadioBlock() {
		
	}



	function setVar($varname, $value = '', $append = false) {
		return $this->_tpl->set_var($varname, $value, $append);
	}

	function parse($target, $varname, $append = false) {
		return $this->_tpl->parse($target, $varname, $append);
	}

	function pparse($target, $varname, $append = false) {
		return $this->_tpl->pparse($target, $varname, $append);
	}
}

?>
