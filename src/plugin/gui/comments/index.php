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

class plugin_gui_comments extends plugin_gui {

    function plugin_gui_comments() {
        parent::plugin_gui();
    }

    function act() {

        // Comment deletion
        if (($this->url->getParam('aff', 3)) == 'del') {
            if (!($id = intval($this->url->getParam('aff', 4)))) {
                $id = $_POST['comment_id'];
            }
            $act = plugins::get(PLUGIN_TYPE_WS, 'comment');
            $act->run('del', array('id' => $id));
        }
    }

    function aff() {

        $this->tpl->set_file('comments', 'tpl/comments.tpl');
        $this->tpl->set_block('comments', array(
            'block_comment_line'        =>  'Hdlblock_comment_line',
        ));

        $act = plugins::get(PLUGIN_TYPE_WS, 'comment');
        $tab = $act->run('getlast');

        $size = count($tab);
        for ($i = 0; $i < $size; $i++) {
            $this->tpl->set_var(array(
                        'FILE_ICON'         =>  $tab[$i]->icon,
                        'PATH_INFO'         =>  $this->url->linkToObj($tab[$i]->object),
                        'PATH_FORMAT'       =>  format($tab[$i]->object, false),
                        'COMMENT'           =>  $tab[$i]->content,
                        'AUTHOR'            =>  $tab[$i]->author,
                        'MAIL'              =>  (empty($tab[$i]->mail) ? (empty($tab[$i]->url) ? '#' : $tab[$i]->url) : 'mailto:'.$tab[$i]->mail),
                        'URL'               =>  (empty($tab[$i]->mail) ? null : $tab[$i]->url),
                        'DATE'              =>  format_date($tab[$i]->date, 1),
                        'COMMENT_ID'        =>  $tab[$i]->id,
                        'ADMIN_DEL_COMMENT' =>  $this->url->linkToPage(array('admin', 'comments', 'del', $tab[$i]->id)),
                        ));
            $this->tpl->parse('Hdlblock_comment_line', 'block_comment_line', true);
        }

        $this->tpl->set_var(array(
                    'MSG'               =>  (!$size) ? __('There are no comments !') : (($size > 1) ? __('Comments from most recent to oldest.') : null),
                    'FORM_COMMENT_DEL'  =>  $this->url->linkToPage(array('admin', 'comments', 'del')),
                    ));

        return $this->tpl->parse('OutPut', 'comments');
   }
}
?>
