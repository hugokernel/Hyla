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

class plugin_obj_slideshow extends plugin_obj {

    function plugin_obj_slideshow($cobj) {
        parent::plugin_obj($cobj);

        $this->tpl->set_root($this->plugin_dir.'slideshow');
        $this->tpl->set_file('slideshow', 'slideshow.tpl');

        $this->tpl->set_block('slideshow', array(
                'description'       =>  'Hdldescription',

                'image_cache'       =>  'Hdlimage_cache',
                'image_thumb'       =>  'Hdlimage_thumb',

                'mode_manual'       =>  'Hdlmode_manual',
                'previous_slide'    =>  'Hdlprevious_slide',
                'next_slide'        =>  'Hdlnext_slide',
                'header_mode_auto'  =>  'Hdlheader_mode_auto',
                'mode_auto'         =>  'Hdlmode_auto',

                'aff'               =>  'Hdlaff',
                ));
    }

    function fullscreen($paff) {

        global $obj, $conf;

        $filter = null;

        // Lecture de la configuration du plugin
        $view_item = $this->getConfVar('view_item');
        $timeout = $this->getConfVar('timeout');

        if ($view_item) {
            $filter = ($view_item == 1) ? array('=', 'type', TYPE_FILE) : array('=', 'cat', __('Image(s)'));
        }

        $start = $this->url->getParam('aff', 2);
        if (!$start)
            $start = 0;

        $tab = $this->obj->getDirContent($this->cobj->file, $_SESSION['sess_sort'], $start, 3, -1, $filter);

        if ($tab) {
            // Listage de répertoire
            $size = sizeof($tab);
            for($i = 1, $last = null, $last_type = null; $i < $size; $i++) {
                $this->tpl->set_var(array(
                        'IMAGE_CACHE'       =>  $this->url->linkToObj($tab[$i]->file, array('mini', 800)),
                        ));
                $this->tpl->parse('Hdlimage_cache', 'image_cache', true);
            }

            if ($paff) {
                list($act, $val) = explode(':', $paff);
                if ($act == 'mode')
                    $mode = $val;
                $this->saveVar('mode', $val);
            } else
                $mode = $this->getVar('mode');

            if ($mode == 'auto')
                $this->tpl->parse('Hdlmode_auto', 'mode_auto', true);
            else
                $this->tpl->parse('Hdlmode_manual', 'mode_manual', true);

            if ($tab[0]->extension == 'jpg' || $tab[0]->extension == 'jpeg' || $tab[0]->extension == 'gif' || $tab[0]->extension == 'png') {
                $this->tpl->set_var('IMAGE', $this->url->linkToObj($tab[0]->file, array('mini', 800), array('force', 'slideshow')));
                $this->tpl->parse('Hdlimage_thumb', 'image_thumb', true);
            }

            if ($tab[0]->info->description) {
                $this->tpl->set_var('DESCRIPTION', $tab[0]->info->description);
                $this->tpl->parse('Hdldescription', 'description', true);
            }

            $this->tpl->set_var(array(

                    'NAME'          =>  view_obj($tab[0]->name),
                    'OBJECT'        =>  view_obj($this->cobj->file),

                    'PREV_IMAGE'    =>  $this->url->linkToCurrentObj(array('start', ($start - 1)), array('force', 'slideshow')),
                    'NEXT_IMAGE'    =>  $this->url->linkToCurrentObj(array('start', ($start + 1)), array('force', 'slideshow')),

                    'FILE_ICON'     =>  $tab[0]->icon,

                    'URL_DOWNLOAD'  =>  $this->url->linkToObj($tab[0]->file, 'download'),
                    'URL_STOP'      =>  $this->url->linkToCurrentObj(),
                    'URL_AUTO'      =>  $this->url->linkToCurrentObj(array('start', $start), array('force', 'slideshow'), null, 'mode:auto'),
                    'URL_MANUAL'    =>  $this->url->linkToCurrentObj(array('start', $start), array('force', 'slideshow'), null, 'mode:manual'),

                    'PATH_2_PLUGIN' =>  $this->_url_2_plugin,

                    'TITLE'         =>  $conf['title'],
                    
                    'STYLESHEET'    =>  get_css(),
                    ));

            $nbr_obj = $obj->getNbrObject();

            if ($start > 0)
                $this->tpl->parse('Hdlprevious_slide', 'previous_slide', true);

            if ($start + 1 < $nbr_obj) {
                $this->tpl->parse('Hdlnext_slide', 'next_slide', true);
                if ($mode == 'auto') {
                    $this->tpl->set_var('TIMEOUT', $timeout);
                    $this->tpl->parse('Hdlheader_mode_auto', 'header_mode_auto', true);
                }
            }

            $this->tpl->parse('Hdlaff', 'aff', true);
        } else {
            $this->tpl->set_var('MESSAGE', __(($view_item == 2 ? 'There are no image !' : 'There are no file !')));
        }
        
        $var_tpl = $this->tpl->parse('OutPut', 'slideshow');
        print($this->tpl->finish($var_tpl));
        return true;
    }
}

?>
