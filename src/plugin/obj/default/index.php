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

    You should have received a copy of the GNU General Public License
    along with Hyla; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

class plugin_obj_default extends plugin_obj {   // implements _plugin { plugin implements _plugin {

    function plugin_obj_default($cobj) {
        parent::plugin_obj($cobj);
        $this->tpl->set_root($this->plugin_dir.'default');
        $this->tpl->set_file(array(
                'default'       =>  'default.tpl'));
        $this->tpl->set_block('default', 'plugin_choice', 'Hdlplugin_choice');
    }

    function aff() {
        $tab = parent::getFilePlugins();
        $size = sizeof($tab);
        for($i = 0, $last = null, $last_type = null; $i < $size; $i++) {
            $this->tpl->set_var(array(
                    'PLUGIN_NAME'           =>  $tab[$i]['name'],
                    'PLUGIN_DESCRIPTION'    =>  $tab[$i]['description']
                    ));
            $this->tpl->parse('Hdlplugin_choice', 'plugin_choice', true);
        }

        $this->tpl->set_var(array(
                'OBJECT'            =>  $this->url->linkToCurrentObj(),
                'URL_OBJ_DOWNLOAD'  =>  $this->url->linkToCurrentObj('download'),
                ));

        return $this->tpl->parse('OutPut', 'default');
    }
}

?>
