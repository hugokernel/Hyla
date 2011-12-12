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

class plugin_gui_groups extends plugin_gui {

    function plugin_gui_groups() {
       parent::plugin_gui();


    }
    function act() {
    }
    function aff() {
    
       $this->tpl->set_file('groups', 'tpl/groups.tpl');
      
       $this->tpl->set_block('groups', array(
         'group_edit_line'           =>  'Hdlgroup_edit_line',
         'groupe_edit_view'          =>  'Hdlgroupe_edit_view',
         'group_edit_add_user'       =>  'Hdlgroup_edit_add_user',
         'group_edit'                =>  'Hdlgroup_edit',
         'groups_line'               =>  'Hdlgroups_line',
         'groups_list'               =>  'Hdlgroups_list',
         'group_add'                 =>  'Hdlgroup_add',
         'block_groups'              =>  'Hdlblock_groups'
        ));
       plugin_obj::addStyleSheet('default.css');
       $usr = new users();

        switch ($this->url->getParam('aff', 3)) {

            // Ajout d'un groupe
            case 'add':
                if ($this->url->getParam('aff', 4) == 'save') {
                    $ret = $usr->checkLogin($_POST['ad_login']);
                    DBUG($ret);
                    if ($ret == -1) {
                        $msg_error = view_error(__('The name is invalid !'));
                        $_POST['ad_login'] = null;
                    } else if (!$ret) {
                        $msg_error = view_error(__('An user or a group of this name already exists !'));
                        $_POST['ad_login'] = null;
                    } else {
                        $id = $usr->addGroup($_POST['ad_login']);
                        break;
                    }
                    $this->tpl->set_var('NAME', $_POST['ad_login']);
                }

                $this->tpl->set_var(array(
                        'FORM_GROUP_SAVE'   =>  $this->url->linkToPage(array('admin', 'groups', 'add', 'save')),
                        'ERROR'             =>  $msg_error,
                        ));

                $this->tpl->parse('Hdlgroup_add', 'group_add', true);
                break;

            // Suppression d'un groupe
            case 'del':
                if (!in_array((int)$this->url->getParam('aff', 4), array(ANY_ID, AUTHENTICATED_ID, ANONYMOUS_ID), true))
                    $usr->delUser($this->url->getParam('aff', 4));
                break;

            // Ajout / Suppression des utilisateurs d'un groupe
            case 'edit':

                // Récupère les infos du groupe
                $ginfo = $usr->getUser($this->url->getParam('aff', 5));

                if ($ginfo && $ginfo->type == USR_TYPE_GRP) {

                    if ($this->url->getParam('aff', 4) == 'del') {
                        if (isset($_POST['ad_del_users'])) {
                            foreach ($_POST['ad_del_users'] as $user_id) {
                                $usr->delUserInGroup($this->url->getParam('aff', 5), $user_id);
                            }
                        }
                    } else if ($this->url->getParam('aff', 4) == 'add') {
                        if (isset($_POST['ad_add_users'])) {
                            foreach ($_POST['ad_add_users'] as $user_id) {
                                $usr->addUserInGroup($this->url->getParam('aff', 5), $user_id);
                            }
                        }
                    }

                    // Affichage des utilisateurs du groupe
                    $tab = $usr->getUsersGroup($this->url->getParam('aff', 5));
                    if ($tab) {
                        $size = sizeof($tab);
                        for ($i = 0; $i < $size; $i++) {
                            $this->tpl->set_var(array(
                                'GROUP_ID'          =>  $tab[$i]->id,
                                'GROUP_NAME'        =>  $tab[$i]->type == USR_TYPE_GRP ? '['.$tab[$i]->name.']' : $tab[$i]->name,
                                ));
                            $this->tpl->parse('Hdlgroup_edit_line', 'group_edit_line', true);
                        }
                        $this->tpl->parse('Hdlgroupe_edit_view', 'groupe_edit_view', true);
                    }

                    // Affichage des utilisateurs n'appartenant pas au groupe
                    $tab_u = $usr->getUsersNotInGroup($this->url->getParam('aff', 5));
                    $size = sizeof($tab_u);
                    for ($i = 0; $i < $size; $i++) {
                        $this->tpl->set_var(array(
                                'USER_ID'   =>  $tab_u[$i]->id,
                                'USER_NAME' =>  $tab_u[$i]->name,
                                ));
                        $this->tpl->parse('Hdlgroup_edit_add_user', 'group_edit_add_user', true);
                    }

                    $this->tpl->set_var(array(
                            'GROUP_NAME'            =>  $ginfo->name,
                            'FORM_GROUP_EDIT_ADD'   =>  $this->url->linkToPage(array('admin', 'groups', 'edit', 'add', $this->url->getParam('aff', 5))),
                            'FORM_GROUP_EDIT_DEL'   =>  $this->url->linkToPage(array('admin', 'groups', 'edit', 'del', $this->url->getParam('aff', 5))),
                            ));
                    $this->tpl->parse('Hdlgroup_edit', 'group_edit', true);
                }
                break;
        }

        $tab = $usr->getGroups();
        $size = sizeof($tab);
        if ($size) {
            for ($i = 0; $i < $size; $i++) {

                $tab_u = $usr->getUsersGroup($tab[$i]->id);
                $str = null;
                foreach ($tab_u as $occ) {
                    $str .= $occ->name.', ';
                }

                $this->tpl->set_var(array(
                        'GROUP_CONTENT'     =>  $str ? substr($str, 0, -2) : null,
                        'GROUP_ID'          =>  $tab[$i]->id,
                        'GROUP_NAME'        =>  $tab[$i]->type == USR_TYPE_GRP ? '['.$tab[$i]->name.']' : $tab[$i]->name,
                        'ADMIN_GROUP_EDIT'  =>  $this->url->linkToPage(array('admin', 'groups', 'edit', 'view', $tab[$i]->id)),
                        'ADMIN_GROUP_DEL'   =>  $this->url->linkToPage(array('admin', 'groups', 'del', $tab[$i]->id)),
                        ));
                $this->tpl->parse('Hdlgroups_line', 'groups_line', true);
            }
            $this->tpl->parse('Hdlgroups_list', 'groups_list', true);
        } else {
            $this->tpl->set_var('MSG', __('There are no group !'));
        }

        $this->tpl->set_var(array(
                'ADMIN_GROUP_ADD'   =>  $this->url->linkToPage(array('admin', 'groups', 'add')),
                ));

        $this->tpl->parse('Hdlblock_groups', 'block_groups', true);
     return $this->tpl->parse('OutPut', 'groups');
    }
}

?>
