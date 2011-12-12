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
			$res = $xml->match('/plugin[contains(@ext,"'.$this->cobj->extension.'")]/*');
			if ($res) {
				$this->info = array(
						'dir'			=>	$file,
						'name'			=>	utf8_decode($xml->getData('/plugin/name')),
						'description'	=>	utf8_decode($xml->getData('/plugin/description')),
						'author'		=>	utf8_decode($xml->getData('/plugin/author')),
						'version'		=>	utf8_decode($xml->getData('/plugin/version'))
						);
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
		return plugins::_getPlugins(true);
	}

	/*	Renvoie un tableau contenant les plugins disponibles pour un fichier
		@access	static
	 */
	function getFilePlugins() {
		return plugins::_getPlugins(false);
	}

	function _getPlugins($type = true) {

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
					$exp = $type ? '/plugin[contains(@target,"dir")]/*' : '/plugin[contains(@target,"file")]/*';
					$res = $xml->match($exp);
					if ($res) {
						$tab[] = array(
							'dir'			=>	$xfile,
							'name'			=>	utf8_decode($xml->getData('/plugin/name')),
							'description'	=>	utf8_decode($xml->getData('/plugin/description')),
							'author'		=>	utf8_decode($xml->getData('/plugin/author')),
							'version'		=>	utf8_decode($xml->getData('/plugin/version'))
							);
					}
				}
			}
		}

		return $tab;
	}

	/*	Vérifie que le plugin existe bien
		@param	string	$plugin_name	Le nom du plugin
		@return	Retourne true ou false
	 */
	function isValid($plugin_name) {
		$ret = false;
		$tab = plugins::getDirPlugins();
		foreach ($tab as $occ) {
			if (strtolower($occ['name']) == strtolower($plugin_name)) {
				$ret = true;
				break;
			}
		}
		return $ret;
	}

	/*	Charge le plugin correspondant
		@param	string	$plugin	Le plugin forcé
		@return	On renvoie le contenu généré par le plugin
	 */
	function load($plugin = null) {

		global $curl, $conf, $l10n;

		$var_tpl = null;

		$plugin_dir = $plugin ? strtolower($plugin) : $this->info['dir'];

		$pfile = DIR_PLUGINS.$plugin_dir.'/index.php';

		if (file_exists($pfile)) {
			include($pfile);

			$pname = 'Plugin_';
			$pname .= $plugin_dir;

			// Y'a t-il un fichier de langue ?
			$l10n_file = DIR_PLUGINS.$plugin_dir.'/'.FILE_L10N;
			if (file_exists($l10n_file)) {
				include($l10n_file);
			}

			// Chargement de la classe 
			$p = new $pname();

			$p->plugin_name = strtolower($this->info['name']);

			if (method_exists($p, 'act')) {
				$p->act($curl->pact);
			}

			// Y'a t-il une méthode fullscreen ?
			if (method_exists($p, 'fullscreen') && $plugin) {
				$var_tpl = $p->fullscreen($curl->paff);
				print($p->tpl->finish($var_tpl));
				system::end();
			}

			$var_tpl = $p->aff($curl->paff);
			$p = null;
		}

		return $var_tpl;
	}

}

?>
