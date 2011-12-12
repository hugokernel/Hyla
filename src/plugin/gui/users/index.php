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

class plugin_gui_users extends plugin_gui {

    function plugin_gui_users() {
       parent::plugin_gui();

    }
    function act() {
      global $cuser;
      $ret = false;
      
      if ($_POST) {
      
        $usr = new users();
        switch ($this->url->getParam('aff', 3)) {
          case 'del':
                  // Pas le droit de s'autosupprimer, ni de supprimer les méta utilisateurs
                  if ($this->url->getParam('aff', 4) != $cuser->id) {
                      if (!in_array((int)$this->url->getParam('aff', 4), array(ANY_ID, AUTHENTICATED_ID, ANONYMOUS_ID), true))
                          $usr->delUser($this->url->getParam('aff', 4));
                  }
                  $param = null;
               break;
               
          case 'saveemail':
                  $act = plugins::get(PLUGIN_TYPE_WS, 'user');
                  $user_email_id = intval($this->url->getParam('aff', 4));
                  $ret = $act->run('setemail', array('email' => $_POST['ad_email'], 'user_id' => $user_email_id));
                  if ($ret) {
                    $this->last_status_msg = __('Email changed !');
                  }
                  if ($act->status) {
                    $this->last_error_msg = $act->status;
                  }

               break;
               
          case 'savetype':
     
                  $act = plugins::get(PLUGIN_TYPE_WS, 'user');
                  $user_id = intval($this->url->getParam('aff', 4));
                  $user_type = intval($_POST['ad_type']);
                  $ret = $act->run('settype', array('user_id' => $user_id,'type' => $user_type));
                  
                  if ($ret) {
                    $this->last_status_msg = __('Type changed !');
                  }
                  if ($act->status) {
                    $this->last_error_msg = $act->status;
                  }
               break;

          case 'savepassword':   
                  $act = plugins::get(PLUGIN_TYPE_WS, 'user');
                  $user_id = intval($this->url->getParam('aff', 4));
                  $password = $_POST['ad_password'];
                  $password_bis = $_POST['ad_password_bis'];
                  if ($password == $password_bis) {
                  $ret = $act->run('setpassword', array('password' => $password, 'user_id' =>$user_id ));
                  } else {
                  $this->last_error_msg = __('Passwords are different');
                  }
                  if ($ret) {
                    $this->last_status_msg = __('Password changed !');
                  }
                  if ($act->status) {
                    $this->last_error_msg = $act->status;
                  }
               break;
          
               
               
        }
      }
      return $ret;
    }
    function aff() {
    
      $this->tpl->set_root($this->plugin_dir.'users');
      $this->tpl->set_file('users', 'tpl/users.tpl');

      $this->tpl->set_block('users', array(
        'user_edit_password'        =>  'Hdluser_edit_password',
        'user_edit_email'           =>  'Hdluser_edit_email',
        'user_edit_type'            =>  'Hdluser_edit_type',
        'user_edit'                 =>  'Hdluser_edit',
        'user_add'                  =>  'Hdluser_add',
        'users_line_del'            =>  'Hdlusers_line_del',
        'users_line'                =>  'Hdlusers_line',
        'users_list'                =>  'Hdlusers_list',
        'block_users'               =>  'Hdlblock_users',
      ));
      plugin_obj::addStyleSheet('default.css');
      global $cuser;
      $msg = null;
      $msg_error = null;
      if (isset($this->last_status_msg)) {
      $msg =  view_status($this->last_status_msg);
      }
      
      if (isset($this->last_error_msg)) {
      $msg = view_error($this->last_error_msg);
      }
      
  
      $usr = new users();

        $param = $this->url->getParam('aff', 3);

        switch ($param) {

            case 'add':
            
                if ($this->url->getParam('aff', 4) == 'save') {
                    $ret = $usr->checkLogin($_POST['ad_login']);
                    $retemail = $usr->checkemail($_POST['ad_email']);
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
                    else if ($retemail == -1 || $retemail == 0) {
                        $msg_error = view_error(__('The email is invalid !'));
                        $_POST['ad_email'] = null;
                    } else {
                        $id = $usr->addUser($_POST['ad_login'], $_POST['ad_password'], $_POST['ad_email']);
                        $tab_type = array(0 => USR_TYPE_USER, 1 => USR_TYPE_SUPERVISOR, 2 => USR_TYPE_ADMIN);
                        if (!array_key_exists($_POST['ad_type'], $tab_type))
                            $_POST['ad_type'] = 0;

                        $usr->setType($id, $tab_type[$_POST['ad_type']]);
                        $param = null;
                        break;
                    }
                    $this->tpl->set_var('NAME', $_POST['ad_login']);
                    $this->tpl->set_var('EMAIL', $_POST['ad_email']);
                }

                $this->tpl->set_var(array(
                        'FORM_USER_SAVE'    =>  $this->url->linkToPage(array('admin', 'users', 'add', 'save')),
                        'ERROR'             =>  $msg_error,
                        ));
                $this->tpl->parse('Hdluser_add', 'user_add', true);
                break;

            case 'del':
                $param = null;
                break;

         
            case 'edit':
                $tab = $usr->getUser($this->url->getParam('aff', 4));
                if ($tab && !($tab->id == ANY_ID || $tab->id == ANONYMOUS_ID || $tab->id == AUTHENTICATED_ID)) {
                    $this->tpl->parse('Hdluser_edit_password', 'user_edit_password', true);
                    $this->tpl->parse('Hdluser_edit_email', 'user_edit_email', true);
                    
                    if ($tab->id != $cuser->id)
                        $this->tpl->parse('Hdluser_edit_type', 'user_edit_type', true);
                    

                    $this->tpl->set_var(array(
                            'USER_NAME'                 =>  $tab->name,

                            'SELECT_TYPE_USER'          =>  $tab->type == USR_TYPE_USER ? 'selected="selected"' : null,
                            'SELECT_TYPE_SUPERVISOR'    =>  $tab->type == USR_TYPE_SUPERVISOR ? 'selected="selected"' : null,
                            'SELECT_TYPE_ADMIN'         =>  $tab->type == USR_TYPE_ADMIN ? 'selected="selected"' : null,

                            'FORM_USER_EDIT_TYPE'       =>  $this->url->linkToPage(array('admin', 'users', 'savetype', $this->url->getParam('aff', 4))),
                            'FORM_USER_EDIT_EMAIL'      =>  $this->url->linkToPage(array('admin', 'users', 'saveemail', $this->url->getParam('aff', 4))),
                            'FORM_USER_EDIT_PASSWORD'   =>  $this->url->linkToPage(array('admin', 'users', 'savepassword', $this->url->getParam('aff', 4))),
                            'EMAIL'                     =>  $tab->email,
                            'MSG'                       =>  $msg,
                            ));

                    $this->tpl->parse('Hdluser_edit', 'user_edit', true);
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

                $this->tpl->set_var(array(
                        'Hdlusers_line_del' =>  null,
                        'USER_ID'           =>  $tab[$i]->id,
                        'USER_NAME'         =>  $tab[$i]->name,
                        'USER_TYPE'         =>  $tab_type[$tab[$i]->type],
                        'USER_EMAIL'        =>  $tab[$i]->email,
                        'ADMIN_USER_EDIT'   =>  $this->url->linkToPage(array('admin', 'users', 'edit', $tab[$i]->id)),
                        'ADMIN_USER_DEL'    =>  $this->url->linkToPage(array('admin', 'users', 'del', $tab[$i]->id)),
                        ));

                if ($tab[$i]->id != ANY_ID && $tab[$i]->id != ANONYMOUS_ID && $tab[$i]->id != AUTHENTICATED_ID) {
                    if ($tab[$i]->id != $cuser->id)
                        $this->tpl->parse('Hdlusers_line_del', 'users_line_del', true);
                }

                $this->tpl->parse('Hdlusers_line', 'users_line', true);
            }
            $this->tpl->parse('Hdlusers_list', 'users_list', true);
        }

        $this->tpl->set_var(array(
                'ADMIN_USER_ADD'    =>  $this->url->linkToPage(array('admin', 'users', 'add')),
                'ADMIN_USER_LIST'   =>  $this->url->linkToPage(array('admin', 'users')),
                ));

        $this->tpl->parse('Hdlblock_users', 'block_users', true);

        return $this->tpl->parse('OutPut', 'users');
    }
}

?>
