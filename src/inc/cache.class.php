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


class cache {

	/*	Renvoie le chemin et le nom vers l'image "cachée" en tenant compte de la taille de l'image

		On obtient un résultat proche de celui ci :
		- cache/6/6676cd76f96956469e7be39d750cc7d8.320x240.name.jpg

		@param	string	$file	Le fichier concerné
		@param	int		$sizex	La taille x
		@param	int		$sizey	La taille y
	 */
	function getImagePath($file, $sizex, $sizey, &$out) {

		$fmd5 = md5(dirname($file));

		if (!is_dir(FOLDER_CACHE.$fmd5{0}))
			mkdir(FOLDER_CACHE.$fmd5{0});

		$out = FOLDER_CACHE.$fmd5{0}.'/'.$fmd5.'.'.$sizex.'x'.$sizey.'.'.basename($file);

		return (bool)file_exists($out);
	}

	/*	Renvoie le chemin et le nom du fichier à cacher

		On obtient un résultat proche de celui ci :
		- cache/6/6676cd76f96956469e7be39d750cc7d8.jpg

		@param	string	$file	Le fichier concerné
		@param	&string	$out	Le buffer oû écrire le résultat
		@return Renvoie true si le fichier existe déjà dans le cache
	 */
	function getFilePath($file, &$out) {

		$fmd5 = md5(dirname($file));

		if (!is_dir(FOLDER_CACHE.$fmd5{0}))
			mkdir(FOLDER_CACHE.$fmd5{0});

		$out = FOLDER_CACHE.$fmd5{0}.'/'.$fmd5.'.'.basename($file);

		return (bool)file_exists($out);
	}

	/*	Renvoie le chemin et le nom de l'archive à cacher

		On obtient un résultat proche de celui ci :
		- IN	:	/gal/LICENSE.txt.zip
		- OUT	:	cache/1/17be6e1c87b44864d301d499d68eec5d/LICENSE.txt.zip

		@param	string	$file	Le fichier concerné
		@param	&string	$out	Le buffer oû écrire le résultat
		@return Renvoie true si le fichier existe déjà dans le cache
	 */
	function getArchivePath($file, &$out) {

		$fmd5 = md5(dirname($file));

		if (!is_dir(FOLDER_CACHE.$fmd5{0}))
			mkdir(FOLDER_CACHE.$fmd5{0});

		if (!is_dir(FOLDER_CACHE.$fmd5{0}.'/'.$fmd5))
			mkdir(FOLDER_CACHE.$fmd5{0}.'/'.$fmd5);

		if ($file == '/')
			$file = 'root';

		$out = FOLDER_CACHE.$fmd5{0}.'/'.$fmd5.'/'.basename($file);

		return (bool)file_exists($out);
	}
	


	/*	Lit un fichier depuis le cache si il y est
	 */
	function getContent() {

	}

}

?>
