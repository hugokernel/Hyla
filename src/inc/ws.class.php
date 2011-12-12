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

define('WS_DOMAIN', 'hyla');

class ws
{
    /**
     *  Get all ws methods
     *  @param  bool    $param  Get argument method
     *  @access static
     */
    function getMethods($getarg = true) {
        
        $methods = array();
        
        $plugin_dir = plugins::getDirFromType(PLUGIN_TYPE_WS);
        $hdl = dir($plugin_dir);
        if (!$hdl) {
            return $methods;
        }

        while (false !== ($dir = $hdl->read())) {
            $manifest = plugin::getManifestFile(PLUGIN_TYPE_WS, $dir);
            if (!$manifest) {
                continue;
            }
dbug($manifest);
continue;
            foreach ($manifest as $method => $param) {
                if ($getarg) {
//                    $methods[WS_DOMAIN.'.'.$dir.'.'.$method] = $param['param'];
                    $methods[$dir][$method] = $param[M_PARAMS];
                } else {
                    $methods[] = WS_DOMAIN.'.'.$dir.'.'.$method;
                }
            }
        }
        
        return $methods;
    }

    /**
     *  Run web service
     *  @param  string  $ws     Web service
     *  @param  string  $param  Param
     *  @access static
     */
    function run($ws, $param) {
        
        list($domain, $group, $name) = explode('.', $ws);
        
        // Test base name
        if ($domain != WS_DOMAIN) {
            return new tError(__('Error, base name must be "%s" !', WS_DOMAIN));
        }

        // Ok, go !
        $act = plugins::get(PLUGIN_TYPE_WS, $group);
        if (!$act) {
            return new tError(__('Unknow group name "%s" !', $group));
        }

        return $act->run($name, $param);
    }
}

?>
