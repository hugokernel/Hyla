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

class plugin_ws_acl extends plugin_ws {

    /**
     *  Constructor
     */
    function plugin_ws_acl() {
        parent::plugin_ws();
    }

    /**
     *  Adding right
     *  @param  string  $path       Path
     *  @param  int     $user_id    User id
     *  @param  int     $right      Right
     */
    function add($path, $user_id, $right) {
        return $this->obj->addRight($path, $user_id, $right);
    }

    /**
     *  Edit right
     *  @param  string  $path       Path
     *  @param  int     $user_id    User
     *  @param  int     $right      Right
     */
    function edit($path, $user_id, $right) {
        return $this->obj->setRight($path, $user_id, $right);
    }

    /**
     *  Delete right
     *  @param  string  $path       Path
     *  @param  mixed   $user_id    User id
     */
    function del($path, $user_id) {
        return $this->obj->delRight($path, $user_id);
    }
}

?>
