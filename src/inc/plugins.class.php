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

		// Si y'a pas d'extension, on cherche pas !
		if (!empty($this->cobj->extension)) {
			$hdl = dir(DIR_PLUGINS);
			if ($hdl) {
				while (false !== ($occ = $hdl->read())) {

					// Si on a un fichier caché...
					if ($occ{0} == '.')
						continue;

					// Si ce n'est pas un répertoire
					if (!is_dir(DIR_PLUGINS.'/'.$occ))
						continue;

					$this->_getInfo($occ);
				}
			}
		}
		return;
	}

	/*	Charge les infos d'un plugin
		@param	string	$file	L'adresse du fichier info.xml
	 */
	function _getInfo($file) {

		$ret = false;

		$xfile = DIR_PLUGINS.$file.'/info.xml';
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

		$hdl = dir(DIR_PLUGINS);
		if ($hdl) {
			while (false !== ($occ = $hdl->read())) {

				// Si on a un fichier caché...
				if ($occ{0} == '.')
					continue;

				// Si ce n'est pas un répertoire
				if (!is_dir(DIR_PLUGINS.'/'.$occ))
					continue;

				$xfile = DIR_PLUGINS.$occ.'/info.xml';
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

		global $curl, $conf, $l10n;

		$var_tpl = null;

		$pfile = DIR_PLUGINS.$this->info['dir'].'/index.php';

		if (file_exists($pfile)) {
			include($pfile);

			$pname = 'Plugin_';
			$pname .= $this->info['dir'];

			// Y'a t-il un fichier de langue ?
			$l10n_file = DIR_PLUGINS.$this->info['dir'].'/'.FILE_L10N;
			if (file_exists($l10n_file)) {
				include($l10n_file);
			}

			// Chargement de la classe 
			$p = new $pname();
			if (method_exists($p, 'act')) {
				$p->act($curl->pact);
			}
			$var_tpl = $p->aff($curl->paff);
			$p = null;
		}

		return $var_tpl;
	}

}

?>
