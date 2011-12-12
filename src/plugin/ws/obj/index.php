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

class plugin_ws_obj extends plugin_ws {

    var $bdd;

    /**
     *  Constructor
     */
    function plugin_ws_obj() {
        parent::plugin_ws();

        $this->bdd = plugins::get(PLUGIN_TYPE_DB);
    }

    /**
     *  Get description
     *  @param  string  $file       File
     *  @param  bool    $formated   Return data formated or not
     */
    function getDescription($file, $formated = false) {
        $object = $this->obj->getInfo($file, true, false);
        return ($formated) ? string::format($object->info->description) : $object->info->description;
    }

    /**
     *  Set description
     *  @param  string  $file           File
     *  @param  string  $description    Description
     */
    function setDescription($file, $description) {

        $object = $this->obj->getInfo($file, false, false);

        // Test right
        if (!acl::ok($object, AC_EDIT_DESCRIPTION)) {
            return new tError(__('You do not have the sufficient rights !'), $this);
        }

        $id = $this->obj->getId($object->file);
//        $description = string::format($description, true, true);
        $qry = "UPDATE ".TABLE_OBJECT." SET obj_description = '$description', obj_date_last_update = '".system::time()."' WHERE obj_id = '$id'";
        if (!$this->bdd->execQuery($qry)) {
            return new tError(__('Error while setting description !'), $this);
        }

        return $description;
    }

    /**
     *  Set icon
     *  @param  string  $file   File
     *  @param  string  $icon   Icon
     */
    function setIcon($file, $icon = null) {

        $object = $this->obj->getInfo($file, false, false);

        // Test right
        if (!acl::ok($object, AC_EDIT_ICON)) {
            return new tError(__('You do not have the sufficient rights !'), $this);
        }

        // Only dir !
        if ($object->type != TYPE_DIR) {
            return new tError(__('Only dir !'), $this);
        }

        // Not root dir !
        if ($object->file == '/') {
            return new tError(__('Not on root dir !'), $this);
        }

        $id = $this->obj->getId($object->file);
        $qry = "UPDATE ".TABLE_OBJECT." SET obj_icon = '$icon', obj_date_last_update = '".system::time()."' WHERE obj_id = '$id'";
        if (!$this->bdd->execQuery($qry)) {
            return new tError(__('Error while setting icon !'), $this);
        }

        return true;
    }

    /**
     *  Set plugin
     *  @param  string  $file   File
     *  @param  string  $plugin Plugin
     */
    function setPlugin($file, $plugin = null) {

        $object = $this->obj->getInfo($file, false, false);

        // Test right
        if (!acl::ok($object, AC_EDIT_PLUGIN)) {
            return new tError(__('You do not have the sufficient rights !'), $this);
        }

        // Only dir !
        if ($object->type != TYPE_DIR) {
            return new tError(__('Not exist !'), $this);
        }

        // Test plugin
        if (!plugins::isValid($plugin, PLUGIN_TYPE_OBJ)) {
            return new tError(__('Plugin "%s" not exist !', $plugin), $this);
        }

        $id = $this->obj->getId($object->file);
        $qry = "UPDATE ".TABLE_OBJECT." SET obj_plugin = '$plugin', obj_date_last_update = '".system::time()."' WHERE obj_id = '$id'";
        if (!$this->bdd->execQuery($qry)) {
            return new tError(__('Error while setting plugin !'), $this);
        }

        return true;
    }
}

?>
