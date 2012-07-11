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

class plugin_obj_pdf extends plugin_obj {

    function plugin_obj_pdf($cobj) {
        parent::plugin_obj($cobj);

        $this->tpl->set_root($this->plugin_dir.'pdf');
        $this->tpl->set_file('pdf', 'pdf.tpl');
    }

    function aff() {
        $this->addStyleSheet('pdf.css');

        $content = file::getContent($this->real_file);
        
        $this->tpl->set_var('PATH_2_PLUGIN', $this->_url_2_plugin);
        $this->tpl->set_var('URL_CURRENT_OBJ', $this->url->linkToCurrentObj('download'));

        return $this->tpl->parse('OutPut', 'pdf');
    }
}

?>
