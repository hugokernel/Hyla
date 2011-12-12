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

class tDataSource {
    var $name;      // Name
    var $callback;  // Callback
    var $param;     // Param

    function tDataSource() {
        $this->name = null;
        $this->callback = null;
        $this->param = null;
    }
}

class datasrc
{
    var $data;

    var $current_id;

    /**
     *  Constructor
     */
    function datasrc() {
        $this->data = array();
        $this->current_id = 0;
    }

    /**
     *  Register data source
     *  @param  string  $name       Name of data source
     *  @param  mixed   $callback   Callback
     *  @param  mixed   $param      Param
     */
    function register($name, $callback, $param = null) {

        $dsrc = new tDataSource;
        $dsrc->name = $name;
        $dsrc->callback = $callback;

        if ($param) {
            $dsrc->param = (is_array($param) ? $param : array($param));
        }

        $this->data[] = $dsrc;

        return (count($this->data) - 1);
    }

    /**
     *  Set current id
     *  @param  int $id Identifier
     */
    function setCurrent($id) {
        $this->current_id = $id;
    }
    
    /**
     *  Get current
     */
    function getCurrent() {
        return $this->current_id;
    }

    /**
     *  Get data from resource identifier
     *  @param  string  $path   Path
     */
    function get($path) {
        $param = array($path);
        if ($this->data[$this->current_id]->param) {
            $param = array_merge($param, $this->data[$this->current_id]->param);
        }
        return call_user_func_array($this->data[$this->current_id]->callback, $param);
    }
}

/*
function toto() {
    return array('/toto', '/tata');
}

$ds = new datasrc();
$id = $ds->register('root', 'toto');
echo $ds->get($id);

echo '<hr>';
$id = $ds->register('root', array('datasrc', 'toto'));
echo $ds->get($id);

echo '<hr>';
$id = $ds->register('root', array('datasrc', 'toto'));
echo $ds->get($id);
*/

?>
