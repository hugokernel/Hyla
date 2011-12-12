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

class plugin_gui_rename extends plugin_gui {

    /**
     *  Constructor
     */
    function plugin_gui_rename() {
        parent::plugin_gui();

        $this->events['onsuccess'] =    array(
                                            'msg'       =>  __('The objet was renamed !'),
                                            'redirect'  =>  'current'
                                        );
    }

    function act() {
        global $cobj;
        $ret = false;

        if ($_POST) {
            $act = plugins::get(PLUGIN_TYPE_WS, 'fs');
            if ($act->run('rename', array('path' => $cobj->file, 'new_name' => $_POST['new_name']))) {
                $ret = true;
            }

            if ($act->status) {
                $this->last_error_msg = $act->status;
            }
        }

        return $ret;
    }

    function aff() {

        $cobj = $this->obj->getCurrentObj();

        $this->tpl->set_file('rename', 'tpl/rename.tpl');

        $this->tpl->parse('Hdlrow', 'row', true);

        $this->tpl->set_var(array(
            'CURRENT_NAME'  =>  view_obj($cobj->name),
            'FORM_RENAME'   =>  $this->url->linkToCurrentObj('rename'),
            'ERROR'         =>  (isset($this->last_error_msg)) ? view_error($this->last_error_msg) : null,
        ));

        return $this->tpl->parse('OutPut', 'rename');
    }
}

?>
