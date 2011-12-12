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

class plugin_url_pathinfo extends plugin_url {

    /*  Constructor
     */
    function plugin_url_pathinfo() {
        parent::plugin_url();
    }

    /*  Scan url
        @param  array   $url    Array ($_REQUEST)
        @return tUrl object
     */
    function scan($url) {

        $ret = new tUrl;

        if (array_key_exists('PATH_INFO', $_SERVER) && $_SERVER['PATH_INFO']) {
            @list($ret->aff, $ret->obj) = explode('/', substr($_SERVER['PATH_INFO'], 1), 2);
//            @list($ret->obj, $tmp) = explode('&', $ret->obj, 2);

            // Scan for parameter in url query
            if (preg_match('#&[a-z]+=[a-z0-9-]+#i', $ret->obj, $match)) {
                $ret->obj = substr($ret->obj, 0, strpos($ret->obj, $match[0]));
            }    

            $ret->obj = '/'.urldecode($ret->obj);
            $ret->aff = explode('-', $ret->aff);

            parse_str($_SERVER['PATH_INFO'], $request);
            $ret->act = isset($request['act']) ? explode('-', $request['act']) : null;
            
            $_GET = array_merge($_GET, $request);
            $_REQUEST = array_merge($_REQUEST, $request);
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
        $url .= '/'.$aff;

//dbug(func_get_args());

        // Arg
        /*
        if ($args) {
            //dbug($args);
            $url .= '|';
            foreach ($args as $key => $value) {
                $arr[] = $key.':'.$value;
            }
            $url .= implode('|', $arr);
            $url .= '|';
        }
        */

        // Insert object
        if ($object) {
            $url .= $this->encode($object);
        }

        // Action
        if ($act) {
            $url .= '&amp;act='.implode('-', $act);
        }

        return $url;
    }
}

?>
