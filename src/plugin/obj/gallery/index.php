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

class plugin_obj_gallery extends plugin_obj { // implements _plugin {

    function plugin_obj_gallery($cobj) {
        parent::plugin_obj($cobj);

        $this->tpl->set_root($this->plugin_dir.'gallery');
        $this->tpl->set_file('gallery', 'gallery.tpl');

        $this->tpl->set_block('gallery', array(
                'gallery_comment'       =>  'Hdlgallery_comment',
                'gallery_col_img'       =>  'Hdlgallery_col_img',
                'gallery_col_other'     =>  'Hdlgallery_col_other',
                'gallery_colspan'       =>  'Hdlgallery_colspan',
                'gallery_col'           =>  'Hdlgallery_col',
                'gal_line'              =>  'Hdlgal_line'
                ));
    }

    function aff($paff) {

        global $sort, $start;

        $this->addStyleSheet('default.css');

        $img_per_line = $this->getConfVar('img_per_line');
        $img_width = $this->getConfVar('img_width');
        $img_height = $this->getConfVar('img_height');

        $sort = isset($_SESSION['sess_sort']) ? $_SESSION['sess_sort'] : (SORT_NAME_ALPHA | SORT_FOLDER_FIRST);

        $tab = $this->obj->getDirContent($this->cobj->file, $sort, $start);

        if ($tab) {

            // Listage de répertoire
            $size = sizeof($tab);
            for ($i = 0, $cmpt = 1, $cmp_line = 0; $i < $size; $i++, $cmpt++) {

                if ($tab[$i]->name == '..') {
                    $cmpt--;
                    continue;
                }

                $cmp_line++;

                $this->tpl->set_var('Hdlgallery_col_img');
                $this->tpl->set_var('Hdlgallery_col_other');
                $this->tpl->set_var('Hdlgallery_comment');
                $this->tpl->set_var('Hdlgallery_colspan');

                $this->tpl->set_var(array(
                        'FILE_NAME'         =>  view_obj($tab[$i]->name),

                        'FILE_ICON'         =>  $tab[$i]->icon,
                        'FILE_SIZE'         =>  get_human_size_reading($tab[$i]->size),
                        'PATH'              =>  $this->url->linkToObj($tab[$i]->file),
                        'OBJECT_MINI'       =>  $this->url->linkToObj($tab[$i]->file, array('mini', $img_width, $img_height)),
                        'NBR_COMMENT'       =>  $tab[$i]->info->nbr_comment,
                        'FILE_DESCRIPTION'  =>  ($tab[$i]->info->description) ? string::cut(eregi_replace("<br />", " ", $tab[$i]->info->description), 90) : __('No description !')
                        ));

                if ($tab[$i]->info->nbr_comment)
                    $this->tpl->parse('Hdlgallery_comment', 'gallery_comment', true);

                if ($tab[$i]->extension == 'jpg' || $tab[$i]->extension == 'jpeg' || $tab[$i]->extension == 'gif' || $tab[$i]->extension == 'png')
                    $this->tpl->parse('Hdlgallery_col_img', 'gallery_col_img', true);
                else
                    $this->tpl->parse('Hdlgallery_col_other', 'gallery_col_other', true);

                $this->tpl->parse('Hdlgallery_col', 'gallery_col', true);

                if (!($cmpt % $img_per_line)) {
                    $cmp_line = 0;
                    $this->tpl->parse('Hdlgal_line', 'gal_line', true);
                    $this->tpl->set_var('Hdlgallery_col');
                }
            }

            $this->tpl->parse('Hdlgal_line', 'gal_line', true);

            return $this->tpl->parse('OutPut', 'gallery');
        }
    }
}

?>
