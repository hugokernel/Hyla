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

class plugin_obj_mp3 extends plugin_obj {

    function plugin_obj_mp3($cobj) {
        parent::plugin_obj($cobj);
        $this->tpl->set_root($this->plugin_dir.'mp3');
        $this->tpl->set_file('mp3', 'mp3.tpl');
    }

    function aff() {
        global $cobj;

        $auto_play = $this->getConfVar('auto_play');
        $loop = $this->getConfVar('loop');

        $param = '?';
        $param .= ($auto_play ? 'autoplay=1&amp;' : 'autoplay=0&amp;');
        $param .= ($loop ? 'loop=1' : 'loop=0');

        $this->tpl->set_var(array(
                'SIZE'              =>  get_human_size_reading($cobj->size),
                'PATH_2_PLUGIN'     =>  $this->_url_2_plugin,
                'OBJECT_PARAM'      =>  $param,
                'OBJECT_DOWNLOAD'   =>  $this->url->linkToCurrentObj('download'),
                ));

        return $this->tpl->parse('OutPut', 'mp3');
    }
}

?>
