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

class plugin_gui_lostpass extends plugin_gui {

    /**
     *  Constructor
     */
    function plugin_gui_lostpass() {
        parent::plugin_gui();
   }

    function act() {
        $ret = false;

        if ($_POST) {

            // Step ?
            if (isset($_POST['action']) && $_POST['action'] == 'modify') {

                $act = plugins::get(PLUGIN_TYPE_WS, 'user');
                $res = $act->run('testLostPasswordToken', array(
                    'username'  =>  $this->url->getParam('aff', 3),
                    'token'     =>  $this->url->getParam('aff', 4)
                ));
                if (system::isError($res)) {
                    return $res;
                }

                if ($_POST['user_password'] != $_POST['user_password_bis']) {
                    return new tError(__('Passwords are different'), $this);
                }

                $res = $act->run('setPasswordWithToken', array(
                    'username'  =>  $this->url->getParam('aff', 3),
                    'token'     =>  $this->url->getParam('aff', 4),
                    'password'  =>  $_POST['user_password']
                ));
                if (system::isError($res)) {
                    return $res;
                }

                // Redirect user...
                $this->events['onsuccess'] =    array(
                                                    'msg'       =>  __('Now, log in !'),
                                                    'redirect'  =>  $this->url->linkToPage('auth'),
                                                );
                 return true;

            } else {

                $act = plugins::get(PLUGIN_TYPE_WS, 'user');
                $res = $act->run('createLostPasswordToken', array(
                    'username' => $_POST['user_name'],
                    'email' => $_POST['user_email'])
                );
                if (system::isError($res)) {
                    return $res;    //$act->getLastError();
                }
                
                system::mail(   $_POST['user_email'],
                                __('Lost password'),
                                __('Click on the link for modify password : %s',
                                $this->url->linkToPage(array('lostpass', 'token', $_POST['user_name'], $res))), $this->conf->get('webmaster_mail'));

                $this->events['onsuccess'] =    array(
                                                    'msg'       =>  __('An email was sent to you with instruction !'),
                                                    'redirect'  =>  'last'
                                                );
                return true;
            }
        }

        return $ret;
    }

    function aff() {

        $this->tpl->set_root($this->plugin_dir.'lostpass');
        $this->tpl->set_file('lostpass', 'tpl/lostpass.tpl');
        $this->tpl->set_block('lostpass', array(
                'first_step' => 'Hdlfirst_step',
                'final_step' => 'Hdlfinal_step',
                ));

        // Reset password request !
        if ($this->url->getParam('aff', 2) == 'token') {

            $act = plugins::get(PLUGIN_TYPE_WS, 'user');
            $res = $act->run('testLostPasswordToken', array('username' => $this->url->getParam('aff', 3), 'token' => $this->url->getParam('aff', 4)));
            if (system::isError($res)) {
                $this->tpl->parse('Hdlfirst_step', 'first_step', true);
            } else {
                $this->tpl->set_var(array(
                    'USER_NAME'             =>  $this->url->getParam('aff', 3),
                    'URL_PASSWORD_MODIFY'   =>  $this->url->linkToPage(array('lostpass', 'token', $this->url->getParam('aff', 3), $this->url->getParam('aff', 4))),
                ));

                $this->tpl->parse('Hdlfinal_step', 'final_step', true);
                $this->tpl->set_var('ERROR', view_error($this->getLastErrorMsg()));
            }
        } else {
            $this->tpl->parse('Hdlfirst_step', 'first_step', true);
        }

        if ($this->getLastError()) {    // Uggly, test return code !
            $this->tpl->set_var('ERROR', view_error($this->getLastErrorMsg()));
        }

        $this->tpl->set_var(array(
                'URL_LOST_PASS' =>  $this->url->linkToPage('lostpass'),
                'PATH_2_PLUGIN' =>  $this->_url_2_plugin,
                ));

        return $this->tpl->parse('OutPut', 'lostpass');
    }
}

?>
