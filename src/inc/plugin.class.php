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

class tPluginInfo {
    var $name;

    var $extension;

    var $description;
    var $author;
    var $version;

    var $minversion;

    function tPluginInfo() {
        $this->name = null;
        $this->extension = array();
        $this->description = null;
        $this->author = null;
        $this->version = null;
        $this->minversion = null;
    }
}

class plugin
{
    var $plugin_name;

    var $plugin_dir;

    var $_conf;

    var $_url_2_plugin;     // L'url pour accéder au dossier du plugin courant

    function plugin() {

        $name_obj = get_class($this);

        list($tmp, $ctype, $this->plugin_name) = explode('_', $name_obj);

        switch ($ctype) {
            case 'obj':
                $this->plugin_dir = DIR_PLUGINS_OBJ;
                break;
            case 'auth':
                $this->plugin_dir = DIR_PLUGINS_AUTH;
                break;
            case 'url':
                $this->plugin_dir = DIR_PLUGINS_URL;
                break;
        }

        $this->_url_2_plugin = system::getHost().REAL_ROOT_URL.$this->plugin_dir.$this->plugin_name.'/';
        $this->_conf = array();
    }

    /*  Charge les infos d'un plugin
        @param  string  $file   L'adresse du plugin
     */
    function loadInfo($file) {

        $ret = false;

        $file = $file.'/info.xml';
        if (file_exists($file)) {
            $xml =& new XPath($file);
            $res = $xml->match('//plugin[@enabled="true"]/*');

            if ($res) {
                $ret = new tPluginInfo;
                $ret->name = strtolower($xml->getData('/plugin/name'));

                $attr = $xml->getAttributes('/plugin');
                $ret->target = strtolower($attr['target']);

                $ret->description = $xml->getData('/plugin/description');
                $ret->author = $xml->getData('/plugin/author');
                $ret->version = $xml->getData('/plugin/version');
                $ret->minversion = $xml->getData('/plugin/minversion');

                $priority = $xml->getData('/plugin/priority');
                $ret->priority = $priority ? $priority : null;

                // Specific obj, put this in plugin_obj ??
                $extension = $xml->getData('/plugin/extension');
                if ($extension) {
                    $ret->extension = explode(',', $extension);
                    $ret->extension = array_map('strtolower', $ret->extension);
                    $ret->extension = array_map('trim', $ret->extension);
                }
            }
        }

        return $ret;
    }

    /*  Configuration
     */

    /*  Lit le fichier de conf des plugins
     */
    function readConf() {
        $fileconf = $this->plugin_dir.$this->plugin_name.'/'.FILE_CONF_PLUGIN;
        if (file_exists($fileconf)) {
            $this->_conf = (function_exists('parse_ini_file')) ? parse_ini_file($fileconf, true) : iniFile::read($fileconf, true);
        }
    }

    /*  Lit une variable de configuration
        @param  string  $name   Nom de la variable
        @param  string  $group  Groupe ([param])
        @param  string  $def    Valeur par défaut si la variable n'existe pas
     */
    function getConfVar($name, $group = null, $def = null) {
        $ret = null;
        if (!$this->_conf) {
            $this->readConf();
        }

        if ($group) {
            if (array_key_exists($group, $this->_conf)) {
                $tab = $this->_conf[$group];
            }
        } else {
            $tab = &$this->_conf;
        }

        if (array_key_exists($name, $tab)) {
            $ret = $tab[$name];
        }

        if (is_null($ret)) {
            $ret = $def;
        }

        return $ret;
    }
}

?>
