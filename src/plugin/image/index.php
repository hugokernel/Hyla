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

class Plugin_image extends plugin {

	function Plugin_image() {
		parent::plugin();

		$this->tpl->set_root(DIR_PLUGINS.'image');
		$this->tpl->set_file('image', 'image.tpl');
		$this->tpl->set_block('image', array(
				'image_size'	=>	'Hdlimage_size',
				'exif_data'		=>	'Hdlexif_data',
				));
	}
	
	function act($pact = null) {
	}
	
	function aff($paff) {

		$thumb_size = '500';

		$tab = graphic::getImageInfo(get_directory());

		// Calcul des tailles possibles par rapport à la taille de l'image et de la taille de la miniature par défaut
		if ($tab['sizex'] > $thumb_size) {

			$act = $val = null;

			if ($paff)
				list($act, $val) = explode(':', $paff);

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
				'AFF_SIZE_1_4'		=>	url::getCurrentObj(null, null, null, 'size:1/4'),
				'AFF_SIZE_1_3'		=>	url::getCurrentObj(null, null, null, 'size:1/3'),
				'AFF_SIZE_1_2'		=>	url::getCurrentObj(null, null, null, 'size:1/2'),
				'AFF_SIZE_1_1'		=>	url::getCurrentObj(null, null, null, 'size:1/1'),
				'OBJECT'			=>	url::getCurrentObj(),
				'OBJECT_DOWNLOAD'	=>	url::getObj($this->cobj->file.(!empty($this->cobj->target) ? '!'.$this->cobj->target : null), 'download'),
				'OBJECT_MINI'		=>	$thumb_size ? url::getCurrentObj(array('mini', $thumb_size)) : url::getObj($this->cobj->file.(!empty($this->cobj->target) ? '!'.$this->cobj->target : null), 'download'),

				'IMAGE_X'			=>	$tab['sizex'],
				'IMAGE_Y'			=>	$tab['sizey'],
				));

		if (extension_loaded('exif') && $tab['exif']) {
			$this->tpl->set_var(array(
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
//					'EXIF_ISOSPEEDRATINGS'	=>	$tab['exif']['ISOSpeedRatings'],
					'EXIF_EXIFVERSION'		=>	$tab['exif']['ExifVersion'],
					'EXIF_DATETIMEORIGINAL'	=>	$tab['exif']['DateTimeOriginal'],
					'EXIF_DATETIMEDIGITIZED'		=>	$tab['exif']['DateTimeDigitized'],
					'EXIF_COMPONENTSCONFIGURATION'	=>	$tab['exif']['ComponentsConfiguration']
					));
			$this->tpl->parse('Hdlexif_data', 'exif_data', true);
		}
		
		return $this->tpl->parse('OutPut', 'image');
	}
}

?>
