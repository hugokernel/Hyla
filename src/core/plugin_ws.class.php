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

class plugin_ws extends plugin
{
    var $obj;

    var $bdd;

    var $status;
    
    var $conf;

    function plugin_ws() {
        parent::plugin();

        $this->obj = obj::getInstance();
        $this->bdd = plugins::get(PLUGIN_TYPE_DB);
        $this->conf = conf::getInstance();
    }

    // ToDo: Implement getManifest

    /**
     *  Run action
     *  @param  string  $method Method
     *  @param  array   $param  Array containing parameters for run action
     */
    function run($method, $param = array()) {

        $class = null;
        $p = array();

        // Get manifest
        $manifest = $this->getManifestFile();
        if (!$manifest) {
            return new tError(__('No manifest found !'), $this);
        }

        // Test if method exists
        if (!array_key_exists($method, $manifest[M_METHODS])) {
            return new tError(__('Method "%s" not exists !', $method), $this);
        }

        // Test rights
        //if (array_key_exists('right', $manifest[M_METHODS][$method])) {
        if (isset($manifest[M_METHODS][$method][M_RIGHTS])) {

            // Test user
            if (array_key_exists('user', $manifest[M_METHODS][$method][M_RIGHTS]) && !acl::test($manifest[M_METHODS][$method][M_RIGHTS]['user'])) {
                return new tError(__('You do not have the sufficient rights !'), $this);
            }

            // Test rights
            if (array_key_exists('acl', $manifest[M_METHODS][$method][M_RIGHTS]) && !acl::ok($manifest[M_METHODS][$method][M_RIGHTS]['acl'])) {
                return new tError(__('You do not have the sufficient rights !'), $this);
            }
        }

        // Test all parameters
        if (isset($manifest[M_METHODS][$method][M_PARAMS])) {

            foreach ($manifest[M_METHODS][$method][M_PARAMS] as $params) {

                list($name, $obligatory, $type, $description) = $params;

                // Test for parameters obligatory
                if ($obligatory && !array_key_exists($name, $param)) {
                    return new tError(__('Parameter "%s" is not present !', $name), $this);
                }

                // Not obligatory ?
                if (!array_key_exists($name, $param)) {
                    continue;
                }

                // Test for type
                if (strpos($type, ':') !== false) {
                    list($type, $class) = explode(':', $type);
                }

                // Test for pipe and multiple type choice
                $atype = (strpos($type, '|') !== false) ? explode('|', $type) : array($type);
                $test = false;
                foreach ($atype as $type) {
                    switch ($type) {
                        case 'int':     $test = is_numeric($param[$name]);                      break;
                        case 'string':  $test = is_string($param[$name]);                       break;
                        case 'array':   $test = is_array($param[$name]);                        break;
                        case 'obj':     $test = ($class && get_class($param[$name]) == $class); break;
                        case 'file':    $test = $this->isFile($param[$name]);                   break;
                        case 'dir':     $test = $this->isDir($param[$name]);                    break;
                        default:        $test = false;                                          break;
                    }

                    if ($test) {
                        break;
                    }
                }

                if (!$test) {
                    return new tError(__('Parameter "%s" type is not valid !', $name), $this);
                }

                $p[$name] = $param[$name];
            }
        }

        // Ok, run action !
        if (!method_exists($this, $method)) {
            return new tError(__('Method %s not exists !', $method), $this);
        }

        $status = call_user_func_array(array($this, $method), $p);
        if (system::isError($status)) {
            $this->status = $status->msg;
            $this->last_error = $status;
            //return false;
        }

        return $status;
    }

    /**
     *  Test if param is a valid file
     *  @param  string  $file   File to test
     */
    function isFile($file) {
        $object = $this->obj->getInfo($file, false, false);
        return ($object && $object->type == TYPE_FILE ? true : false);
    }

    /**
     *  Test if param is a valid path
     *  @param  string  $path   Path to test
     */
    function isDir($path) {
        $object = $this->obj->getInfo($path, false, false);
        return ($object && $object->type == TYPE_DIR ? true : false);
    }
}

?>
