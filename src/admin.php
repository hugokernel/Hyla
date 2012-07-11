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

if (!defined('PAGE_HOME'))
    header('location: ../index.php');

$tpl->set_file('admin', 'admin.tpl');

$tpl->set_block('admin', array(
        'test_version'              =>  'Hdltest_version',

        'test_ok'                   =>  'Hdltest_ok',
        'test_no'                   =>  'Hdltest_no',

        'aff_home'                  =>  'Hdlaff_home',
        'aff_conf_template_style'   =>  'Hdlaff_conf_template_style',
        'aff_conf_template'         =>  'Hdlaff_conf_template',
        'aff_conf_plugin'           =>  'Hdlaff_conf_plugin',
        'aff_conf'                  =>  'Hdlaff_conf',

        'comment_line'              =>  'Hdlcomment_line',
        'comment'                   =>  'Hdlcomment',

        'anon_move_dir_occ'         =>  'Hdlanon_move_dir_occ',
        'anon_move'                 =>  'Hdlanon_move',
        'anon_line'                 =>  'Hdlanon_line',
        'anon_list'                 =>  'Hdlanon_list',
        'anon'                      =>  'Hdlanon',

        'maintenance'               =>  'Hdlmaintenance',

        'user_edit_password'        =>  'Hdluser_edit_password',
        'user_edit_type'            =>  'Hdluser_edit_type',
        'user_edit'                 =>  'Hdluser_edit',
        'user_add'                  =>  'Hdluser_add',
        'users_line_del'            =>  'Hdlusers_line_del',
        'users_line'                =>  'Hdlusers_line',
        'users_list'                =>  'Hdlusers_list',
        'users'                     =>  'Hdlusers',

        'group_edit_line'           =>  'Hdlgroup_edit_line',
        'groupe_edit_view'          =>  'Hdlgroupe_edit_view',
        'group_edit_add_user'       =>  'Hdlgroup_edit_add_user',
        'group_edit'                =>  'Hdlgroup_edit',
        'groups_line'               =>  'Hdlgroups_line',
        'groups_list'               =>  'Hdlgroups_list',
        'group_add'                 =>  'Hdlgroup_add',
        'groups'                    =>  'Hdlgroups',


        'rights_list_header'        =>  'Hdlrights_list_header',
        'rights_list_line_error'    =>  'Hdlrights_list_line_error',
        'rights_list_line'          =>  'Hdlrights_list_line',
        'rights_list'               =>  'Hdlrights_list',
        'rights_list_tab'           =>  'Hdlrights_list_tab',

        'rights_edit_right_selected'=>  'Hdlrights_edit_right_selected',
        'rights_edit_right'         =>  'Hdlrights_edit_right',
        'rights_edit_selected_view' =>  'Hdlrights_edit_selected_view',
        'rights_edit_disabled_multiple' =>  'Hdlrights_edit_disabled_multiple',
        'rights_edit'               =>  'Hdlrights_edit',

        'rights_add_1_dir_occ'      =>  'Hdlrights_add_1_dir_occ',
        'rights_add_1'              =>  'Hdlrights_add_1',

        'rights_add_2_user'         =>  'Hdlrights_add_2_user',
        'rights_add_2_right'        =>  'Hdlrights_add_2_right',
        'rights_add_2'              =>  'Hdlrights_add_2',

        'rights_error'              =>  'Hdlrights_error',

        'rights'                    =>  'Hdlrights',
        ));

$tpl->l10n->setFile('admin.php');

$msg = null;
$msg_error = null;

switch ($url->getParam('aff', 2)) {

    #   Maintenance
    case 'maintenance':

        switch ($url->getParam('aff', 3)) {
            case 'purge':
                cache::free();
                $tpl->set_var('PURGE_RAPPORT',  __('Cache was purged !'));
                break;

            case 'sync':
                $tpl->set_var('SYNC_RAPPORT',   __('%s objet(s) was deleted from database !', $obj->syncBdd()));
                break;
        }

        $tpl->set_var(array(
                'ADMIN_PAGE_MAINTENANCE_PURGE'  =>  $url->linkToPage(array('admin', 'maintenance', 'purge')),
                'ADMIN_PAGE_MAINTENANCE_SYNC'   =>  $url->linkToPage(array('admin', 'maintenance', 'sync')),
                ));

        $tpl->parse('Hdlmaintenance', 'maintenance', true);
        break;

    #   Les utilisateurs
    case 'users':

        $usr = new users();

        $param = $url->getParam('aff', 3);

        switch ($param) {

            case 'add':
                if ($url->getParam('aff', 4) == 'save') {
                    $ret = $usr->testLogin($_POST['ad_login']);
                    if ($ret == -1) {
                        $msg_error = view_error(__('The name is invalid !'));
                        $_POST['ad_login'] = null;
                    } else if (!$ret) {
                        $msg_error = view_error(__('An user or a group of this name already exists !'));
                        $_POST['ad_login'] = null;
                    } else if (empty($_POST['ad_password']) || empty($_POST['ad_password_bis']))
                        $msg_error = view_error(__('All the fields must be filled'));
                    else if (strlen($_POST['ad_password']) < MIN_PASSWORD_SIZE)
                        $msg_error = view_error(__('Password must have at least %s characters !', MIN_PASSWORD_SIZE));
                    else if ($_POST['ad_password'] != $_POST['ad_password_bis'])
                        $msg_error = view_error(__('Passwords are different'));
                    else {
                        $id = $usr->addUser($_POST['ad_login'], $_POST['ad_password']);

                        $tab_type = array(0 => USR_TYPE_USER, 1 => USR_TYPE_SUPERVISOR, 2 => USR_TYPE_ADMIN);
                        if (!array_key_exists($_POST['ad_type'], $tab_type))
                            $_POST['ad_type'] = 0;

                        $usr->setType($id, $tab_type[$_POST['ad_type']]);
                        $param = null;
                        break;
                    }
                    $tpl->set_var('NAME', $_POST['ad_login']);
                }

                $tpl->set_var(array(
                        'FORM_USER_SAVE'    =>  $url->linkToPage(array('admin', 'users', 'add', 'save')),
                        'ERROR'             =>  $msg_error,
                        ));
                $tpl->parse('Hdluser_add', 'user_add', true);
                break;

            case 'del':
                // Pas le droit de s'autosupprimer, ni de supprimer les méta utilisateurs
                if ($url->getParam('aff', 4) != $cuser->id) {
                    if (!in_array((int)$url->getParam('aff', 4), array(ANY_ID, AUTHENTICATED_ID, ANONYMOUS_ID), true))
                        $usr->delUser($url->getParam('aff', 4));
                }
                $param = null;
                break;

            case 'savetype':
                $tab_type = array(0 => USR_TYPE_USER, 1 => USR_TYPE_SUPERVISOR, 2 => USR_TYPE_ADMIN);
                if (!array_key_exists($_POST['ad_type'], $tab_type))
                    $_POST['ad_type'] = 0;

                if ($url->getParam('aff', 4) == $cuser->id) {
                    $msg = view_error(__('Unable to change his own administration permission !'));
                } else
                    $usr->setType($url->getParam('aff', 4), $tab_type[$_POST['ad_type']]);

            case 'savepassword':
                // Pas propre ça !
                if ($url->getParam('aff', 3) == 'savepassword') {
                    if (empty($_POST['ad_password']) || empty($_POST['ad_password_bis'])) {
                        $msg = view_error(__('All the fields must be filled'));
                    } else if ($_POST['ad_password'] != $_POST['ad_password_bis']) {
                        $msg = view_error(__('Passwords are different'));
                    } else if (strlen($_POST['ad_password']) < MIN_PASSWORD_SIZE) {
                        $msg = view_error(__('Password must have at least %s characters !', MIN_PASSWORD_SIZE));
                    } else if ($url->getParam('aff', 4) != ANONYMOUS_ID && isset($_POST['ad_password']) && !empty($_POST['ad_password'])) {
                        $usr->setPassword($url->getParam('aff', 4), $_POST['ad_password']);
                        $msg = view_status(__('Password changed !'));
                    }
                }

            case 'edit':
                $tab = $usr->getUser($url->getParam('aff', 4));
                if ($tab && !($tab->id == ANY_ID || $tab->id == ANONYMOUS_ID || $tab->id == AUTHENTICATED_ID)) {
                    $tpl->parse('Hdluser_edit_password', 'user_edit_password', true);

                    if ($tab->id != $cuser->id)
                        $tpl->parse('Hdluser_edit_type', 'user_edit_type', true);

                    $tpl->set_var(array(
                            'USER_NAME'                 =>  $tab->name,

                            'SELECT_TYPE_USER'          =>  $tab->type == USR_TYPE_USER ? 'selected="selected"' : null,
                            'SELECT_TYPE_SUPERVISOR'    =>  $tab->type == USR_TYPE_SUPERVISOR ? 'selected="selected"' : null,
                            'SELECT_TYPE_ADMIN'         =>  $tab->type == USR_TYPE_ADMIN ? 'selected="selected"' : null,

                            'FORM_USER_EDIT_TYPE'       =>  $url->linkToPage(array('admin', 'users', 'savetype', $url->getParam('aff', 4))),
                            'FORM_USER_EDIT_PASSWORD'   =>  $url->linkToPage(array('admin', 'users', 'savepassword', $url->getParam('aff', 4))),
                            'MSG'                       =>  $msg,
                            ));

                    $tpl->parse('Hdluser_edit', 'user_edit', true);
                }
                break;
        }

        if (!$param) {

            $tab_type = array(  USR_TYPE_SPECIAL    =>  __('Special'),
                                USR_TYPE_USER       =>  __('Standard'),
                                USR_TYPE_SUPERVISOR =>  __('Supervisor'),
                                USR_TYPE_ADMIN      =>  __('Administrator')
                            );

            $tab = $usr->getUsers();

            $size = sizeof($tab);
            for ($i = 0; $i < $size; $i++) {

                // On n'affiche pas les méta-utilisateurs
                if ($tab[$i]->id == ANY_ID || $tab[$i]->id == ANONYMOUS_ID || $tab[$i]->id == AUTHENTICATED_ID)
                    continue;

                $tpl->set_var(array(
                        'Hdlusers_line_del' =>  null,
                        'USER_ID'           =>  $tab[$i]->id,
                        'USER_NAME'         =>  $tab[$i]->name,
                        'USER_TYPE'         =>  $tab_type[$tab[$i]->type],
                        'ADMIN_USER_EDIT'   =>  $url->linkToPage(array('admin', 'users', 'edit', $tab[$i]->id)),
                        'ADMIN_USER_DEL'    =>  $url->linkToPage(array('admin', 'users', 'del', $tab[$i]->id)),
                        ));

                if ($tab[$i]->id != ANY_ID && $tab[$i]->id != ANONYMOUS_ID && $tab[$i]->id != AUTHENTICATED_ID) {
                    if ($tab[$i]->id != $cuser->id)
                        $tpl->parse('Hdlusers_line_del', 'users_line_del', true);
                }

                $tpl->parse('Hdlusers_line', 'users_line', true);
            }
            $tpl->parse('Hdlusers_list', 'users_list', true);
        }

        $tpl->set_var(array(
                'ADMIN_USER_ADD'    =>  $url->linkToPage(array('admin', 'users', 'add')),
                'ADMIN_USER_LIST'   =>  $url->linkToPage(array('admin', 'users')),
                ));

        $tpl->parse('Hdlusers', 'users', true);
        break;

    #   Les groupes
    case 'groups':

        $usr = new users();

        switch ($url->getParam('aff', 3)) {

            // Ajout d'un groupe
            case 'add':
                if ($url->getParam('aff', 4) == 'save') {
                    $ret = $usr->testLogin($_POST['ad_login']);
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
                    $tpl->set_var('NAME', $_POST['ad_login']);
                }

                $tpl->set_var(array(
                        'FORM_GROUP_SAVE'   =>  $url->linkToPage(array('admin', 'groups', 'add', 'save')),
                        'ERROR'             =>  $msg_error,
                        ));

                $tpl->parse('Hdlgroup_add', 'group_add', true);
                break;

            // Suppression d'un groupe
            case 'del':
                if (!in_array((int)$url->getParam('aff', 4), array(ANY_ID, AUTHENTICATED_ID, ANONYMOUS_ID), true))
                    $usr->delUser($url->getParam('aff', 4));
                break;

            // Ajout / Suppression des utilisateurs d'un groupe
            case 'edit':

                // Récupère les infos du groupe
                $ginfo = $usr->getUser($url->getParam('aff', 5));

                if ($ginfo && $ginfo->type == USR_TYPE_GRP) {

                    if ($url->getParam('aff', 4) == 'del') {
                        if (isset($_POST['ad_del_users'])) {
                            foreach ($_POST['ad_del_users'] as $user_id) {
                                $usr->delUserInGroup($url->getParam('aff', 5), $user_id);
                            }
                        }
                    } else if ($url->getParam('aff', 4) == 'add') {
                        if (isset($_POST['ad_add_users'])) {
                            foreach ($_POST['ad_add_users'] as $user_id) {
                                $usr->addUserInGroup($url->getParam('aff', 5), $user_id);
                            }
                        }
                    }

                    // Affichage des utilisateurs du groupe
                    $tab = $usr->getUsersGroup($url->getParam('aff', 5));
                    if ($tab) {
                        $size = sizeof($tab);
                        for ($i = 0; $i < $size; $i++) {
                            $tpl->set_var(array(
                                'GROUP_ID'          =>  $tab[$i]->id,
                                'GROUP_NAME'        =>  $tab[$i]->type == USR_TYPE_GRP ? '['.$tab[$i]->name.']' : $tab[$i]->name,
                                ));
                            $tpl->parse('Hdlgroup_edit_line', 'group_edit_line', true);
                        }
                        $tpl->parse('Hdlgroupe_edit_view', 'groupe_edit_view', true);
                    }

                    // Affichage des utilisateurs n'appartenant pas au groupe
                    $tab_u = $usr->getUsersNotInGroup($url->getParam('aff', 5));
                    $size = sizeof($tab_u);
                    for ($i = 0; $i < $size; $i++) {
                        $tpl->set_var(array(
                                'USER_ID'   =>  $tab_u[$i]->id,
                                'USER_NAME' =>  $tab_u[$i]->name,
                                ));
                        $tpl->parse('Hdlgroup_edit_add_user', 'group_edit_add_user', true);
                    }

                    $tpl->set_var(array(
                            'GROUP_NAME'            =>  $ginfo->name,
                            'FORM_GROUP_EDIT_ADD'   =>  $url->linkToPage(array('admin', 'groups', 'edit', 'add', $url->getParam('aff', 5))),
                            'FORM_GROUP_EDIT_DEL'   =>  $url->linkToPage(array('admin', 'groups', 'edit', 'del', $url->getParam('aff', 5))),
                            ));
                    $tpl->parse('Hdlgroup_edit', 'group_edit', true);
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

                $tpl->set_var(array(
                        'GROUP_CONTENT'     =>  $str ? substr($str, 0, -2) : null,
                        'GROUP_ID'          =>  $tab[$i]->id,
                        'GROUP_NAME'        =>  $tab[$i]->type == USR_TYPE_GRP ? '['.$tab[$i]->name.']' : $tab[$i]->name,
                        'ADMIN_GROUP_EDIT'  =>  $url->linkToPage(array('admin', 'groups', 'edit', 'view', $tab[$i]->id)),
                        'ADMIN_GROUP_DEL'   =>  $url->linkToPage(array('admin', 'groups', 'del', $tab[$i]->id)),
                        ));
                $tpl->parse('Hdlgroups_line', 'groups_line', true);
            }
            $tpl->parse('Hdlgroups_list', 'groups_list', true);
        } else {
            $tpl->set_var('MSG', __('There are no group !'));
        }

        $tpl->set_var(array(
                'ADMIN_GROUP_ADD'   =>  $url->linkToPage(array('admin', 'groups', 'add')),
                ));

        $tpl->parse('Hdlgroups', 'groups', true);
        break;

    #   Les droits
    case 'rights':

        $usr = new users();

        $param = $url->getParam('aff', 3);
        switch ($param) {

            // Bêta test : réparation des droits
            case 'repair':
                $obj->findError(true);
                $param = null;
                break;

            // Ajout d'un droit
            case 'add':

                if ($url->getParam('aff', 4) == 'save') {

                    // Calcul des droits
                    $right = $obj->calculateRights($_POST['rgt_value']);

                    if (isset($_POST['rgt_users'])) {
                        foreach ($_POST['rgt_users'] as $user) {
                            $obj->addRight($_POST['rgt_dir'], $user, $right);
                        }
                        $param = null;
                        break;
                    } else {
                        $msg_error = __('You did not select user !');
                        $url->getParam('aff', 4, 'next');
                    }
                }

                // Étape 2 de l'ajout, on choisi les utilisateurs parmis ceux restant et les droits
                $obj_dir = (isset($_POST['rgt_dir'])) ? $_POST['rgt_dir'] : $url->getParam('obj');
                $obj_dir = stripslashes($obj_dir);
                $obj_dir = file::getRealDir($obj_dir, FOLDER_ROOT);

                if ($url->getParam('aff', 4) == 'next' && $obj_dir && is_dir(FOLDER_ROOT.$obj_dir)) {

                    // On affiche les utilisateurs et les groupes
                    $tab = $usr->getUsers(true);
                    $tab_exist = $obj->getObjRights($obj_dir);

                    $size = sizeof($tab);
                    for ($i = 0; $i < $size; $i++) {
                        if (!array_key_exists($tab[$i]->name, $tab_exist)) {
                            $tpl->set_var(array(
                                    'USER_ID'   =>  $tab[$i]->id,
                                    'USER_NAME' =>  $tab[$i]->type == USR_TYPE_GRP ? '['.$tab[$i]->name.']' : $tab[$i]->name,
                                    ));
                            $tpl->parse('Hdlrights_add_2_user', 'rights_add_2_user', true);
                        }
                    }
                    unset($tab);

                    // On affiche les droits
                    foreach($obj->getAcl() as $val => $name) {
                        if (!constant($val) || constant($val) & AC_VIEW) {
                            continue;
                        }
                        $tpl->set_var(array(
                                'RIGHT_VALUE'   =>  constant($val),
                                'RIGHT_NAME'    =>  $name,
                                ));
                        $tpl->parse('Hdlrights_add_2_right', 'rights_add_2_right', true);
                    }

                    $tpl->set_var(array(
                            'ERROR'             =>  $msg_error ? view_error($msg_error) : null,
                            'FORM_RIGHTS_ADD'   =>  $url->linkToPage(array('admin', 'rights', 'add', 'save'), $obj_dir),
                            'OBJECT'            =>  $obj_dir,
                            ));

                    $tpl->parse('Hdlrights_add_2', 'rights_add_2', true);

                } else {
                    // Étape 1 : On choisi le dossier sur lequel appliquer les droits
                    $tab = $obj->scanDir(FOLDER_ROOT, true);
                    asort($tab);

                    // Les dossiers
                    foreach ($tab as $occ) {
                        $tpl->set_var('DIR_NAME', $occ);
                        $tpl->parse('Hdlrights_add_1_dir_occ', 'rights_add_1_dir_occ', true);
                    }

                    $tpl->set_var(array(
                            'FORM_RIGHTS_ADD'   =>  $url->linkToPage(array('admin', 'rights', 'add', 'next')),
                            ));

                    $tpl->parse('Hdlrights_add_1', 'rights_add_1', true);
                }

                break;

            // Édition des droits
            case 'edit':

                // Sauvergarde de l'édition
                if ($url->getParam('aff', 4) == 'save') {

                    list($obj_id, $user_id) = explode('|', $url->getParam('aff', 5));

                    // Calcul des droits
                    $right = $obj->calculateRights($_POST['rgt_value']);
                    $obj->setRight($obj_id, $user_id, $right);

                    $param = null;
                    break;
                }

                list($obj_id, $user_id) = explode('|', $url->getParam('aff', 4));

                $iuser = $usr->getUser($user_id);
                $ifile = $obj->getFile($obj_id);
                if ($iuser && $ifile) {

                    $tpl->set_var(array(
                            'USER_NAME'     =>  $iuser->name,
                            'CURRENT_DIR'   =>  $ifile,
                            ));

                    $right = $obj->getRightsFromUserAndPath($user_id, $ifile);

                    foreach($obj->getAcl() as $val => $name) {
                        if (constant($val) & AC_VIEW)
                            continue;
                        $tpl->set_var('Hdlrights_edit_right_selected');
                        if ($right & constant($val)) {
                            $tpl->parse('Hdlrights_edit_right_selected', 'rights_edit_right_selected', true);
                        }
                        $tpl->set_var(array(
                                'RIGHT_VALUE'   =>  constant($val),
                                'RIGHT_NAME'    =>  $name,
                                ));
                        $tpl->parse('Hdlrights_edit_right', 'rights_edit_right', true);
                    }

                    // Affichage des case à cocher View
                    if ($right & AC_VIEW) {
                        $tpl->parse('Hdlrights_edit_selected_view', 'rights_edit_selected_view', true);
                    } else {
                        $tpl->parse('Hdlrights_edit_disabled_multiple', 'rights_edit_disabled_multiple', true);
                    }

                    $tpl->set_var('FORM_RIGHTS_EDIT', $url->linkToPage(array('admin', 'rights', 'edit', 'save', $obj_id.'|'.$user_id)));

                    $tpl->parse('Hdlrights_edit', 'rights_edit', true);
                }
                break;

            // Suppression d'un ou de plusieurs droits
            case 'del':
                if ($_POST['right_id']) {
                    foreach ($_POST['right_id'] as $right) {
                        list($obj_id, $user_id) = explode('|', $right);
                        $obj->delRight($obj_id, $user_id);
                    }
                }
                $param = null;
                break;
        }

        // Affichage du tableau des droits
        if (!$param) {
            $tab = $obj->getAllRights($cobj->file);

            // Y-a-t-il des erreurs dans les droits ?
            $t_error = $obj->findError();
            if ($t_error) {
                $tpl->set_var('URL_RIGHTS_REPAIR', $url->linkToPage(array('admin', 'rights', 'repair')));
                $tpl->parse('Hdlrights_error', 'rights_error', true);
            }

            if ($tab) {
                $last_obj = 0;
                foreach ($tab as $occ) {

                    // Affichage des erreurs
                    $tpl->set_var('Hdlrights_list_line_error');
                    if ($t_error && array_key_exists($occ['obj_file'], $t_error) && array_key_exists($occ['user_id'], $t_error[$occ['obj_file']])) {
                        $tpl->parse('Hdlrights_list_line_error', 'rights_list_line_error', true);
                    }

                    // Le dossier n'existe plus ?
                    if (!file_exists(FOLDER_ROOT.$occ['obj_file'])) {
                        continue;
                    }

                    $tpl->set_var('Hdlrights_list_header');
                    $tpl->set_var('Hdlrights_list_line');

                    if ($last_obj != $occ['obj_id']) {
                        $tpl->set_var(array(
                                'OBJ_NAME'          =>  $occ['obj_file'],
                                'URL_OBJ'           =>  $url->linkToObj($occ['obj_file']),
                                'URL_RIGHTS_ADD'    =>  $url->linkToPage(array('admin', 'rights', 'add', 'next'), $occ['obj_file']),
                                ));
                        $tpl->parse('Hdlrights_list_header', 'rights_list_header', true);
                    }

                    $tpl->set_var(array(
                            'USER_ID'           =>  $occ['user_id'],
                            'USER_NAME'         =>  $occ['user_type'] == USR_TYPE_GRP ? '['.$occ['user_name'].']' : $occ['user_name'],
                            'RIGHTS'            =>  $obj->getTextRights($occ['perm']),
                            'URL_RIGHTS_EDIT'   =>  $url->linkToPage(array('admin', 'rights', 'edit', $occ['obj_id'].'|'.$occ['user_id'])),
                            'RIGHT_ID'          =>  $occ['obj_id'].'|'.$occ['user_id'],
                            ));

                    $tpl->parse('Hdlrights_list_line', 'rights_list_line', true);
                    $tpl->parse('Hdlrights_list', 'rights_list', true);
                    $last_obj = $occ['obj_id'];
                }

                $tpl->set_var('FORM_RIGHT_DEL', $url->linkToPage(array('admin', 'rights', 'del')));

                $tpl->parse('Hdlrights_list_tab', 'rights_list_tab', true);
            } else {
                $msg = __('No right was found in all the filesystem !');
            }
        }

        $tpl->set_var(array(
                'MSG'               =>  $msg,
                'URL_RIGHTS_ADD'    =>  $url->linkToPage(array('admin', 'rights', 'add')),
                'URL_RIGHTS_LIST'   =>  $url->linkToPage(array('admin', 'rights')),
                ));

        $tpl->parse('Hdlrights', 'rights', true);
        break;

    #   Les commentaires
    case 'comment':

        // Suppression d'un ou plusieurs commentaires !
        if ($url->getParam('aff', 3) == 'del') {
            if (intval($url->getParam('aff', 4)))
                $obj->delComment(intval($url->getParam('aff', 4)));
            else {
                $obj->delComment($_POST['comment_id']);
            }
        }

        $tab = $obj->getLastComment();
        $size = sizeof($tab);
        for ($i = 0; $i < $size; $i++) {
            $tpl->set_var(array(
                    'FILE_ICON'         =>  $tab[$i]->icon,
                    'PATH_INFO'         =>  $url->linkToObj($tab[$i]->object),
                    'PATH_FORMAT'       =>  format($tab[$i]->object, false),
                    'COMMENT'           =>  $tab[$i]->content,
                    'AUTHOR'            =>  $tab[$i]->author,
                    'MAIL'              =>  (empty($tab[$i]->mail) ? (empty($tab[$i]->url) ? '#' : $tab[$i]->url) : 'mailto:'.$tab[$i]->mail),
                    'URL'               =>  (empty($tab[$i]->mail) ? null : $tab[$i]->url),
                    'DATE'              =>  format_date($tab[$i]->date, 1),
                    'COMMENT_ID'        =>  $tab[$i]->id,
                    'ADMIN_DEL_COMMENT' =>  $url->linkToPage(array('admin', 'comment', 'del', $tab[$i]->id)),
                    ));
            $tpl->parse('Hdlcomment_line', 'comment_line', true);
        }

        $tpl->set_var(array(
                'MSG'               =>  (!$size) ? __('There are no comments !') : (($size > 1) ? __('Comments from most recent to oldest.') : null),
                'FORM_COMMENT_DEL'  =>  $url->linkToPage(array('admin', 'comment', 'del')),
                ));
        $tpl->parse('Hdlcomment', 'comment', true);
        break;

    #   Affichage de la configuration
    case 'conf':

        // Sauvegarde de la config !
        if ($url->getParam('aff', 3) == 'save') {
            $ini = new iniFile(FILE_INI);

            // Affichage
            $ini->editVar('title',              $_POST['conf_title']);

            list($template, $style) = explode('|', $_POST['conf_template']);
            $ini->editVar('template',           $template);
            $ini->editVar('style',              $style);

            $ini->editVar('view_toolbar',       $_POST['conf_view_toolbar']);
            $ini->editVar('view_tree',          $_POST['conf_view_tree']);
            $ini->editVar('view_hidden_file',   $_POST['conf_view_hidden_file']);

            // Ajout de fichiers et dossiers
            $ini->editVar('file_chmod',         $_POST['conf_file_chmod']);
            $ini->editVar('dir_chmod',          $_POST['conf_dir_chmod']);
            $ini->editVar('anon_file_send',     $_POST['conf_anon_file_send']);

            // Listage de répertoires
            $ini->editVar('sort',               $_POST['conf_sort']);
            $ini->editVar('folder_first',       $_POST['conf_folder_first']);
            $ini->editVar('group_by_sort',      $_POST['conf_group_by_sort']);
            $ini->editVar('nbr_obj',            $_POST['conf_nbr_obj']);

            // Divers
            $ini->editVar('webmaster_mail',     $_POST['conf_webmaster_mail']);
            $ini->editVar('lng',                $_POST['conf_lng']);
            $ini->editVar('download_counter',   $_POST['conf_download_counter']);

            $ini->editVar('time_of_redirection',    $_POST['conf_time_of_redirection'] < 1 ? 1 : $_POST['conf_time_of_redirection']);

            $ini->editVar('download_dir',           $_POST['conf_download_dir']);

            $ini->editVar('fs_charset_is_utf8',     $_POST['conf_fs_charset_is_utf8']);

            $ini->editVar('register_user',          $_POST['conf_register_user']);

            if (plugins::isValid(strtolower($_POST['conf_plugin_default_dir']))) {
                $ini->editVar('plugin_default_dir',     $_POST['conf_plugin_default_dir']);
            }


            $tpl_changed = ($template != $conf['name_template']) ? true : false;

            if (!$ini->saveFile())
                $msg_error = __('Couldn\'t write configuration file ( %s ) !', FILE_INI);
            else {
                load_config();

                // Si le template change, on redirige
                if ($tpl_changed) {
                    redirect($cobj->file, $url->linkToPage(array('admin', 'conf')), __('The new template will be applied !'));
                    system::end();
                }

                $msg_status = __('Configuration was correctly recorded !');
            }

            unset($ini);
        }

        $tpl->set_var(array(
                'WEBMASTER_MAIL'        =>  $conf['webmaster_mail'],
                'TIME_OF_REDIRECTION'   =>  $conf['time_of_redirection'],
                'CURRENT_TEMPLATE'      =>  $conf['name_template'],

                'TITLE'                 =>  $conf['title'],
                'LNG'                   =>  $conf['lng'],
                'FILE_CHMOD'            =>  decoct($conf['file_chmod']),
                'DIR_CHMOD'             =>  decoct($conf['dir_chmod']),

                'ADMIN_PAGE_SAVECONF'       =>  $url->linkToPage(array('admin', 'conf', 'save')),
                ));

        // Action en cas de fichier anonyme...
        switch ($conf['anon_file_send']) {
            case 0:   $tpl->set_var('CONF_ANON_FILE_SEND_0', 'selected="selected"');    break;
//            case 2:   $tpl->set_var('CONF_ANON_FILE_SEND_2', 'selected="selected"');    break;
//            case 3:   $tpl->set_var('CONF_ANON_FILE_SEND_3', 'selected="selected"');    break;
            case 1:
            default:    $tpl->set_var('CONF_ANON_FILE_SEND_1', 'selected="selected"');  break;
        }

        // Listage des répertoires de tpl/
        $hdl = dir(DIR_ROOT.DIR_TPL);
        if ($hdl) {
            while (false !== ($tpl_name = $hdl->read())) {

                $xfile = DIR_ROOT.DIR_TPL.$tpl_name.'/info.xml';
                if (!file_exists($xfile))
                    continue;

                $tpl->set_var('Hdlaff_conf_template_style');
                $tpl->set_var('TEMPLATE_NAME', $tpl_name);

                $xml = new XPath($xfile);
                $res = $xml->match('/template');
                if ($res) {
                    $res = $xml->match('/template/stylesheets/stylesheet');
                    if ($res) {
                        foreach ($res as $occ) {
                            $style_title = $xml->getData($occ.'/title');
                            $style_file = $xml->getData($occ.'/href');
                            $tpl->set_var(array(
                                    'STYLE_NAME'            =>  $style_title,
                                    'STYLE_FILE'            =>  $style_file,
                                    'CONF_TEMPLATE_NAME'    =>  ($tpl_name == $conf['name_template'] && $style_file == $conf['style']) ? 'selected="selected"' : null
                                    ));
                            $tpl->parse('Hdlaff_conf_template_style', 'aff_conf_template_style', true);
                        }
                    } else
                        continue;
                }

                $tpl->parse('Hdlaff_conf_template', 'aff_conf_template', true);
            }
            unset($hdl);
        }

        $folder_first = false;

        switch ($conf['sort_config']) {
            case SORT_DEFAULT:                          $tpl->set_var('CONF_SORT_0', 'selected="selected"');    break;

            case SORT_NAME_ALPHA:                       $tpl->set_var('CONF_SORT_1', 'selected="selected"');    break;
            case SORT_NAME_ALPHA | SORT_FOLDER_FIRST:   $tpl->set_var('CONF_SORT_1', 'selected="selected"');    $folder_first = true;   break;
            case SORT_NAME_ALPHA_R:                     $tpl->set_var('CONF_SORT_2', 'selected="selected"');    break;
            case SORT_NAME_ALPHA_R | SORT_FOLDER_FIRST: $tpl->set_var('CONF_SORT_2', 'selected="selected"');    $folder_first = true;   break;

            case SORT_EXT_ALPHA:                        $tpl->set_var('CONF_SORT_3', 'selected="selected"');    break;
            case SORT_EXT_ALPHA | SORT_FOLDER_FIRST:    $tpl->set_var('CONF_SORT_3', 'selected="selected"');    $folder_first = true;   break;
            case SORT_EXT_ALPHA_R:                      $tpl->set_var('CONF_SORT_4', 'selected="selected"');    break;
            case SORT_EXT_ALPHA_R | SORT_FOLDER_FIRST:  $tpl->set_var('CONF_SORT_4', 'selected="selected"');    $folder_first = true;   break;

            case SORT_CAT_ALPHA:                        $tpl->set_var('CONF_SORT_5', 'selected="selected"');    break;
            case SORT_CAT_ALPHA | SORT_FOLDER_FIRST:    $tpl->set_var('CONF_SORT_5', 'selected="selected"');    $folder_first = true;   break;
            case SORT_CAT_ALPHA_R:                      $tpl->set_var('CONF_SORT_6', 'selected="selected"');    break;
            case SORT_CAT_ALPHA_R | SORT_FOLDER_FIRST:  $tpl->set_var('CONF_SORT_6', 'selected="selected"');    $folder_first = true;   break;

            case SORT_SIZE:                             $tpl->set_var('CONF_SORT_7', 'selected="selected"');    break;
            case SORT_SIZE | SORT_FOLDER_FIRST:         $tpl->set_var('CONF_SORT_7', 'selected="selected"');    $folder_first = true;   break;
            case SORT_SIZE_R:                           $tpl->set_var('CONF_SORT_8', 'selected="selected"');    break;
            case SORT_SIZE_R | SORT_FOLDER_FIRST:       $tpl->set_var('CONF_SORT_8', 'selected="selected"');    $folder_first = true;   break;
                break;
        }

        $tpl->set_var(array(
                ($folder_first ? 'CONF_FOLDER_FIRST_1' : 'CONF_FOLDER_FIRST_0')     =>  'selected="selected"',
                ($conf['group_by_sort'] ? 'CONF_GROUP_BY_SORT_1' : 'CONF_GROUP_BY_SORT_0')  =>  'selected="selected"',
                'NBR_OBJ'       =>  $conf['nbr_obj'],
                ));

        $tpl->set_var(array(
                ($conf['view_hidden_file'] ? 'CONF_VIEW_HIDDEN_FILE_1' : 'CONF_VIEW_HIDDEN_FILE_0') =>  'selected="selected"',
                ($conf['download_counter'] ? 'CONF_DOWNLOAD_COUNTER_1' : 'CONF_DOWNLOAD_COUNTER_0') =>  'selected="selected"',

                ($conf['download_dir'] ? 'CONF_DOWNLOAD_DIR_1' : 'CONF_DOWNLOAD_DIR_0') =>  'selected="selected"',

                ($conf['fs_charset_is_utf8'] ? 'CONF_FS_CHARSET_IS_UTF8_1' : 'CONF_FS_CHARSET_IS_UTF8_0')   =>  'selected="selected"',

                ($conf['view_toolbar'] ? 'CONF_VIEW_TOOLBAR_1' : 'CONF_VIEW_TOOLBAR_0')     =>  'selected="selected"',

                ($conf['register_user'] ? 'CONF_REGISTER_USER_1' : 'CONF_REGISTER_USER_0')  =>  'selected="selected"',

                'CONF_VIEW_TREE_0'      =>  ($conf['view_tree'] == 0) ? 'selected="selected"' :  null,
                'CONF_VIEW_TREE_1'      =>  ($conf['view_tree'] == 1) ? 'selected="selected"' :  null,
                'CONF_VIEW_TREE_2'      =>  ($conf['view_tree'] == 2) ? 'selected="selected"' :  null,

                ));

        // Listage des répertoires des plugins
        $tab_plugins = plugin_obj::getDirPlugins();
        foreach ($tab_plugins as $occ) {
            $tpl->set_var(array(
                    'PLUGIN_NAME'           =>  $occ['name'],
                    'PLUGIN_DESCRIPTION'    =>  $occ['description'],
                    'CONF_PLUGIN_NAME'      =>  (strtolower($occ['name']) == strtolower($conf['plugin_default_dir']) ? 'selected="selected"' : null)
                    ));
            $tpl->parse('Hdlaff_conf_plugin', 'aff_conf_plugin', true);
        }

        $tpl->set_var(array(
                'ERROR'     =>  isset($msg_error) ? view_error($msg_error) : null,
                'STATUS'    =>  isset($msg_status) ? view_status($msg_status) : null,

                'FILE_INI'  =>  FILE_INI,
                ));

        $tpl->parse('Hdlaff_conf', 'aff_conf', true);
        break;

    #   Les fichiers anonymes
    case 'anon':

        $msg = null;

        $lobj = new obj(get_anon_path());
        $lobj->loadRights();

        switch ($url->getParam('aff', 3)) {
            #   Téléchargement
            case 'download':
                if (file::getRealFile($url->getParam('obj'), get_anon_path())) {
                    file::sendFile(get_anon_path() . $url->getParam('obj'));
                    system::end();
                }
                break;

            #   Accepttation d'un fichier anonyme
            case 'accept':
                $ret = $lobj->acceptAnonFile($url->getParam('obj'), FOLDER_ROOT);
                if ($ret) {
                    $msg = __('File was moved in %s', $ret);
                }
                break;

            #   Suppression d'un fichier
            case 'del':
                $file = $lobj->getInfo($url->getParam('obj'), false, false);
                if ($file->type != TYPE_UNKNOW) {
                    $lobj->delete($file);
                }
        }

        $lobj->view_hidden_file = true;
        $tab = $lobj->getDirContent('/', SORT_DATE, 0, 10000, -1, array(array('=', 'type', TYPE_FILE), array('!', 'name', '.htaccess')));
        $wrap = $lobj->getAnonFile();
        $size = sizeof($tab);
        if ($size) {
            for ($i = 0, $cmpt = 0; $i < $size; $i++) {
                $path = (isset($wrap[$tab[$i]->file])) ? $wrap[$tab[$i]->file] : '/';
                $tpl->set_var(array(
                        'FILE_PATH'         =>  $path,
                        'FILE_ICON'         =>  $tab[$i]->icon,
                        'FILE_NAME'         =>  $tab[$i]->name,
                        'FILE_SIZE'         =>  get_human_size_reading($tab[$i]->size),
                        'FILE_DATE'         =>  format_date(filectime($tab[$i]->realpath), 1),
                        'FILE_DESCRIPTION'  =>  ($tab[$i]->info->description) ? $tab[$i]->info->description : __('No description !'),
                        'PATH_DOWNLOAD'     =>  $url->linkToPage(array('admin', 'anon', 'download'), $tab[$i]->file),
                        'ADMIN_ANON_DEL'    =>  $url->linkToPage(array('admin', 'anon', 'del'), $tab[$i]->file),
                        'ADMIN_ANON_MOVE'   =>  $url->linkToPage(array('admin', 'anon', 'accept'), $tab[$i]->file),
                        ));

                $tpl->parse('Hdlanon_line', 'anon_line', true);
                $cmpt++;
            }
            $tpl->parse('Hdlanon_list', 'anon_list', true);
        }

        $tpl->set_var('MSG', (!isset($cmpt) && !$msg) ? __('There are no file !') : $msg);

        unset($lobj);
        $tpl->parse('Hdlanon', 'anon', true);

        break;

    #   Test si une version plus récente existe
    case 'testver':

        if (ini_get('allow_url_fopen')) {
            $var = file::getContent(URL_TEST_VERSION);
            if ($var) {
                $res = strcmp(trim(HYLA_VERSION), trim($var));
                if ($res < 0) {
                    $msg = __('A new version ( %s ) is disponible !', $var);
                } else {
                    $msg = __('You have the latest version !');
                }
            }
        }

        $tpl->set_var('STATUS_VERSION', $msg);

    #   Accueil
    default:

        if (ini_get('allow_url_fopen'))
            $tpl->parse('Hdltest_version', 'test_version', true);

        $ok = $tpl->get_var('test_ok');
        $no = $tpl->get_var('test_no');

        $tpl->set_var(array(

                'CONFIG_FILE'               =>  CONFIG_FILE,

                'FOLDER_ROOT'               =>  FOLDER_ROOT,
                'FOLDER_ROOT_READING'       =>  is_readable(FOLDER_ROOT) ? $ok : $no,
                'FOLDER_ROOT_WRITING'       =>  is_writable(FOLDER_ROOT) ? $ok : $no,

                'FOLDER_ROOT_ERROR_MSG'     =>  !is_readable(FOLDER_ROOT) ? view_error(__('The root dir (FOLDER_ROOT) is not readable, please, edit FOLDER_ROOT constant in configuration file %s.', CONFIG_FILE)) : null,

                'PATH_TO_SCRIPT'            =>  dirname($_SERVER['SCRIPT_FILENAME']),

                'WEBMASTER_MAIL'            =>  $conf['webmaster_mail'] ? __('Webmaster mail is %s', $conf['webmaster_mail']) : __('Webmaster mail is not set !'),

                'CONFIG_ALLOW_URL_FOPEN'    =>  ini_get('allow_url_fopen') ? $ok : $no,
                'CONFIG_FILE_UPLOADS'       =>  ini_get('file_uploads') ? $ok : $no,
                'CONFIG_UPLOAD_MAX_FILESIZE'=>  ini_get('upload_max_filesize'),

                'FILE_INI'                  =>  FILE_INI,
                'DIR_CACHE'                 =>  DIR_CACHE,
                'DIR_ANON'                  =>  DIR_ANON,

                'ACCESS_FILE_INI'           =>  is_writable(FILE_INI) ? $ok : $no,
                'ACCESS_DIR_CACHE'          =>  is_writable(get_cache_path()) ? $ok : $no,
                'ACCESS_DIR_ANON'           =>  is_writable(get_anon_path()) ? $ok : $no,

                'EXTENSION_GD'              =>  extension_loaded('gd') ? $ok : $no,
                'EXTENSION_EXIF'            =>  extension_loaded('exif') ? $ok : $no,
                'TEST_VERSION'              =>  $url->linkToPage(array('admin', 'testver'))
                ));

        $tpl->parse('Hdlaff_home', 'aff_home', true);
        break;
}



$tpl->set_var(array(
        'ADMIN_PAGE'                =>  $url->linkToPage('admin'),
        'ADMIN_PAGE_CONF'           =>  $url->linkToPage(array('admin', 'conf')),
        'ADMIN_PAGE_USERS'          =>  $url->linkToPage(array('admin', 'users')),
        'ADMIN_PAGE_GROUPS'         =>  $url->linkToPage(array('admin', 'groups')),
        'ADMIN_PAGE_RIGHTS'         =>  $url->linkToPage(array('admin', 'rights')),
        'ADMIN_PAGE_COMMENT'        =>  $url->linkToPage(array('admin', 'comment')),
        'ADMIN_PAGE_ANON'           =>  $url->linkToPage(array('admin', 'anon')),
        'ADMIN_PAGE_MAINTENANCE'    =>  $url->linkToPage(array('admin', 'maintenance')),
        'ERROR'                     =>  $msg_error,
        ));

$var_tpl = $tpl->parse('OutPut', 'admin');

?>
