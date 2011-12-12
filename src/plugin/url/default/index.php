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

class plugin_url_default extends plugin_url {

    /*  Constructor
     */
    function plugin_url_default() {
        parent::plugin_url();
    }

    /*  Scan url
        @param  array   $url    Array ($_REQUEST or other)
        @return tUrl object
     */
    function scan($url) {

        $ret = new tUrl;

        if (array_key_exists('p', $url)) {
            @list($ret->aff, $ret->obj) = explode(',', $url['p'], 2);
            $ret->aff = explode('-', $ret->aff);
        }

        if (array_key_exists('act', $url)) {
            $ret->act = explode('-', $url['act']);
        }
        
        // Load arg
        foreach ($_GET as $key => $value) {
            if ($key == 'p') {
                continue;
            }
            $ret->arg[$key] = $value;
        }
        
        return $ret;
    }

    /*  Get an url
        @param  mixed   $object Object
        @param  array   $aff    Page
        @param  array   $act    Action
        @return Url
     */
    function get($object, $aff = null, $act = null, $args = null) {

        $url = null;
        
        if ($this->absolute) {
            $url .= system::getHost();
        }
        
        $url .= $this->root_url.'index.php';

        // Obj
        $aff = $aff ? implode('-', $aff) : null;
        $url .= '?p='.$aff;

        // Insert object
        if ($object) {
            $url .= ','.$this->encode($object);
        }

        // Action
        if ($act) {
            $url .= '&amp;act='.implode('-', $act);
        }
        
        // Arg
        if ($args) {
            foreach ($args as $key => $value) {
                $url .= '&amp;'.$key.'='.$value;
            }
//            $url .= implode('&amp;', $arr);
        }

        return $url;
    }
}

?>