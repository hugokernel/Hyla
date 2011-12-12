<?php
/*
	This file is part of Hyla
	Copyright (c) 2004-2007 Charles Rincheval.
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

class plugin_obj
{
	var $tpl;
	var $obj;
	var $url;
	var $cobj;

	var $plugin_name;

	var $plugin_dir;

	var $_conf;

	var $_url_2_plugin;		// L'url pour accéder au dossier du plugin courant

	var $_real_file;		// Le chemin d'accès à l'objet courant, très utile car, il va chercher dans le cache (ex: fichier contenus dans zip, tar...)
	
	function plugin_obj($cobj) {

		global $tpl, $obj, $url, $cuser;
	
		$this->tpl = $tpl;
		
		$this->obj = &$obj;
		$this->cobj = $cobj;

		$this->url = &$url;

		$name_obj = get_class($this);

		$this->plugin_name = substr($name_obj, strlen('plugin_obj_'), strlen($name_obj));

		$this->plugin_dir = DIR_PLUGINS_OBJ;

		$this->_url_2_plugin = null;

		$this->_conf = array();

		$this->_real_file = get_real_directory();
	}

	/*	Lit le fichier de conf des plugins
	 */
	function readConf() {
		$this->_conf = (function_exists('parse_ini_file')) ? parse_ini_file(FILE_PLUGINS, true) : iniFile::read(FILE_PLUGINS, true);
	}

	/*	Lit une variable de configuration
		@param	string	$name	Nom de la variable
		@param	string	$def	Valeur par défaut si la variable n'existe pas
	 */
	function getConfVar($name, $def = null) {
		$ret = null;
		if (!$this->_conf)
			$this->readConf();
		if (array_key_exists($this->plugin_name, $this->_conf)) {
			if (array_key_exists($name, $this->_conf[$this->plugin_name])) {
				$ret = $this->_conf[$this->plugin_name][$name];
			}
		}

		return $ret ? $ret : $def;
	}

	/*	Sauve une variable dans la session courante
	 */
	function saveVar($name, $value) {
		return $_SESSION['sess_'.$this->plugin_name.'_'.$name] = $value;
	}

	/*	Récupère une variable de la session courante
	 */
	function getVar($name) {
		$key = 'sess_'.$this->plugin_name.'_'.$name;
		if (isset($_SESSION) && array_key_exists($key, $_SESSION))
			return $_SESSION[$key];
	}

	/*	Ajoute la css d'un plugin
		@param	string	$name	La css, à partir du chemin du plugin
	 */
	function addStyleSheet($name) {
		add_stylesheet_plugin($this->plugin_dir.$this->plugin_name.'/'.$name, $this->plugin_name);
	}
}

?>
