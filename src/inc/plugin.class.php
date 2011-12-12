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

/*
interface _plugin {
   public function act($mact);
   public function aff();
}
*/

class plugin
{
	var $tpl;
	var $lst;
	var $obj;
	var $cobj;

	var $plugin_name;
	
	function plugin() {

		global $tpl;
		global $lst;
		global $obj;
		global $cobj;
	
		$this->tpl = $tpl;
		
		$this->lst = $lst;
		
		$this->obj = &$obj;
		$this->cobj = $cobj;

		$this->plugin_name = null;
	}

	/*	Sauve une variable dans la session courante
	 */
	function saveVar($name, $value) {
		return $_SESSION['sess_'.$this->plugin_name.'_'.$name] = $value;
	}

	/*	Récupère une variable de la session courante
	 */
	function getVar($name) {
		return $_SESSION['sess_'.$this->plugin_name.'_'.$name];
	}

}

?>
