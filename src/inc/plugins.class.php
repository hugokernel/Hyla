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

define('PLUGIN_TYPE_OBJ',   1);
define('PLUGIN_TYPE_AUTH',  2);
define('PLUGIN_TYPE_URL',   4);

class plugins {

    /*  Load a plugin
        @access static
        @param  int $type   Plugin type
        @param  int $param  Param
        @return Object
     */
    function get($type, $param = null) {
        $plugin = null;

        switch ($type) {
            case PLUGIN_TYPE_OBJ:
                if (!$param) {
                    $plugin = plugin_obj::search();
                } else {
                    $plugin = plugin::loadInfo(DIR_PLUGINS_OBJ.strtolower($param));
                }
                break;
            case PLUGIN_TYPE_AUTH:
                $name = plugins::search($param, $type);
                $cname = 'plugin_auth_'.$name;
                $plugin = new $cname();
                break;
            case PLUGIN_TYPE_URL:
                $name = plugins::search($param, $type);
                $cname = 'plugin_url_'.$name;
                $plugin = new $cname();
                break;
        }
        
        return $plugin;
    }

    /*  Search plugin and return it !
        @param  string  $name   Name
        @param  int     $type   Type
     */
    function search($name, $type) {
        
        $file_mask = DIR_PLUGINS.'%s/%s/index.php';
        $file = sprintf($file_mask, plugins::getNameFromType($type), $name);
        if (!file_exists($file)) {
            $name = 'default';
            $file = sprintf($file_mask, plugins::getNameFromType($type), $name);
            if (!file_exists($file)) {
                exit(__('Fatal error : Default plugin (%s) not exists !', plugins::getNameFromType($type)));
            }
        }

        include_once($file);

        return $name;
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
    
    /*  Get plugin name from type
        @param  string  $type   Type
     */
    function getNameFromType($type) {
        $type2name = array( PLUGIN_TYPE_OBJ     =>  'obj',
                            PLUGIN_TYPE_AUTH    =>  'auth',
                            PLUGIN_TYPE_URL     =>  'url',
                        );
        return $type2name[$type];
    }
}

?>
