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

class Plugin_image extends plugin {

	function Plugin_image() {
		parent::plugin();

		$this->tpl->set_root(FOLDER_PLUGINS.'image');
		$this->tpl->set_file(array(
				'image'	 	=>	'image.tpl'));
		$this->tpl->set_block('image', array(
				'image_size'	=>	'Hdlimage_size'));
	}
	
	function act($mact = null) {
	}
	
	function aff($maff) {

		$thumb_size = '500';

		$tab = graphic::getImageInfo(get_directory());

		// Calcul des tailles possibles par rapport à la taille de l'image et de la taille de la miniature par défaut
		if ($tab['sizex'] > $thumb_size) {

			$act = $val = null;

			if ($maff)
				list($act, $val) = explode(':', $maff);

			if ($act == 'size') {
				if ($val == '1/4')
					$thumb_size = round($tab['sizex'] / 4);
				if ($val == '1/3')
					$thumb_size = round($tab['sizex'] / 3);
				else if ($val == '1/2')
					$thumb_size = round($tab['sizex'] / 2);
				else if ($val == '1/1')
					$thumb_size = 0;
			}
			$this->tpl->parse('Hdlimage_size', 'image_size', true);
		}

		$this->tpl->set_var(array(
				'OBJECT'				=>	obj::getCurrentUrl(AFF_INFO),
				'OBJECT_DOWNLOAD'		=>	obj::getUrl($this->cobj->file.(!empty($this->cobj->target) ? '!'.$this->cobj->target : null), AFF_DOWNLOAD),
				'OBJECT_MINI'			=>	$thumb_size ? obj::getCurrentUrl(AFF_MINI, $thumb_size) : obj::getUrl($this->cobj->file.(!empty($this->cobj->target) ? '!'.$this->cobj->target : null), AFF_DOWNLOAD),

				'IMAGE_X'	=>	$tab['sizex'],
				'IMAGE_Y'	=>	$tab['sizey'],

				'EXIF_MAKE'		=>	$tab['exif']['Make'],
				'EXIF_MODEL'	=>	$tab['exif']['Model'],
				'EXIF_ORIENTATION'	=>	$tab['exif']['Orientation'],
				'EXIF_XRESOLUTION'	=>	$tab['exif']['XResolution'],
				'EXIF_YRESOLUTION'	=>	$tab['exif']['YResolution'],
				'EXIF_RESOLUTIONUNIT'	=>	$tab['exif']['ResolutionUnit'],
				'EXIF_DATETIME'			=>	$tab['exif']['DateTime'],
				'EXIF_YCBCRPOSITIONING'	=>	$tab['exif']['YCbCrPositioning'],
				'EXIF_EXIFIFDPOINTER'	=>	$tab['exif']['Exif_IFD_Pointer'],

				'EXIF_EXPOSURETIME'	=>	$tab['exif']['ExposureTime'],
				'EXIF_FNUMBER'		=>	$tab['exif']['FNumber'],
				'EXIF_EXPOSUREPROGRAM'	=>	$tab['exif']['ExposureProgram'],
//				'EXIF_ISOSPEEDRATINGS'	=>	$tab['exif']['ISOSpeedRatings'],
				'EXIF_EXIFVERSION'		=>	$tab['exif']['ExifVersion'],
				'EXIF_DATETIMEORIGINAL'	=>	$tab['exif']['DateTimeOriginal'],
				'EXIF_DATETIMEDIGITIZED'		=>	$tab['exif']['DateTimeDigitized'],
				'EXIF_COMPONENTSCONFIGURATION'	=>	$tab['exif']['ComponentsConfiguration']
		));
		
		return $this->tpl->parse('OutPut', 'image');
	}
}

?>
