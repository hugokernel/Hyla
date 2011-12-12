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

class cache {

    /*  Vide le cache
     */
    function free() {
        $i = 0;
        $hdl = dir(HYLA_RUN_PATH.DIR_CACHE);
        if ($hdl) {
            while (false !== ($occ = $hdl->read())) {
                if ($occ{0} == '.') {
                    continue;
                }

                if (file::rmDirs(HYLA_RUN_PATH.DIR_CACHE.$occ)) {
                    $i++;
                }
            }
        }
        return $i;
    }

    /*  Supprime les infos en cache d'un fichier
        @param  string  $file   Le fichier concerné
     */
    function del($file) {

        $types = archive::getAllType();
        foreach ($types as $type) {
            cache::getArchivePath($file, $out, $type);
            if (is_file(HYLA_RUN_PATH.$out)) {
                unlink(HYLA_RUN_PATH.$out);
            }
        }

        $obj = obj::getInstance();
        $path = $obj->getRoot();
        if (!is_dir($path.$file)) {

            cache::getFilePath($file, $out);
            if (is_dir($out)) {
                $file =  array();

                // Récupération des miniatures pour suppression
                $hdl = dir($out);
                if ($hdl) {
                    while (false !== ($_occ = $hdl->read())) {
                        if ($_occ == '.' || $_occ == '..') {
                            continue;
                        }
                        $fmd5 = md5(file::dirName(HYLA_RUN_PATH.$out.'/'.$_occ));
                        $file[] = '#'.preg_quote($fmd5).'.[0-9]*x[0-9]*.'.preg_quote($_occ).'#s';
                        $dir = file::dirName($fmd5{0}.'/'.$fmd5);
                    }
                }
                $hdl->close();

                if (is_dir(HYLA_RUN_PATH.DIR_CACHE.$dir)) {
                    $list = array();
                    $hdl = dir(HYLA_RUN_PATH.DIR_CACHE.$dir);
                    if ($hdl) {
                        while (false !== ($_occ = $hdl->read())) {
                            if ($_occ == '.' || $_occ == '..')
                                continue;
                            $list[] = $_occ;
                        }
                        $hdl->close();

                        $cmpt = 0;
                        foreach ($file as $k) {
                            $ret = null;
                            if ($ret = preg_grep($k, $list)) {
                                $ret = array_values($ret);
                                foreach ($ret as $r) {
                                    unlink(HYLA_RUN_PATH.DIR_CACHE.$dir.'/'.$r);
                                }
                            }
                        }
                    }
                }

                if (is_dir(HYLA_RUN_PATH.$out)) {
                    file::rmDirs(HYLA_RUN_PATH.$out);
                }
            }
        }
    }

    /*  Renvoie le chemin et le nom vers l'image "cachée" en tenant compte de la taille de l'image

        On obtient un résultat proche de celui ci :
        - cache/6/6676cd76f96956469e7be39d750cc7d8.320x240.name.jpg

        @param  string  $file   Le fichier concerné
        @param  int     $sizex  La taille x
        @param  int     $sizey  La taille y
     */
    function getImagePath($file, $sizex, $sizey, &$out) {

        $fmd5 = md5(file::dirName($file));

        if (!is_dir(DIR_CACHE.$fmd5{0}))
            mkdir(DIR_CACHE.$fmd5{0});

        $out = DIR_CACHE.$fmd5{0}.'/'.$fmd5.'.'.$sizex.'x'.$sizey.'.'.basename($file);

        return (bool)file_exists($out);
    }

    /*  Renvoie le chemin et le nom du fichier à cacher

        On obtient un résultat proche de celui ci :
        - cache/6/6676cd76f96956469e7be39d750cc7d8.jpg

        @param  string  $file   Le fichier concerné
        @param  &string $out    Le buffer oû écrire le résultat
        @return Renvoie true si le fichier existe déjà dans le cache
     */
    function getFilePath($file, &$out) {

        $fmd5 = md5(file::dirName($file));

        if (!is_dir(DIR_CACHE.$fmd5{0}))
            mkdir(DIR_CACHE.$fmd5{0});

        $out = DIR_CACHE.$fmd5{0}.'/'.$fmd5.'.'.basename($file);

        return (bool)file_exists($out);
    }

     /*  Renvoie le chemin et le nom de l'archive à cacher

        On obtient un résultat proche de celui ci :
        - IN    :   /gal/LICENSE.txt.zip
        - OUT   :   cache/1/17be6e1c87b44864d301d499d68eec5d/LICENSE.txt.zip

        @param  string  $file   Le fichier concerné
        @param  &string $out    Le buffer oû écrire le résultat
        @return Renvoie true si le fichier existe déjà dans le cache
     */
    function getArchivePath($file, &$out, $type) {

        $fmd5 = md5(file::dirName($file));

        if (!is_dir(DIR_CACHE.$fmd5{0})) {
            mkdir(DIR_CACHE.$fmd5{0});
        }

        if (!is_dir(DIR_CACHE.$fmd5{0}.'/'.$fmd5)) {
            mkdir(DIR_CACHE.$fmd5{0}.'/'.$fmd5);
        }

        if ($file == '/') {
            $file = 'root';
        }

        $out = DIR_CACHE.$fmd5{0}.'/'.$fmd5.'/'.basename($file).'.'.$type;

        return (bool)file_exists($out);
    }
}

?>
