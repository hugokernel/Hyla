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

require 'src/lib/pclerror.lib.php';
require 'src/lib/pcltrace.lib.php';
require 'src/lib/pcltar.lib.php';
require 'src/lib/pclzip.lib.php';

class archive {

	/*	Retourne le type d'une archive
		@param	string	$file	Le nom de l'archive	
	 */
	function getType($file) {
		$size = strlen($file);		// ToDo: Remplacer par unpack pour lire l'entête des fichiers
		$type = (substr($file, $size - 6, $size) == 'tar.gz') ? 'tar.gz' : file::getExtension($file);
		return $type;
	}

	/*	Liste les fichiers contenus dans une archive
		@param	string	$file	L'archive
	 */
	function listContent($file) {
		$ret = null;

		$type = archive::getType($file);
		switch ($type) {
			case 'zip':
				$zip = new PclZip($file);
				$ret = $zip->listContent();
				break;

			case 'tar':
			case 'gz':
			case 'tar.gz':
				$ret = PclTarList($file);
				break;
		}

		return $ret;
	}

	/*	Extrait les fichiers
		@param	string	$file	L'archive
		@param	string	$dest	Le répertoire de destination
	 */
	function extract($file, $dest) {
		$ret = false;

		$type = archive::getType($file);
		switch ($type) {
			case 'zip':
				$zip = new PclZip($file);
				$ret = $zip->extract($dest);
				break;

			case 'tar':
			case 'gz':
			case 'tar.gz':
				$ret = PclTarExtract($file, $dest);
				break;
		}

		return $ret;
	}


	/*	Créé une archive contenant tous les fichiers d'un répertoire
		@param	string	$archive	L'archive de destination
		@param	string	$path		Le répertoire en question
	 */
	function createFromDir($archive, $path) {

		$ret = 0;

		$hdl = dir($path);
		if ($hdl) {
			$str = null;
			while (false !== ($occ = $hdl->read())) {

				// Si on a un fichier caché...
				if ($occ{0} == '.')
					continue;

				if (is_file($path.$occ)) {
					$str[] = $path.$occ;
//					$str .= $path.$occ.',';
				}
			}
/*
			$zip = new PclZip($archive);
			$out = $zip->create($str, PCLZIP_OPT_REMOVE_PATH, $path);
*/
			$out = PclTarCreate($archive, $str, 'tar', null, $path);
		}

		return $out;
	}

	/*	Renvoie un tableau contenant les nom des fichiers précédent et suivant en tenant compte du tri
		@param	string	$archive	L'archive
	 */
	function getPrevNext($archive, $target) {

		$ret = array('prev' => null, 'next' => null);

		$tab = archive::listContent($archive);

		$size = sizeof($tab);
		for ($i = 0, $prev = 0, $fprev = false; $i < $size; $i++) {

			if ($tab[$i]['folder'])
				continue;

			if ($fprev && !$tab[$i]['folder']) {
				$ret['next'] = $tab[$i]['filename'];
				break;
			}

			if (!$ret['prev'] && $tab[$i]['filename'] == $target) {
				$ret['prev'] = !$ret['prev'] ? $prev : $ret['prev'];
				$fprev = true;
			}

			$prev = $tab[$i]['filename'];
		}

		return $ret;
	}

}

?>
