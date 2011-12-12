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

class plugin_gui_rights extends plugin_gui {

    function plugin_gui_rights() {
       parent::plugin_gui();


    }
    function act() {
    
    }
    function aff() {
    
      $this->tpl->set_file('rights', 'tpl/rights.tpl');
      
      $this->tpl->set_block('rights', array(
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

        'block_rights'                    =>  'Hdlblock_rights',
        ));
       plugin_obj::addStyleSheet('default.css');
    
       
       $msg = null;
       $msg_error = null;
       
       $usr = new users();

        $param = $this->url->getParam('aff', 3);
        switch ($param) {

            // Bêta test : réparation des droits
            case 'repair':
                $this->obj->findError(true);
                $param = null;
                break;

            // Ajout d'un droit
            case 'add':

                if ($this->url->getParam('aff', 4) == 'save') {

                    // Calcul des droits
                    $right = $this->obj->calculateRights($_POST['rgt_value']);

                    if (isset($_POST['rgt_users'])) {
                        foreach ($_POST['rgt_users'] as $user) {
                            $this->obj->addRight($_POST['rgt_dir'], $user, $right);
                        }
                        $param = null;
                        break;
                    } else {
                        $msg_error = __('You did not select user !');
                        $this->url->getParam('aff', 4, 'next');
                    }
                }

                // Étape 2 de l'ajout, on choisi les utilisateurs parmis ceux restant et les droits
                $obj_dir = (isset($_POST['rgt_dir'])) ? $_POST['rgt_dir'] : $this->url->getParam('obj');
                $obj_dir = stripslashes($obj_dir);
                $obj_dir = file::getRealDir($obj_dir, $this->obj->getRoot());

                if ($this->url->getParam('aff', 4) == 'next' && $obj_dir && is_dir($this->obj->getRoot().$obj_dir)) {

                    // On affiche les utilisateurs et les groupes
                    $tab = $usr->getUsers(true);
                    $tab_exist = $this->obj->getObjRights($obj_dir);

                    $size = sizeof($tab);
                    for ($i = 0; $i < $size; $i++) {
                        if (!array_key_exists($tab[$i]->name, $tab_exist)) {
                            $this->tpl->set_var(array(
                                    'USER_ID'   =>  $tab[$i]->id,
                                    'USER_NAME' =>  $tab[$i]->type == USR_TYPE_GRP ? '['.$tab[$i]->name.']' : $tab[$i]->name,
                                    ));
                            $this->tpl->parse('Hdlrights_add_2_user', 'rights_add_2_user', true);
                        }
                    }
                    unset($tab);

                    // On affiche les droits
                    foreach($this->obj->acl_rights as $val => $name) {
                        if (constant($val) & AC_VIEW)
                            continue;
                        $this->tpl->set_var(array(
                                'RIGHT_VALUE'   =>  constant($val),
                                'RIGHT_NAME'    =>  $name,
                                ));
                        $this->tpl->parse('Hdlrights_add_2_right', 'rights_add_2_right', true);
                    }

                    $this->tpl->set_var(array(
                            'ERROR'             =>  $msg_error ? view_error($msg_error) : null,
                            'FORM_RIGHTS_ADD'   =>  $this->url->linkToPage(array('admin', 'rights', 'add', 'save'), $obj_dir),
                            'OBJECT'            =>  $obj_dir,
                            ));

                    $this->tpl->parse('Hdlrights_add_2', 'rights_add_2', true);

                } else {
                    // Étape 1 : On choisi le dossier sur lequel appliquer les droits
                    $tab = $this->obj->scanDir($this->obj->getRoot(),null, true, true);
                    asort($tab);

                    // Les dossiers
                    foreach ($tab as $occ) {
                        $this->tpl->set_var('DIR_NAME', $occ);
                        $this->tpl->parse('Hdlrights_add_1_dir_occ', 'rights_add_1_dir_occ', true);
                    }

                    $this->tpl->set_var(array(
                            'FORM_RIGHTS_ADD'   =>  $this->url->linkToPage(array('admin', 'rights', 'add', 'next')),
                            ));

                    $this->tpl->parse('Hdlrights_add_1', 'rights_add_1', true);
                }

                break;

            // Édition des droits
            case 'edit':

                // Sauvergarde de l'édition
                if ($this->url->getParam('aff', 4) == 'save') {

                    list($obj_id, $user_id) = explode('|', $this->url->getParam('aff', 5));

                    // Calcul des droits
                    $right = $this->obj->calculateRights($_POST['rgt_value']);
                    $this->obj->setRight($obj_id, $user_id, $right);

                    $param = null;
                    break;
                }

                list($obj_id, $user_id) = explode('|', $this->url->getParam('aff', 4));

                $iuser = $usr->getUser($user_id);
                
                $ifile = $this->obj->getFile($obj_id);
                if ($iuser && $ifile) {

                    $this->tpl->set_var(array(
                            'USER_NAME'     =>  $iuser->name,
                            'CURRENT_DIR'   =>  $ifile,
                            ));

                    $right = $this->obj->getRightsFromUserAndPath($user_id, $ifile);

                    foreach($this->obj->acl_rights as $val => $name) {
                        if (constant($val) & AC_VIEW)
                            continue;
                        $this->tpl->set_var('Hdlrights_edit_right_selected');
                        if ($right & constant($val)) {
                            $this->tpl->parse('Hdlrights_edit_right_selected', 'rights_edit_right_selected', true);
                        }
                        $this->tpl->set_var(array(
                                'RIGHT_VALUE'   =>  constant($val),
                                'RIGHT_NAME'    =>  $name,
                                ));
                        $this->tpl->parse('Hdlrights_edit_right', 'rights_edit_right', true);
                    }

                    // Affichage des case à cocher View
                    if ($right & AC_VIEW) {
                        $this->tpl->parse('Hdlrights_edit_selected_view', 'rights_edit_selected_view', true);
                    } else {
                        $this->tpl->parse('Hdlrights_edit_disabled_multiple', 'rights_edit_disabled_multiple', true);
                    }

                    $this->tpl->set_var('FORM_RIGHTS_EDIT', $this->url->linkToPage(array('admin', 'rights', 'edit', 'save', $obj_id.'|'.$user_id)));

                    $this->tpl->parse('Hdlrights_edit', 'rights_edit', true);
                }
                break;

            // Suppression d'un ou de plusieurs droits
            case 'del':
                if ($_POST['right_id']) {
                    foreach ($_POST['right_id'] as $right) {
                        list($obj_id, $user_id) = explode('|', $right);
                        $this->obj->delRight($obj_id, $user_id);
                    }
                }
                $param = null;
                break;
        }

        // Affichage du tableau des droits
        if (!$param) {
            //$tab = $this->obj->getAllRights($this->cobj->file);
            $tab = $this->obj->getAllRights();
//DBUG($this);
            // Y-a-t-il des erreurs dans les droits ?
            $t_error = $this->obj->findError();
            if ($t_error) {
                $this->tpl->set_var('URL_RIGHTS_REPAIR', $this->url->linkToPage(array('admin', 'rights', 'repair')));
                $this->tpl->parse('Hdlrights_error', 'rights_error', true);
            }

            if ($tab) {
                $last_obj = 0;
                foreach ($tab as $occ) {

                    // Affichage des erreurs
                    $this->tpl->set_var('Hdlrights_list_line_error');
                    if ($t_error && array_key_exists($occ['obj_file'], $t_error) && array_key_exists($occ['user_id'], $t_error[$occ['obj_file']])) {
                        $this->tpl->parse('Hdlrights_list_line_error', 'rights_list_line_error', true);
                    }

                    // Le dossier n'existe plus ?
                    if (!file_exists($this->obj->getRoot().$occ['obj_file'])) {
                        continue;
                    }

                    $this->tpl->set_var('Hdlrights_list_header');
                    $this->tpl->set_var('Hdlrights_list_line');

                    if ($last_obj != $occ['obj_id']) {
                        $this->tpl->set_var(array(
                                'OBJ_NAME'          =>  $occ['obj_file'],
                                'URL_OBJ'           =>  $this->url->linkToObj($occ['obj_file']),
                                'URL_RIGHTS_ADD'    =>  $this->url->linkToPage(array('admin', 'rights', 'add', 'next'), $occ['obj_file']),
                                ));
                        $this->tpl->parse('Hdlrights_list_header', 'rights_list_header', true);
                    }

                    $this->tpl->set_var(array(
                            'USER_ID'           =>  $occ['user_id'],
                            'USER_NAME'         =>  $occ['user_type'] == USR_TYPE_GRP ? '['.$occ['user_name'].']' : $occ['user_name'],
                            'RIGHTS'            =>  $this->obj->getTextRights($occ['perm']),
                            'URL_RIGHTS_EDIT'   =>  $this->url->linkToPage(array('admin', 'rights', 'edit', $occ['obj_id'].'|'.$occ['user_id'])),
                            'RIGHT_ID'          =>  $occ['obj_id'].'|'.$occ['user_id'],
                            ));

                    $this->tpl->parse('Hdlrights_list_line', 'rights_list_line', true);
                    $this->tpl->parse('Hdlrights_list', 'rights_list', true);
                    $last_obj = $occ['obj_id'];
                }

                $this->tpl->set_var('FORM_RIGHT_DEL', $this->url->linkToPage(array('admin', 'rights', 'del')));

                $this->tpl->parse('Hdlrights_list_tab', 'rights_list_tab', true);
            } else {
                $msg = __('No right was found in all the filesystem !');
            }
        }

        $this->tpl->set_var(array(
                'MSG'               =>  $msg,
                'URL_RIGHTS_ADD'    =>  $this->url->linkToPage(array('admin', 'rights', 'add')),
                'URL_RIGHTS_LIST'   =>  $this->url->linkToPage(array('admin', 'rights')),
                ));

        $this->tpl->parse('Hdlblock_rights', 'block_rights', true);
      
      

        return $this->tpl->parse('OutPut', 'rights');
    }
}

?>

