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


class plugins {

	var $info;

	var $cobj;


	function plugins() {

		global $cobj;

		$this->cobj = &$cobj;

		$this->info = array(
			'dir'			=>	'default',
			'name'			=>	'Default',
			'description'	=>	'Plugin par défaut',
			'author'		=>	'hugo',
			'version'		=>	'1'
			);
	}

	/*	Retourne le tableau rempli avec le bon plugin correspondant au type de l'objet
	 */
	function search() {

		$hdl = dir(FOLDER_PLUGINS);
		if ($hdl) {
			while (false !== ($occ = $hdl->read())) {

				// Si on a un fichier caché...
				if ($occ{0} == '.')
					continue;

				// Si ce n'est pas un répertoire
				if (!is_dir(FOLDER_PLUGINS.'/'.$occ))
					continue;

				$this->_getInfo($occ);
			}
		}
		return;
	}

	/*	Charge les infos d'un plugin
		@param	string	$file	L'adresse du fichier info.xml
	 */
	function _getInfo($file) {

		$ret = false;

		$xfile = FOLDER_PLUGINS.$file.'/info.xml';
		if (file_exists($xfile)) {
			$xml =& new XPath($xfile);
			// /plugin[contains(@target,"file")
//			$res = $xml->match('/plugin/*');	//[contains(@ext,"'.$this->cobj->extension.'")]/*');
			$res = $xml->match('/plugin[contains(@ext,"'.$this->cobj->extension.'")]/*');
			if ($res) {
				$this->info = array(
					'dir'			=>	$file,
					'name'			=>	$xml->getData('/plugin/name'),
					'description'	=>	$xml->getData('/plugin/description'),
					'author'		=>	$xml->getData('/plugin/author'),
					'version'		=>	$xml->getData('/plugin/version'));
				$ret = true;
			}
		}

		return $ret;
	}

	/*	Renvoie un tableau contenant les plugins disponibles pour un répertoire
		@access	static
		/!\ Factoriser le code ci dessous avec le reste du code, c'est pas très propre... /!\
	 */
	function getDirPlugins() {

		$tab = array();

		$hdl = dir(FOLDER_PLUGINS);
		if ($hdl) {
			while (false !== ($occ = $hdl->read())) {

				// Si on a un fichier caché...
				if ($occ{0} == '.')
					continue;

				// Si ce n'est pas un répertoire
				if (!is_dir(FOLDER_PLUGINS.'/'.$occ))
					continue;

				$xfile = FOLDER_PLUGINS.$occ.'/info.xml';
				if (file_exists($xfile)) {

					$xml =& new XPath($xfile);
					$res = $xml->match('/plugin[contains(@target,"dir")]/*');
					if ($res) {
						$tab[] = array(
							'dir'			=>	$xfile,
							'name'			=>	$xml->getData('/plugin/name'),
							'description'	=>	$xml->getData('/plugin/description'),
							'author'		=>	$xml->getData('/plugin/author'),
							'version'		=>	$xml->getData('/plugin/version'));
					}
				}
			}
		}

		return $tab;
	}

	/*	Charge le plugin correspondant
		@return	On renvoie le contenu généré par le plugin
	 */
	function load() {

		global $pact, $paff;

		$var_tpl = null;

		if ($this->cobj->info->plugin == 'gallery') {		// TODO: c un peu bizarre ça !
			$this->_getInfo($this->cobj->info->plugin);
		}

		$pfile = FOLDER_PLUGINS.$this->info['dir'].'/index.php';

		if (file_exists($pfile)) {
			include($pfile);

			$pname = 'Plugin_';
			$pname .= $this->info['dir'];

			// Chargement de la classe 
			$p = new $pname();
			if (method_exists($p, 'act'))
				$p->act($pact);
			$var_tpl = $p->aff($paff);
			$p = null;
		}

		return $var_tpl;
	}

}


?>
