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

class plugin_gui_maintenance extends plugin_gui {

    function plugin_gui_maintenance() {
        parent::plugin_gui();
    }

    function act() {
        switch ($this->url->getParam('aff', 3)) {
            case 'freecache':
                $act = plugins::get(PLUGIN_TYPE_WS, 'util');
                $act->run('freeCache');
                $this->last_status = __('Cache was purged !');
                break;

            case 'syncbdd':
                $act = plugins::get(PLUGIN_TYPE_WS, 'util');
                $var = $act->run('syncBdd');
                $this->last_status = __('%s objet(s) was deleted from database !', $var);
                break;
        }
    }

    function aff() {

        // Declare tpl
        $this->tpl->set_file('maintenance', 'tpl/maintenance.tpl');

        $this->tpl->set_var(array(
            'MSG_STATUS'                    =>  ($this->last_status) ? $this->last_status : null,

            'URL_PAGE_MAINTENANCE_PURGE'    =>  $this->url->linkToPage(array('admin','maintenance', 'freecache')),
            'URL_PAGE_MAINTENANCE_SYNC'     =>  $this->url->linkToPage(array('admin','maintenance', 'syncbdd')),
        ));

        return $this->tpl->parse('OutPut', 'maintenance');
    }
}

?>
