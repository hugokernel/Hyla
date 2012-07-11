<?php
/*
    This file is part of Hyla
    Copyright (c) 2004-2012 Charles Rincheval.
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

class file
{
    /*  Copie un dossier dans un autre
        @param  string  $folder_origin  Le répertoire
        @param  string  $folder_dest    La cible
        @param  octal   $dir_chmod      Le mode de création des répertoires
        @return Renvoie le nombre de fichiers et répertoires créés
     */
    function copyDir($folder_origin, $folder_dest, $dir_chmod = 0765) {

        static $var = true;
        $ret = 0;

        $hdl = dir($folder_origin);
        if ($hdl) {
            while (($_occ = $hdl->read()) !== false) {
                if ($_occ == '.' || $_occ == '..')
                    continue;

                if (is_dir($folder_origin.'/'.$_occ)) {
                    if (mkdir($folder_dest.'/'.$_occ, $dir_chmod))
                        $ret++;
                    $ret += file::copyDir($folder_origin.'/'.$_occ, $folder_dest.'/'.$_occ, $dir_chmod);
                } else if (is_file($folder_origin.'/'.$_occ)) {
                    if (copy($folder_origin.'/'.$_occ, $folder_dest.'/'.$_occ))
                        $ret++;
                }
            }
        }

        return $ret;
    }

    /*  Créer des répertoires (php.net)
        @param  string  $dir    Le chemin complet contenant les répertoires à créer
        @param  octal   $mode   Les droits
     */
    function mkDirs($dir, $mode = 0755) {
        if (is_null($dir) || $dir == '')
            return false;
        if (is_dir($dir) || $dir == '/')
            return true;
        if (file::mkdirs(file::dirName($dir), $mode))
            return mkdir($dir, $mode);
        return false;
    }

    /*  Supprime un répertoire et tout son contenu
        @param string $dir
     */
    function rmDirs($dir) {
        $hdl = dir($dir);
        if ($hdl) {
            while (false !== ($_occ = $hdl->read())) {
                if ($_occ == '.' || $_occ == '..')
                    continue;
                if (is_dir($dir.'/'.$_occ))
                    file::rmDirs($dir.'/'.$_occ);
                else if (is_file($dir.'/'.$_occ))
                    unlink($dir.'/'.$_occ);
            }
            $hdl->close();
        }
        return rmdir($dir);
    }

    /*  Si le fichier existe déjà dans la racine, retourne le nom du fichier préfixé d'une string le rendant unique
        @param  string  $name   Le nom du fichier
        @param  string  $root   La racine
     */
    function getUniqueName($name, $root) {
        $prefix = '0';
        if (file_exists($root.'/'.$name)) {
            while (1) {
                if (file_exists($root.'/'.$prefix.'_'.$name)) {
                    $prefix++;
                    continue;
                }
                $name = $prefix.'_'.$name;
                break;
            }
        }
        return $name;
    }

    /*  Retourne un tableau contenant l'arborescence du repertoire spécifié
        @param  string  $_folder_root   Le répertoire racine
        @param  string  $_folder        Sous répertoire
        @param  bool    $hidden         Renvoie ou non les objets cachés
     */
    function scanDir($_folder_root, $hidden = true, $_folder = null) {
        static $tab_dir, $tab = null;
        static $cmpt = 0;
        $cmpt++;

        $hdl = dir($_folder_root.$_folder);
        if ($hdl) {
            while (false !== ($_occ = $hdl->read())) {
                if ($_occ != '.' && $_occ != '..' && is_dir($_folder_root.$_folder.'/'.$_occ)) {
                    if ($_occ{0} != '.' || $hidden) {
                        $tab_dir[] = $_folder.'/'.$_occ.'/';
                        file::scanDir($_folder_root, $hidden, $_folder.'/'.$_occ);
                    }
                }
            }
            $hdl->close();
        }

        if ($cmpt == 1) {
            $tab = $tab_dir;
            $tab_dir = null;
        }
        $cmpt--;
        return $tab;
    }

    /*  Les fichiers matchant l'expression régulière sont stockés dans un tableau
        @param  string  $folder     Répertoire pour le scan (finissant par un '/')
        @param  string  $exp        L'expression régulière
        @param  boolean $recurs     Faire ça récursivement
        @param  string  $base       Répertoire de base
        @param  boolean $scandir    Scanner aussi le nom des répertoire
        @param  bool    $hidden     Renvoie ou non les objets cachés
     */
    function searchFile($folder, $exp, $recurs = true, $base = null, $scandir = false, $hidden = false) {
        static $tab_file = null;
        static $cmpt = 0;
        $cmpt++;

        $tab = null;

        if (!$tab_file && $folder == '/')
            $folder = null;

        $hdl = dir($base.$folder);
        if ($hdl) {
            while (false !== ($_occ = $hdl->read())) {
                if ($_occ{0} == '.' && !$hidden) {
                    continue;
                }
                if (is_dir($base.$folder.'/'.$_occ) && $_occ != '.' && $_occ != '..') {
                     if ($scandir && fnmatch(strtolower($exp), strtolower($_occ))) {
                        $tab_file[] = $folder.'/'.$_occ.'/';
                    }

                    if ($recurs)
                        file::searchFile($folder.'/'.$_occ, $exp, $recurs, $base, $scandir, false);
                } else if (is_file($base.$folder.'/'.$_occ) && $_occ != '.' && $_occ != '..') {
                     if (fnmatch(strtolower($exp), strtolower($_occ))) {
                        $tab_file[] = $folder.'/'.$_occ;
                    }
                }
            }
            $hdl->close();
        }


        if ($cmpt == 1) {
            $tab = $tab_file;
            $tab_file = null;
        }
        $cmpt--;
        return $tab;
    }

    /*  Renvoie la taille totale occupée par le dossier et le nombre de fichiers contenus
        @param  string  $path   Le chemin
        @param  bool    $recurs Récursivement ?
        @return array('size', 'nbr')
     */
    function getDirSize($path, $recurs = true) {
        $ret = array('size' => 0, 'nbr' => 0);
        $dir = opendir($path);
        while ($file = readdir($dir)) {
            if (is_file($path.'/'.$file)) {
                $ret['size'] += filesize($path.'/'.$file);
                $ret['nbr']++;
            } else if (is_dir($path.'/'.$file) && $file != '.' && $file != '..' && $recurs) {
                $r = file::getDirSize($path.'/'.$file);
                $ret['size'] += $r['size'];
                $ret['nbr'] += $r['nbr'];
            }
        }
        return $ret;
    }

    /*  Renvoie le nom du dernier dossier
        @param  string  $path   Le chemin à scanner
     */
    function getLastDir($path) {
        $size = strlen($path);
        if ($path{$size - 1} == '/') {
            $path{$size - 1} = null;
        }

        $path = trim($path);
        $last_dir = strrchr($path, '/');
        $last_dir = substr($last_dir, 1);
        return $last_dir;
    }

    /*  Retourne l'extension d'un fichier
        @param string $file Le nom du fichier concerné
     */
    function getExtension($file) {
        $ext = null;
        $pos = strrpos($file, '.');

        // Attention car si on envoie toto/super.txt/cool, ça va retourner txt/cool
        if ($pos !== false) {
            $ext = substr(strtolower($file), $pos + 1, strlen($file));
        }

        return $ext;
    }

    /*  Retourne le répertoire
        ATTENTION : Différent de dirname car vérifie si le répertoire existe vraiment et si
                    $folder est un chemin avec un fichier, il extrait le chemin si il existe
        @param  string  $folder Le répertoire avec un nom de fichier ou non
        @param  string  $base La racine qui comporte $folder
        @return Le chemin réel
     */
    function getRealDir($folder, $base = null) {
        $ret = null;

        if (is_file($base.$folder)) {
            if (file::_isLegalPath($base, $folder, $rpath)) {

                if ($base{strlen($base) - 1} != '/') {
                    $base .= '/';
                }

                $ret = substr($rpath, strlen($base) - 1, (strlen($rpath) - strlen($base) + 1) - strlen(file::getRealFile($folder, $base)));
            }
        } else if (is_dir($base.$folder)) {

            if (file::_isLegalPath($base, $folder, $rpath)) {

                if ($base{strlen($base) - 1} != '/') {
                    $base .= '/';
                }

                $ret = substr($rpath, strlen($base) - 1, strlen($rpath) - strlen($base) + 1);
            }
        }

        return file::formatPath($ret);
    }

    /* Teste s'il est nécessaire, selon le client, d'utiliser le mimetype
       multipart/x-byteranges voulu par certains clients ou le standard
       multipart/byteranges. Inspiré du filtre interne "byterange" d'apache.
    */
    function use_range_x() {
        return (isset($_SERVER['HTTP_REQUEST_RANGE'])
                || strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 3') !== false);
    }

    /*  Retourne le nom du fichier
        ATTENTION : Différent de basename car vérifie si le fichier existe vraiment
        @param  string  $file Le nom du fichier
        @param  string  $base La racine comportant le fichier
     */
    function getRealFile($file, $base = null) {
        $ret = null;
        if (is_file($base.$file)) {
            if (file::_isLegalPath($base, $file, $_)) {
                $ret = file::baseName($file);
            }
        }
        return file::formatPath($ret);
    }

    /*  Envoie un fichier avec le bon type mime
        @param string $file Le fichier
     */
    function sendFile($file) {
        $ext = null;        // Extension
        $ctype = null;      // Content type

        $ext = file::getExtension($file);

        switch ($ext) {
            case 'mpeg':
            case 'mpg':
            case 'mpe':     $ctype = 'video/mpeg';          break;  // Vidéos Mpg
            case 'avi':     $ctype = 'video/avi';           break;  // Vidéos Microsoft Windows
            case 'doc':     $ctype = 'application/word';    break;
            case 'zip':     $ctype = 'application/zip';     break;
            case 'pdf':     $ctype = 'application/pdf';     break;
            case 'png':     $ctype = 'image/png';           break;
            case 'gif':     $ctype = 'image/gif';           break;
            case 'jpeg':
            case 'jpg':     $ctype = 'image/jpeg';          break;
            case 'mp3':     $ctype = 'audio/mpeg3';         break;
            case 'htm':
            case 'html':    $ctype = 'text/html';           break;

            case 'asm':
            case 'inc':
            case 'c':
            case 'cpp':
            case 'txt':     $ctype = 'text/plain';          break;
            default:        $ctype = 'octet/stream';        break;
        }

        $file_len = filesize($file);
        header('Content-Disposition: inline; filename="'.file::baseName($file).'"');
        header('Content-Type: '.$ctype);

        if (   !isset($_SERVER['HTTP_RANGE'])
            && !isset($_SERVER['HTTP_REQUEST_RANGE'])) {
            // Envoi classique du fichier en entier.
            header('Content-Length: '.$file_len);
            // Avant d'envoyer le fichier, on vide le(s) buffer(s) de sortie
            // et on le(s) désactive(s) (autrement php garde en mémoire le
            // fichier, qui est peut-être très (trop) gros).
            while(@ob_end_flush());
            readfile($file);

        } else {
            // Le client demande uniquement une ou plusieurs parties du fichier
            // RFC 2616, 14.16
            $req_range = $_SERVER['HTTP_RANGE'];
            if (empty($req_range)) // vieux navigateurs
                $req_range = $_SERVER['HTTP_REQUEST_RANGE'];

            list(, $ranges) = explode('=', $req_range, 2);
            $ranges = explode(',', $ranges);
            $parts = array();   
            foreach ($ranges as $range) {
                list($begin, $end) = explode('-', $range);
                if (   (strlen($end  ) && !is_numeric($end  ))
                   || (strlen($begin) && !is_numeric($begin))
                   || (!strlen($begin) && !strlen($end)) ) {
                    continue;
                }
                if (!strlen($end) || $end >= $file_len)
                    $end = $file_len - 1;
                if (!strlen($begin)) {
                    $begin = $file_len - $end;
                    $end = $file_len - 1;
                }
                if ($begin > $end)
                    continue;
                $parts[] = array( 'begin' => intval($begin),
                                  'end'   => intval($end  ),
                                  'len'   => intval($end) - intval($begin) + 1 );
            }

            $satisfiable = false;
            foreach ($parts as $p) {
                if ($p['begin'] < $file_len || $p['end'] != 0) {
                    $satisfiable = true;
                    break;
                }
            }
            if (!$satisfiable) {
                header("HTTP/1.1 416 Requested range not satisfiable");
                return;
            }

            header("HTTP/1.1 206 Partial Content");
            header("Accept-Ranges: bytes");
            if (count($parts) == 1) {
                // S'il n'y a qu'une partie on l'envoie directement.
                header("Content-Range: bytes "
                       .$parts[0]['begin']."-".$parts[0]['end']."/$file_len");
                header("Content-Length: ".$parts[0]['len']);
            } else {
                // S'il y a plusieurs parties à envoyer, il faut les empaqueter
                // dans un multipart (comme dans les mails).
                $bound = sprintf("%x%x%x", time(), getmypid(), rand());
                header("Content-Type: multipart/".(file::use_range_x()?'x-':'')
                       ."byteranges; boundary=$bound");
                // Calcul du la taille du contenu que nous allons envoyer
                $clen = 0;
                $interparts = array();
                foreach ($parts as $p) {
                    $clen += $p['len'];
                    $interparts[] = "\r\n--$bound\r\nContent-type: $ctype\r\n"
                                    ."Content-range: bytes ".$p['begin']."-"
                                    .$p['end']."/$file_len\r\n\r\n";
                }
                foreach ($interparts as $i)
                    $clen += strlen($i);
                $tail = "\r\n--$bound--\r\n"; // fin du multipart
                $clen += strlen($tail);
                header("Content-Length: $clen");
            }

            // Avant d'envoyer le fichier, on vide le(s) buffer(s) de sortie
            // et on le(s) désactive(s) (autrement php garde en mémoire le fichier,
            // qui est peut-être très (trop) gros).
            while(@ob_end_flush());

            $fd = fopen($file, 'rb');
            if (!$fd) return;

            for ($i = 0 ; $i < count($parts) ; $i++) {
                fseek($fd, $parts[$i]['begin'], SEEK_SET);
                $cur = $parts[$i]['begin'];
                if (count($parts) > 1)
                    print $interparts[$i];
                while (!feof($fd) && $cur <= $parts[$i]['end']
                       && !connection_aborted()) {
                    $block = min(1024*16, $parts[$i]['end']-$cur+1);
                    print fread($fd, $block);
                    $cur += $block;
                }
            }
            fclose($fd);
            if (count($parts) > 1)
                print $tail; // fin du multipart
        }
        // not exit here !
    }

    /*  Renvoie le contenu d'un fichier
        @param string $file Le fichier
     */
    function getContent($file) {
        $ret = null;
        if (function_exists('file_get_contents')) {
            $ret = file_get_contents($file);
        } else {
            $fp = fopen($file, 'rb');
            if ($fp) {
                while ($str = fread($fp, 4096)) {
                    $ret .= $str;
                }
            }
            fclose($fp);
        }
        return $ret;
    }

    /*  Ecrit dans un fichier
        @param string $file Le nom du fichier
        @param string $str Le contenu
     */
    function putContent($file, $str) {
        $ret = false;
        if (function_exists('file_put_contents')) {
            $ret = file_put_contents($file, $str);
        } else {
            $fp = fopen($file, 'ab');
            if ($fp) {
                if (fwrite($fp, $str)) {
                    fclose($fp);
                    $ret = true;
                }
            }
        }
        return $ret;
    }

    /*  Descend d'une arborescence (ATTENTION, uniquement des chemins *NIX)
        Ex: * /usr/local/apache -> /usr/local
            * /home/toto/doc.htm -> /home/toto
        @param string $path Le chemin
     */
    function downPath($path) {
        if ($path{strlen($path) - 1} == '/')
            $path{strlen($path) - 1} = null;

        $tab = explode('/', trim($path));
        $path = '/';
        $size = sizeof($tab);
        $size--;
        for ($i = 0; $i < $size; $i++) {
            $n = $tab[$i];
            if ($n) {
                $path .= $n.'/';
            }
        }
        return $path;
    }

    /*  Cette fonction renvoie le chemin canonique absolu
        Certain hébergeur désactive realpath pour des raisons de sécurité, pourtant la fonction existe et ne renvoie rien (merci Free)...
        @param  string  $path   Le chemin à tester
        @param  bool    $exist  Test si le fichier ou répertoire existe vraiment (avec ce paramètre à true, cette fonction simule parfaitement realpath)
     */
    function realpath($path, $exist = false) {

        $ret = null;
        $path = file::formatPath($path);

        if ($exist && $path == '.') {
            return file::dirName($_SERVER['PATH_TRANSLATED']);
        }

        $dirs = explode('/', $path);
        $dirs_out = array();

        $i = 0;

        foreach ($dirs as $k => $v) {

            if ($v == null || $v == '.') {
                continue;
            }

            if ($v == '..') {
                $dirs_out[--$i] = null;
            }
            // Empêcher les ..... pour descendre dans l'arbo
            if ($v == str_repeat('.', strlen($v))) {
                continue;
            } else {
                $dirs_out[$i++] = $v;
            }
        }

        $p = ($exist && substr(PHP_OS, 0, 3) == 'WIN') ? null : '/';
        foreach($dirs_out as $k => $v) {
            if (isset($v)) {
                $p .= $v.'/';
            }
        }

        $p = substr($p, 0, strlen($p) - 1);

        if ($exist) {
            if (is_dir($p)) {
                $ret = $p;
            } else if (is_file($p)) {
                $ret = $p;
            } else {
                $ret = false;
            }
        } else {
            $ret = $p;
        }

        $ret = file::unFormatPath($ret);
        if ($exist && is_dir($ret) && $ret{strlen($ret) - 1} != '/') {
            $ret .= '/';
        }

        return $ret;
    }

    /*  Fonction de remplacement de dirname car l'original pose des problèmes sous windows
        @param  string  $path   Le chemin
     */
    function dirName($path) {
        $path = dirname($path);
        $path = file::formatPath($path);
        return $path;
    }

    /*  Get the file name (original basename function is bugged in php with name started with éà...)
        @param  string  $dir    Get the file name
     */
    function baseName($dir) {

        $sep = '/';
        $pos = strrpos($dir, '/');

        if (system::osIsWin()) {
            $pos1 = strrpos($dir, '\\');
            $sep = ($pos1 > $pos) ? '\\' : '/';
            $pos = max($pos, $pos1);
        }
        
        if ($pos !== false) {
            if ($pos + 1 == strlen($dir)) {
                $pos = strrpos(substr($dir, 0, $pos), $sep);
                $ret = substr($dir, $pos + 1, -1);
            } else {
                $ret = substr($dir, strrpos($dir, $sep) + 1, strlen($dir));
            }
        } else {
            $ret = $dir;
        }

        return $ret;
    }

    /*  Test si un chemin est contenu dans un autre :
        $base:  /var/www/test/toto
        $path:  /var/www
        return: true

        $base:  /var/www/test/toto
        $path:  /var/www/pouf
        return: false
    */
    function isInPath($base, $path, $oflow = false) {
        $ret = true;

        $tbase = explode('/', $base);
        $tpath = explode('/', $path);

        $sbase = sizeof($tbase);
        $spath = sizeof($tpath);
        $size = $oflow ? min($sbase, $spath) : sizeof($tpath);
        for ($i = 0; $i < $size; $i++) {
            if (!array_key_exists($i, $tbase)) {
                $ret = false;
                break;
            }
            if (!empty($tpath[$i]) && !empty($tbase[$i]) && $tpath[$i] != $tbase[$i]) {
                $ret = false;
                break;
            }
        }

        return $ret;
    }

    /*  Combien de dossier en commum ?
        @param  string  $path0  First path
        @param  string  $path1  Second path
     */
    function countInPath($path0, $path1) {
        
        $dir0 = explode('/', $path0);
        $dir1 = explode('/', $path1);
    
        $size = max(count($dir0), count($dir1));
        for ($i = 0, $result = 0; $i < $size; $i++) {
            if ($dir0[$i] && $dir1[$i]) {
                if ($dir0[$i] != $dir1[$i]) {
                    break;
                } else {
                    $result++;   
                }
            }
        }
        
        return $result;
    }

    /*  Renvoie une chaine propre pour windows
     */
    function formatPath($path) {
        if (system::osIsWin()) {
            $path = str_replace('\\', '/', $path);
        }
        return $path;
    }

    /*  Renvoie une chaine propre pour windows
     */
    function unFormatPath($path) {
        if (system::osIsWin()) {
            $path = str_replace('/', '\\', $path);
        }
        return $path;
    }

    /*  Renvoie true si le chemin canonique absolu ne descend pas en dessous de la base
        @param string $base La base référence
        @param string $path Le chemin symbolique ou autre
        @param string $realpath Retourne le vrai chemin avec la base
        @return boolean
        @access private
     */
    function _isLegalPath($base, $path, &$realpath) {
        $ret = false;

        $rpath = file::realpath($path);
        $rpath = file::realpath($base.$rpath, true);

        if ($rpath) {
            $realpath = $rpath;
            $ret = true;
        }

        return $ret;
    }
}

?>
