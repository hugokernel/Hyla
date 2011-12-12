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


require 'src/lib/pclzip.lib.php';

class archive {


	/*	Créé une archive contenant tous les fichiers d'un répertoire
		@param	string	$archive	L'archive de destination
		@param	string	$path		Le répertoire en question
	 */
	function createFromDir($archive, $path) {

		$ret = 0;

		$hdl = dir($path);
		if ($hdl) {
			$str = null;

//			require_once('src/lib/zip.lib.php');
//			$zip = new zipfile();

			while (false !== ($occ = $hdl->read())) {

				// Si on a un fichier caché...
				if ($occ{0} == '.')
					continue;

				if (is_file($path.$occ)) {
					$str .= $path.$occ.',';
/*					$str = $path.$occ;
					$content = file::getContent($str);
					$zip->addfile($content, $occ);
*/
				}
			}

//			$content = $zip->file();
//			echo 'var: '.$content;
//			file::putContent('/var/www/gal/archive.zip', $content);


			$zip = new PclZip($archive);
			$out = $zip->create($str, PCLZIP_OPT_REMOVE_PATH, $path);
		}

		return $out;
	}

	/*	Renvoie un tableau contenant les nom des fichiers précédent et suivant en tenant compte du tri
		@param	string	$archive	L'archive
	 */
	function getPrevNext($archive, $target) {

		$ret = array('prev' => null, 'next' => null);

		$this->zip = new PclZip($archive);
		$tab = $this->zip->listContent();

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
