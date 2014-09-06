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

    You should have received a copy of the GNU General Public Licensetod
    along with Hyla; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

class dcache {

    private $data;

    public function __construct() {
        $this->data = array();
    }

    /*  Adding key / value pair in context
        @param  mixed   $string Context
        @param  mixed   $key    The key
        @param  mixed   $value  Value
     */
    public function add($context, $key, $value) {
        $this->data[$context][$key] = $value;
        return $this->data[$context][$key];
    }

    /*  Get
        @param  mixed   $string Context
        @param  mixed   $key    The key
     */
    public function get($context, $key) {
        $ret = null;
        if (array_key_exists($context, $this->data)) {
            if (array_key_exists($key, $this->data[$context])) {
                $ret = $this->data[$context][$key];
            }
        }
        return $ret;
    }
}
