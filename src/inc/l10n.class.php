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


/*  Renvoie le texte associé
    @param  string  $str    Chaine voulue
    @param  ...
 */
function __($str) {
    $l10n = l10n::getInstance();
    $ret = $l10n->getVal($str);
    if (func_num_args() > 1) {
        $tab = func_get_args();
        $tab[0] = $ret;
        $ret = call_user_func_array('sprintf', $tab);
    }
    return $ret;
}


class l10n {

    var $_lng;
    var $_arr;

    var $_last_context;

    /*  Traduction
        @param string   $lng    La langue
     */
    function l10n($lng = DEFAULT_LNG) {
        $this->_lng = $lng;
        $this->_arr = array();

        $this->_last_context = null;
    }

    /**
     *  Get obj (Singleton...)
     */
    function getInstance() {
        static $obj = null;
        if ($obj == null) {
            $obj = new l10n();
        }
        return $obj;
    }

    /*  Envoie l'entête
     */
    function sendHeader() {
        header('Content-Language: '.$this->_lng);
    }

    /*  Inclu un fichier
        @param  string  $file   Le nom du fichier
     */
    function setFile($file) {
        $f = HYLA_ROOT_PATH.'l10n/'.$this->_lng.'/'.$file;
        if (!file_exists($f)) {
            system::end(__('Fatal error : translate file (%s) not present !', $f));
        }

        include($f);
        $this->_arr[$file] = $l10n;

        $this->_last_context = $file;

        return $f;
    }

    /*  Inclu un fichier de langue se trouvant dans un chemin différent
        @param  string  $plugin Le chemin (ex: src/plugin/obj/toto/)
        @param  string  $file   Le nom du fichier (ex: messages.php)
     */
    function setSpecialFile($path, $file) {
        $l10n = array();
        $path .= '/';
        $f = $path.'/l10n/'.$this->_lng.'/'.$file;
        if (!file_exists($f)) {
            system::end(__('Fatal error : translate file (%s) not present !', $f));
        }
        
        include($f);
        $this->_last_context = file::format($path.$file);
        $this->_arr[$this->_last_context] = $l10n;
       
        return $f;
    }

    /**
     *  Test if file exist in appropriate language dir
     *  @param  string  $path   Path
     *  @param  string  $file   File
     */
    function testFile($path, $file) {
        return file_exists(file::format($path.'/l10n/'.$this->_lng.'/'.$file));
    }

    /*  Renvoi la valeur demandée
        @param  string  $var        Valeur à retourner
        @param  string  $context    Le fichier concerné
     */
    function getVal($val, $context = null) {

        $val = stripslashes($val);

        // Si on ne spécifie aucun contexte, on les fait tous !
        if ($context) {
            $val = (array_key_exists($val, $this->_arr[$context])) ? $this->_arr[$context][$val] : $val;
        } else {
            foreach ($this->_arr as $c => $v) {
                if (array_key_exists($val, $v)) {
                    $val = $this->_arr[$c][$val];
                    break;
                }
            }
        }
        return $val;
    }

    /*  Inclu un fichier et parse le tpl
        @param  string  $var        Valeur à parser
        @param  string  $context    Le fichier concerné
     */
    function parse($var) {
        return preg_replace('/\{LANG:([^}]+)\}/e', "\$this->getVal('$1')", $var);
    }

    /*  Assigne une valeur dans le tableau de traduction
        @param  string  $context    Le fichier concerné (if null, last context is used)
        @param  string  $var        Clef
        @param  ... Argument à passer à fprint
     */
    function setStr($context, $str) {
        $ret = null;

        if (is_null($context)) {
            $context = $this->_last_context; 
        }

        if (func_num_args() >= 2) {
            if (is_array($str)) {
                foreach ($str as $strg) {
                    if (array_key_exists($strg[0], $this->_arr[$context])) {
                        $val = $strg[0];
                        $strg[0] = $this->getVal($strg[0], $context);
                        $ret = call_user_func_array('sprintf', $strg);
                        $this->_arr[$context][$val] = $ret;
                    }
                }
            } else {
                if (array_key_exists($str, $this->_arr[$context])) {
                    $strc = $this->getVal($str, $context);
                    $tab = func_get_args();
                    array_shift($tab);
                    array_shift($tab);
                    array_unshift($tab, $strc);
                    $ret = call_user_func_array('sprintf', $tab);
                    $this->_arr[$context][$str] = $ret;
                }
            }
        }
        return $ret;
    }
}

?>
