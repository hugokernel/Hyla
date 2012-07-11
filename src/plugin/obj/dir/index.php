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

class plugin_obj_dir extends plugin_obj {

    function plugin_obj_dir($cobj) {
        parent::plugin_obj($cobj);
        $this->tpl->set_root($this->plugin_dir.'dir');
        $this->tpl->set_file(array(
                'dir'   =>  'dir.tpl'));
        $this->tpl->set_block('dir', array(
                'line_comment'  =>  'Hdlline_comment',
                'line_header'   =>  'Hdlline_header',
                'line_content'  =>  'Hdlline_content',
                'line'          =>  'Hdlline',
                ));
    }

    function aff($aff) {

        global $sort, $start, $conf;

        $this->addStyleSheet('default.css');

        $sort = $_SESSION['sess_sort'];
        $grp = $_SESSION['sess_grp'];

        switch ($sort) {
            case SORT_DEFAULT:
            case SORT_NAME_ALPHA:
            case SORT_NAME_ALPHA | SORT_FOLDER_FIRST:
            case SORT_NAME_ALPHA_R:
            case SORT_NAME_ALPHA_R | SORT_FOLDER_FIRST:
                $header_value = 'return $tab[$i]->name{0};';
                break;
            case SORT_EXT_ALPHA:
            case SORT_EXT_ALPHA | SORT_FOLDER_FIRST:
            case SORT_EXT_ALPHA_R:
            case SORT_EXT_ALPHA_R | SORT_FOLDER_FIRST:
                $header_value = 'return $tab[$i]->extension;';
                break;
            case SORT_CAT_ALPHA:
            case SORT_CAT_ALPHA | SORT_FOLDER_FIRST:
            case SORT_CAT_ALPHA_R:
            case SORT_CAT_ALPHA_R | SORT_FOLDER_FIRST:
                $header_value = 'return $tab[$i]->cat;';
                break;
            case SORT_SIZE:
            case SORT_SIZE | SORT_FOLDER_FIRST:
            case SORT_SIZE_R:
            case SORT_SIZE_R | SORT_FOLDER_FIRST:
                $header_value = 'return get_human_size_reading($tab[$i]->size, 0);';
                break;
        }

        $tab = $this->obj->getDirContent($this->cobj->file, $sort, $start);
        if ($tab) {
            // Listage de répertoire
            $size = sizeof($tab);
            for($i = 0, $last = null, $last_type = null; $i < $size; $i++) {
                $this->tpl->set_var('Hdlline_header');
                $this->tpl->set_var('Hdlline_content');
                $this->tpl->set_var('Hdlline_comment');

                $this->tpl->set_var(array(
                        'FILE_NAME'         =>  view_obj($tab[$i]->name),

                        'FILE_ICON'         =>  $tab[$i]->icon,
                        'FILE_SIZE'         =>  ($tab[$i]->type == TYPE_FILE) ? get_human_size_reading($tab[$i]->size) : ' ',
                        'PATH_DOWNLOAD'     =>  $this->url->linkToObj($tab[$i]->file, 'download'),
                        'PATH_INFO'         =>  $this->url->linkToObj($tab[$i]->file),
                        'FILE_DESCRIPTION'  =>  ($tab[$i]->info->description ? string::cut(eregi_replace("<br />", " ", string::unFormat($tab[$i]->info->description)), 90) : null),
                        'NBR_COMMENT'       =>  $tab[$i]->info->nbr_comment,
                        ));

                if ($tab[$i]->info->nbr_comment) {
                    $this->tpl->parse('Hdlline_comment', 'line_comment', true);
                }

                // Utilisé pour le groupage par catégorie
                if ($grp == 1) {
                    $rupt = eval($header_value);
                    if ($sort & SORT_CAT_ALPHA || $sort & SORT_CAT_ALPHA_R)
                        $this->tpl->set_var('HEADER_INFO_VALUE', (($tab[$i]->type == TYPE_FILE) ? $rupt : __('Dir(s) ')));
                    else {
                        $this->tpl->set_var(array(
                                'HEADER_VALUE'      =>  (($tab[$i]->type == TYPE_DIR) ? __('Dir(s) ') : __('File(s) ')),
                                'HEADER_INFO_VALUE' =>  (($tab[$i]->type == TYPE_FILE) ? $rupt : null)));
                    }
                    $bool = (($tab[$i]->type == 1) && ((SORT_FOLDER_FIRST | $sort) && $last_type == $tab[$i]->type));
                    if (!$bool && ($last_type != $tab[$i]->type || strtolower($last) != strtolower($rupt))) {
                        $this->tpl->parse('Hdlline_header', 'line_header', true);
                        $last = $rupt;
                        $last_type = $tab[$i]->type;
                    }
                }
                $this->tpl->parse('Hdlline_content', 'line_content', true);
                $this->tpl->parse('Hdlline', 'line', true);
            }

            return $this->tpl->parse('OutPut', 'dir');
        }
    }
}

?>
