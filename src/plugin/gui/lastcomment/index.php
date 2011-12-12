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

class plugin_gui_lastcomment extends plugin_gui {

    function plugin_gui_lastcomment() {
        parent::plugin_gui();
    }

    function aff() {

        $this->tpl->set_file('lastcomment', 'tpl/lastcomment.tpl');
        $this->tpl->set_block('lastcomment', array(
            'line'  =>  'Hdlline',
        ));

        $act = plugins::get(PLUGIN_TYPE_WS, 'comment');
        $tab = $act->run('getlast');

        if (system::isError($tab)) {
            return $tab;
        }

        $size = count($tab);
        for ($i = 0; $i < $size; $i++) {
            $this->tpl->set_var(array(
                    'ID'            =>  $tab[$i]->id,
                    'PATH_FORMAT'   =>  format($tab[$i]->object, false),
                    'FILE_ICON'     =>  $tab[$i]->icon,
                    'PATH_INFO'     =>  $this->url->linkToObj($tab[$i]->object),
                    'COMMENT'       =>  $tab[$i]->content,
                    'AUTHOR'        =>  $tab[$i]->author,
                    'EMAIL'         =>  $tab[$i]->mail,
                    'URL'           =>  ($tab[$i]->url ? $tab[$i]->url : null),
                    'DATE'          =>  format_date($tab[$i]->date, 1),
                    ));
            $this->tpl->parse('Hdlline', 'line', true);
        }

        $this->tpl->set_var('MSG', (!$size) ? __('There are no comments !') : (($size > 1) ? __('Comments from most recent to oldest.') : null));

        return $this->tpl->parse('OutPut', 'lastcomment');
    }
}

?>
