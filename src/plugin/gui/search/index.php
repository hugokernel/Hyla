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

class plugin_gui_search extends plugin_gui {

    function plugin_gui_search() {
        parent::plugin_gui();
    }

    function aff() {

        $cobj = $this->obj->getCurrentObj();

        $conf = conf::getInstance();

        $this->tpl->set_file('search', 'tpl/search.tpl');
        $this->tpl->set_block('search', array(
            'line'      =>  'Hdlline',
            'result'    =>  'Hdlresult',
        ));

        if (isset($_POST['word']) && !empty($_POST['word'])) {

            $scandir = (isset($_POST['scandir'])) ? true : false;
            $recurs = (isset($_POST['recurs'])) ? true : false;

            $tab = file::searchFile($cobj->path, $_POST['word'], $recurs, FOLDER_ROOT, $scandir, $conf->get('view_hidden_file'));
            $tab = $this->obj->getDirContent(null, null, 0, 10000, $tab);

            if ($tab) {
                $size = sizeof($tab);
                for($i = 0; $i < $size; $i++) {
                    $this->tpl->set_var(array(
                            'PATH_INFO'         =>  $this->url->linkToObj($tab[$i]->file),
                            'FILE_ICON'         =>  $tab[$i]->icon,
                            'FILE_NAME'         =>  $tab[$i]->name,
                            'FILE_SIZE'         =>  ($tab[$i]->type == TYPE_FILE) ? get_human_size_reading($tab[$i]->size) : '&nbsp;',
                            'PATH'              =>  $tab[$i]->path,
                            'PATH_FORMAT'       =>  format($tab[$i]->file, false),
                            'FILE_DESCRIPTION'  =>  string::cut(eregi_replace("<br />", " ", $tab[$i]->info->description), 90)));
                    $this->tpl->parse('Hdlline', 'line', true);
                }

                $this->tpl->parse('Hdlresult', 'result', true);
            } else {
                $this->tpl->set_var('ERROR', view_error(__('No result')));
            }

            $this->tpl->set_var(array(
                    'SCANDIR_CHECKED'   =>  isset($_POST['scandir']) ? 'checked="checked"' : null,
                    'RECURS_CHECKED'    =>  isset($_POST['recurs']) ? 'checked="checked"' : null,
                    'WORD'              =>  strip_tags($_POST['word']),
//                    'SEARCH_RESULT'     =>  __('Search results for &laquo; %s &raquo; :', strip_tags($_POST['word']))
                    ));

          $this->l10n->setStr(null, 'Search results for &laquo; %s &raquo; :', strip_tags($_POST['word']));
        }

        $this->tpl->set_var('FORM_SEARCH', $this->url->linkToCurrentObj('search'));

        $this->l10n->setStr(null, 'Search in &laquo; %s &raquo;', $cobj->path);

        return $this->tpl->parse('OutPut', 'search');
    }
}

?>
