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


class image {

    /*  Renvoie des infos sur une image
        @param string $img L'image
        @access static
     */
    function getInfo($img) {

        $tab = getimagesize($img);

        $ret['sizex'] = $tab[0];
        $ret['sizey'] = $tab[1];
        $ret['exif'] = null;

        eregi('(png$)|(jp[e]?g$)|(gif$)', $img, $tab);
        $ret['extension'] = $tab[0];

        // Si l'extension Exif est présente
        if (extension_loaded('exif')) {
            $type = exif_imagetype($img);
            if ($type == IMAGETYPE_JPEG || $type == IMAGETYPE_TIFF_II || $type == IMAGETYPE_TIFF_MM) {
                $ret['exif'] = exif_read_data($img, 'EXIF');
            }
        }

        return $ret;
    }

    /*  Redimension d'image
        @param string $img L'image
        @param int $size_x Largeur de l'image voulu
        @param int $size_y Longeur de l'image voulu
        @access static
     */
    function resize($img, $size_x, $size_y = 0, $destdir = null, $send = true) {
        $ret = false;

        eregi('(png$)|(jp[e]?g$)|(gif$)', $img, $tab);
        if (strtolower($tab[0]) == 'jpg') {
            $tab[0] = 'jpeg';
        }

        $fnt = 'imagecreatefrom'.$tab[0];
        if (function_exists($fnt)) {

            $img_src = $fnt($img);

            $size_x_src = imagesx($img_src);
            $size_y_src = imagesy($img_src);

            // On reste proportionnel !
            if ($size_y == 0) {
                $coeff = $size_y_src / $size_x_src;
                $size_y = $coeff * $size_x;
            }

            // Si l'image est plus petite que le redimensionnement à faire...
            if ($size_x > $size_x_src) {
                $size_x = $size_x_src;
                $size_y = $size_y_src;
            }

//          $fnct = ($tab[0] == 'png' || !function_exists('imagecreatetruecolor')) ? 'imagecreate' : 'imagecreatetruecolor';
            $fnct = 'imagecreatetruecolor';
            $img_dst = $fnct($size_x, $size_y);
            imagecopyresized($img_dst, $img_src, 0, 0, 0, 0, $size_x, $size_y, $size_x_src, $size_y_src);

            $fnt = 'image'.$tab[0];
            if ($send && function_exists($fnt)) {
                header('Content-type: image/'.$tab[0]);
                $fnt($img_dst);
                $ret = true;
            }
            
            if ($destdir) {
                $fnt($img_dst, $destdir);
            }
        }

        return $ret;
    }
}

?>
