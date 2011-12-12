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


/*  ToDo
 *  - Adding multiple basket gestion
 */

class plugin_ws_basket extends plugin_ws {

    var $basket;

    /**
     *  Constructor
     */
    function plugin_ws_basket() {
        parent::plugin_ws();
        $this->basket = basket::getInstance();
    }

    /**
     *  Add an object to current basket
     */
    function add($object) {
        $this->basket->add($object);
    }

    /**
     *  Add an object to current basket
     */
    function remove($object) {
        $this->basket->remove($object);
    }

    /**
     *  Get basket content
     */
    function getList() {
        return $this->basket->getList();
    }

    /**
     *  Get basket content
     */
    function getCount() {
        return count($this->getList());
    }

    /**
     *  Clear basket content
     */
    function clear() {
        return $this->basket->clear();
    }
}

?>
