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

require HYLA_ROOT_PATH.'src/core/plugin_obj.class.php';
require HYLA_ROOT_PATH.'src/core/plugin_auth.class.php';
require HYLA_ROOT_PATH.'src/core/plugin_url.class.php';
require HYLA_ROOT_PATH.'src/core/plugin_db.class.php';
require HYLA_ROOT_PATH.'src/core/plugin_ws.class.php';
require HYLA_ROOT_PATH.'src/core/plugin_gui.class.php';

define('PLUGIN_TYPE_OBJ',   1);
define('PLUGIN_TYPE_AUTH',  2);
define('PLUGIN_TYPE_URL',   3);
define('PLUGIN_TYPE_ADMIN', 4);
define('PLUGIN_TYPE_DB',    5);
define('PLUGIN_TYPE_WS',    6);
define('PLUGIN_TYPE_GUI',   7);

$plugin_2_name = array(
        PLUGIN_TYPE_OBJ     =>  array('name' => 'obj',  'path' => DIR_PLUGINS_OBJ),
        PLUGIN_TYPE_AUTH    =>  array('name' => 'auth', 'path' => DIR_PLUGINS_AUTH),
        PLUGIN_TYPE_URL     =>  array('name' => 'url',  'path' => DIR_PLUGINS_URL),
        PLUGIN_TYPE_DB      =>  array('name' => 'db',   'path' => DIR_PLUGINS_DB),
        PLUGIN_TYPE_WS      =>  array('name' => 'ws',   'path' => DIR_PLUGINS_WS),
        PLUGIN_TYPE_GUI     =>  array('name' => 'gui',  'path' => DIR_PLUGINS_GUI),
);


class plugins {

    /*  Load a plugin
        @access static
        @param  int     $type   Plugin type
        @param  mixed   $param  Param 0
        @param  mixed   $param1 Param 1
        @return Object
     */
    function get($type, $param = null) {//, $param1 = null) {


        static $plugins = array();
        $plugin = null;
        $cname = null;

        // Use dcache
        if (is_null($param) && array_key_exists($type, $plugins)) {
           return $plugins[$type];
        }

        switch ($type) {
            case PLUGIN_TYPE_OBJ:
                if ($param) {
                    $plugin = plugin_obj::getManifest($param);
                } else {
                    $obj = obj::getInstance();
                    $plugin = plugin_obj::search($obj->getCurrentObj()); //$param1);
                }
                break;
            case PLUGIN_TYPE_AUTH:
                $conf = conf::getInstance();    // Do not remove !
                $name = plugins::search(($param) ? $param : $conf->get('plugin_default_auth'), $type);
                $cname = 'plugin_auth_'.$name;
                break;
            case PLUGIN_TYPE_URL:
                $conf = conf::getInstance();    // Do not remove !
                $name = plugins::search(($param) ? $param : $conf->get('plugin_default_url'), $type);
                $cname = 'plugin_url_'.$name;
                break;
            case PLUGIN_TYPE_DB:
                // ToDo: clear loadDsn because already used in plugin_db
                $aDsn = plugin_db::loadDsn(($param) ? $param : DSN);
                $name = plugins::search($aDsn['backend'], $type);
                $cname = 'plugin_db_'.$name;
                break;
            case PLUGIN_TYPE_WS:
                $name = plugins::search($param, $type);
                $cname = 'plugin_ws_'.$name;
                break;
            case PLUGIN_TYPE_GUI:
                $name = plugins::search($param, $type);
                $cname = 'plugin_gui_'.$name;
                break;
        }

        if (class_exists($cname)) {
            $plugin = new $cname();
        } else if ($type != PLUGIN_TYPE_OBJ) {
            system::end(sprintf('Error: unable to load \'%s\' !', $cname));
        }

        $plugins[$type] = &$plugin;

        return $plugin;
    }

    /*  Search plugin and return it !
        @param  string  $name   Name
        @param  int     $type   Type
     */
    function search($name, $type) {
        $ret = null;
        $file_mask = HYLA_ROOT_PATH.DIR_PLUGINS.'%s/%s/index.php';
        $file = sprintf($file_mask, plugins::getNameFromType($type), $name);
        if ($type == PLUGIN_TYPE_OBJ && !file_exists($file)) {
            $ret = 'default';
            $file = sprintf($file_mask, plugins::getNameFromType($type), $ret);
            if (!file_exists($file)) {
                system::end(__('Fatal error : Default plugin (%s) not exists !', plugins::getNameFromType($type)));
            }
        }

        if (file_exists($file)) {
            $ret = $name;
            include_once($file);
        }

        return $ret;
    }

    /*  Test if a plugin exists
        @param  string  $name   Plugin name
        @param  string  $type   Type
     */
    function isValid($name, $type = PLUGIN_TYPE_OBJ) {
        $ret = false;
        $file_mask = DIR_PLUGINS.'%s/%s/index.php';
        $file = sprintf($file_mask, plugins::getNameFromType($type), $name);
        if (file_exists($file)) {
            $ret = true;
        }

        return $ret;
    }

    /**
     *  Get plugin name from type
     *  @param  string  $type   Type
     *  @access static
     */
    function getNameFromType($type) {
        global $plugin_2_name;
        return (array_key_exists($type, $plugin_2_name) ? $plugin_2_name[$type]['name'] : null);
    }

    /**
     *  Get dir from plugin type
     *  @param  string  $type   Plugin type
     *  @access static
     */
    function getDirFromType($type) {
        global $plugin_2_name;
        return (array_key_exists($type, $plugin_2_name) ? $plugin_2_name[$type]['path'] : null);
    }

    /**
     *  Get plugin type from name
     *  @param  string  $name   Name
     *  @access static
     */
    function getTypeFromName($name) {
        global $plugin_2_name;
        
        $ret = 0;
        foreach ($plugin_2_name as $type => $info) {
            if ($info['name'] == $name) {
                $ret = $type;
                break;
            }
        }
        
        return $ret;
    }

    /**
     *  Get all plugins type
     *  @param  bool    $info   Return info
     */
    function getAllType($info = false) {
        global $plugin_2_name;
        $ret = array();
        foreach ($plugin_2_name as $type => $data) {
            if ($info) {
                $ret[$type] = $data;
            } else {
                $ret[] = $type;
            }
        }
        return $ret;
    }
}

?>
