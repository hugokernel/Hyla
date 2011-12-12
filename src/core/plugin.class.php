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

// Plugin common
define('M_NAME',        20);
define('M_DESCRIPTION', 1);
define('M_AUTHOR',      2);
define('M_VERSION',     3);
define('M_MINVERSION',  4);

define('M_CONF',        16);
define('M_DEFAULT',     17);

/* TEST */
//define('M_TYPE',        20);
define('M_TRIGGERS',    18);
define('M_CALLBACK',    19);
/* TEST */

// Plugin obj
define('M_TARGET',      5);
define('M_EXTENSION',   6);
define('M_PRIORITY',    7);     // Gui

// Plugin WS
define('M_METHODS',     8);
define('M_METHOD',      9);
define('M_PARAMS',      10);
define('M_RIGHTS',      11);    // Gui

// Plugin Gui
define('M_CAT',         12);
define('M_CONTEXT',     13);
define('M_MENU',        14);
define('M_OBJ_TYPE',    15);

/*
define('M_USER',        13);
define('M_USERTYPE',    14);
*/

class tPluginManifest {
    var $dir = null;

    var $name = null;
    var $description = null;
    var $author = null;
    var $version = null;
    var $minversion = null;

    var $conf = null;
}


class plugin
{
    var $plugin_name;

    var $plugin_dir;

    var $plugin_type;

    var $plugin_path;

    var $_conf;

    var $log;

    var $last_status;
    var $last_error;

    var $conf;

    var $manifest;

    var $site_id;

    function plugin() {

        list($tmp, $this->plugin_type, $this->plugin_name) = explode('_', get_class($this));

        $this->plugin_type = plugins::getTypeFromName($this->plugin_type);
        $this->plugin_dir = plugins::getDirFromType($this->plugin_type);

        $this->log = log::getInstance();
        $this->conf = conf::getInstance();

        $this->manifest = $this->getManifest($this->plugin_name);

        $this->site_id = HYLA_SITE_ID;

        // Testing
        $this->last_status = null;
        $this->last_error = null;

        $this->last_status_msg = null;
        $this->last_error_msg = null;
    }

    function getLastError() {
        return $this->last_error;
    }

    function getLastErrorMsg() {
        return ($this->last_error) ? $this->last_error->msg : null;
    }

    function getManifest() {
        return null;
    }

    /**
     *  Get manifest
     *  @param  string  $type   Plugin type
     *  @param  string  $name   Plugin name
     */
    function getManifestFile($type = null, $name = null) {

        global $dcache;

        // Read cache
        $m_id = $type.'-'.$name;
        $manifest = $dcache->get('mfile', $m_id);
        if (!$manifest) {

            $manifest = null;
            $path = HYLA_ROOT_PATH;

            if ($type && $name) {
                $path .= plugins::getDirFromType($type).$name.'/manifest.php';
            } else if (isset($this)) {
                $path .= $this->plugin_dir.$this->plugin_name.'/manifest.php';
            }

            if (file_exists($path)) {
                include($path);
            }

            $dcache->add('mfile', $m_id, $manifest);
        }

        return $manifest;
    }

    /**
     *  Get context
     */
    function getContext() {
        return 'plugin:'.plugins::getNameFromType($this->plugin_type).':'.$this->plugin_name;
    }

    /**
     *  Get conf value
     *  @param  string  $name   Variable name
     */
    function getConfVar($name) {
        $val = $this->conf->get($name, $this->getContext());
        if ($val) {
            return $val;
        }

        if (array_key_exists($name, $this->manifest->conf)) {
            return $this->manifest->conf[$name];
        }
    }
}

?>
