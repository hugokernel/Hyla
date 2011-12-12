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

class plugin_gui_admin extends plugin_gui {

    function plugin_gui_admin() {
        parent::plugin_gui();
    }

    function act() {

    }

    function aff() {

        global $url;

        // Declare tpl
        $this->tpl->set_file('admin', 'tpl/admin.tpl');
        $this->tpl->set_block('admin', array(
            'plugin_current'    =>  'Hdlplugin_current',
            'plugin'            =>  'Hdlplugin',
        ));

        $this->tpl->l10n->setFile('admin.php');

        plugins::get(PLUGIN_TYPE_GUI);
        $plugins = plugin_gui::getPlugin('admin');

        // Get good plugin
        $cplugin = $url->getParam('aff', 2);
        if (!plugins::search($cplugin, PLUGIN_TYPE_GUI)) {
            $cplugin = 'home';
        }

        // Create toolbar
        $dir = plugins::getDirFromType(PLUGIN_TYPE_GUI);
        foreach ($plugins as $name => $manifest) {

            $this->tpl->set_var('Hdlplugin_current');

            // Current plugin ?
            if ($cplugin == $name) {
                $this->tpl->parse('Hdlplugin_current', 'plugin_current', true);
            }

            $this->tpl->set_var(  array(
                'URL_PLUGIN'            =>  $url->linkToPage(array('admin', $name)),
                'PLUGIN_NAME'           =>  __($manifest->name),
                'PLUGIN_DESCRIPTION'    =>  __($manifest->description),
                'PLUGIN_ICON'           =>  HYLA_ROOT_URL.$dir.$name.'/icon.png',
                )
            );
            $this->tpl->parse('Hdlplugin', 'plugin', true);
        }

        // Run plugin
        $p = plugins::get(PLUGIN_TYPE_GUI, $cplugin);
        if ($p) {
            $this->tpl->set_var('CONTENT', $p->run());
        }

        if ($p->last_error) {
            $this->tpl->set_var('MSG_ERROR', view_error($p->last_error));
        }

        //$this->tpl->set_var('CURRENT_DESCRIPTION', '??');

        return $this->tpl->parse('OutPut', 'admin');
    }
}

?>
