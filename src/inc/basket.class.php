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

class basket {

    var $current_id;

    var $baskets;

    var $default_name;

    function basket() {
        $this->current_id = 0;
        $this->default_name = 'default';
        $this->create($this->default_name);
    }

    /**
     *  Singleton
     */
    function &getInstance() {
        static $thisInstance = null;
        if (is_null($thisInstance)) {
            $thisInstance = new basket();
        }
        return $thisInstance;
    }

    /**
     *  Set current basket
     *  @param  int $current_id Basket id
     */
    function setCurrent($current_id) {
        return $this->current_id = $current_id;
    }

    /**
     *  Create new basket
     *  @param  string  $name Basket name
     *  @return Basket id
     */
    function create($name) {
        $this->baskets[] = array('name' => $name, 'content' =>  array());
        return $this->setCurrent(count($this->baskets) - 1);
    }

    /**
     *  Add in basket
     *  @param  string  $content    Content to add
     */
    function add($content) {
        $this->baskets[$this->current_id]['content'][] = $content;
    }

    /**
     *  Remove from basket
     */
    function remove($content) {
        foreach ($this->baskets[$this->current_id]['content'] as $key => $value) {
            if ($value == $content) {
                unset($this->baskets[$this->current_id]['content'][$key]);
                break;
            }
        }
    }

    /**
     *  Get list
     */
    function getList() {
        return $this->baskets[$this->current_id]['content'];
    }

    /**
     *  Set basket name
     */
    function setName($name) {
        return $this->baskets[$this->current_id]['name'] = $name;
    }

    /**
     *  Clear content basket
     */
    function clear() {
        if ($this->current_id == 0) {
            $this->baskets[0] = array('name' => 'default', 'content' =>  array());
        } else
            $this->baskets[$this->current_id] = array();
    }

    /**
     *  Save basket
     */
    function restore() {
        if (array_key_exists('sess_basket', $_SESSION)) {
            $this->baskets = unserialize($_SESSION['sess_basket']);
        }
    }

    /**
     *  Save basket
     */
    function save() {
        $_SESSION['sess_basket'] = serialize($this->baskets);
    }

    /**
     *  Get name of current basket
     */
    function getCurrentName() {
        return $this->baskets[$this->current_id]['name'];
    }
}

/*
$bsk = basket::getInstance();
echo $bsk->create('default');

$bsk->add('toto');
$bsk->add('tata');

//$bsk->remove('toto');

$bsk->setName('coucou');
$bsk->clear();

echo '<pre>';
print_r($bsk->getList());
print_r($bsk->content);
*/
?>
