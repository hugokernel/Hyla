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

class tError {
    var $msg;
    var $context;

    function tError($msg, $obj = null) {
        $this->msg = $msg;
        if ($obj) {
            $this->context = $obj->getContext();
        }
    }
}

/*
define('S_SUCCESS', 1);
define('S_ERROR',   2);

class Status {
    var $type;  // S_SUCCESS, S_ERROR
    var $msg;
    var $context;

    function Status($type, $msg = null, $context = null) {
        $this->type = $type;
        $this->msg = $msg;
        $this->context = $context;
    }
}
*/

?>
