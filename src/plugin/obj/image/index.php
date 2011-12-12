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

class plugin_obj_image extends plugin_obj {

	function plugin_obj_image($cobj) {
		parent::plugin_obj($cobj);

		$this->tpl->set_root($this->plugin_dir.'image');
		$this->tpl->set_file('image', 'image.tpl');
		$this->tpl->set_block('image', array(
				'image_size'	=>	'Hdlimage_size',
				'exif_data'		=>	'Hdlexif_data',
				));
	}
	
	function act($pact = null) {
	}
	
	function aff($paff) {

		$this->addStyleSheet('default.css');

		$thumb_size_conf = $this->getConfVar('thumb_size');
		$thumb_size_conf = ($thumb_size_conf) ? $thumb_size_conf : '500';
		$thumb_size = $thumb_size_conf;

		$tab = image::getInfo($this->_real_file);

		// Calcul des tailles possibles par rapport à la taille de l'image et de la taille de la miniature par défaut
		if ($tab['sizex'] > $thumb_size && extension_loaded('gd')) {

			$act = $val = null;

			if ($paff)
				list($act, $val) = explode(':', $paff);

			if ($act == 'size') {
				if ($val == '1/4') {
					$thumb_size = round($tab['sizex'] / 4);
					$this->saveVar('sized', $thumb_size.'|'.$tab['sizex']);
				} else if ($val == '1/3') {
					$thumb_size = round($tab['sizex'] / 3);
					$this->saveVar('sized', $thumb_size.'|'.$tab['sizex']);
				} else if ($val == '1/2') {
					$thumb_size = round($tab['sizex'] / 2);
					$this->saveVar('sized', $thumb_size.'|'.$tab['sizex']);
				} else if ($val == '1/1') {
					$thumb_size = 0;
					$this->saveVar('sized', $thumb_size.'|'.$tab['sizex']);
				} else {
					$thumb_size = $thumb_size_conf;
					$this->saveVar('sized', null);
				}
			} else {
				// On prend la dernière taille demandée pour cette taille d'image
				$t_size = $this->getVar('sized');
				@list($t_size, $size) = @explode('|', $t_size);
				if ($size == $tab['sizex']) {
					$thumb_size = $t_size;
				}
			}

			$this->tpl->parse('Hdlimage_size', 'image_size', true);
		}

		$this->tpl->set_var(array(
				'AFF_SIZE_1_4'		=>	url::getCurrentObj(null, null, null, 'size:1/4'),
				'AFF_SIZE_1_3'		=>	url::getCurrentObj(null, null, null, 'size:1/3'),
				'AFF_SIZE_1_2'		=>	url::getCurrentObj(null, null, null, 'size:1/2'),
				'AFF_SIZE_1_1'		=>	url::getCurrentObj(null, null, null, 'size:1/1'),

				'AFF_SIZE_DEFAULT'	=>	url::getCurrentObj(null, null, null, 'size:'),

				'OBJECT_DOWNLOAD'	=>	url::getCurrentObj('download'),
				'OBJECT_MINI'		=>	($thumb_size && extension_loaded('gd')) ? url::getCurrentObj(array('mini', $thumb_size)) : url::getCurrentObj('download'),

				'IMAGE_X'			=>	$tab['sizex'],
				'IMAGE_Y'			=>	$tab['sizey'],
				));

		// Si des infos Exif sont disponibles...
		if (extension_loaded('exif') && $tab['exif'] && !empty($tab['exif'])) {
			$tab_exif = array(
					'EXIF_MAKE'						=>	'Make',
					'EXIF_MODEL'					=>	'Model',
					'EXIF_ORIENTATION'				=>	'Orientation',
					'EXIF_XRESOLUTION'				=>	'XResolution',
					'EXIF_YRESOLUTION'				=>	'YResolution',
					'EXIF_RESOLUTIONUNIT'			=>	'ResolutionUnit',
					'EXIF_DATETIME'					=>	'DateTime',
					'EXIF_YCBCRPOSITIONING'			=>	'YCbCrPositioning',
					'EXIF_EXIFIFDPOINTER'			=>	'Exif_IFD_Pointer',
					'EXIF_EXPOSURETIME'				=>	'ExposureTime',
					'EXIF_FNUMBER'					=>	'FNumber',
					'EXIF_EXPOSUREPROGRAM'			=>	'ExposureProgram',
//					'EXIF_ISOSPEEDRATINGS'			=>	$tab['exif']['ISOSpeedRatings'],
					'EXIF_EXIFVERSION'				=>	'ExifVersion',
					'EXIF_DATETIMEORIGINAL'			=>	'DateTimeOriginal',
					'EXIF_DATETIMEDIGITIZED'		=>	'DateTimeDigitized',
					'EXIF_COMPONENTSCONFIGURATION'	=>	'ComponentsConfiguration',
					);

			foreach ($tab_exif as $k => $v) {
				if (isset($tab['exif'][$v])) 
					$this->tpl->set_var($k, $tab['exif'][$v]);
			}

			$this->tpl->parse('Hdlexif_data', 'exif_data', true);
		}
		
		return $this->tpl->parse('OutPut', 'image');
	}
}

?>
