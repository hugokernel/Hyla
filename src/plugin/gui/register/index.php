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

class plugin_gui_register extends plugin_gui {

    /**
     *  Constructor
     */
    function plugin_gui_register() {
        parent::plugin_gui();
        
        $this->events['onsuccess'] =    array(
                                            'msg'       =>  __('Account created !'),
                                            'redirect'  =>  'current'
                                        );
   }

    function act() {
        $ret = false;

        if ($_POST) {

            global $cuser;

            if (empty($_POST['user_name']) ||  empty($_POST['user_email']) || empty($_POST['user_password']) || empty($_POST['user_password_bis'])) {
                return new tError(__('All the fields must be filled'), $this);
            }
            
            if ($_POST['user_password'] != $_POST['user_password_bis']) {
                return new tError(__('Passwords are different'), $this);
            }

            $act = plugins::get(PLUGIN_TYPE_WS, 'user');
            $ret = $act->run('add', array('username' => $_POST['user_name'], 'email' =>  $_POST['user_email'], 'password' => $_POST['user_password']));
            /*
            if (system::isError($res)) {
                return $res;    //$act->getLastError();
            }
            
            $ret = true;
            */
        }

        return $ret;
    }

    function aff() {

        $this->tpl->set_file('register', 'tpl/register.tpl');

        $this->tpl->set_var(array(
            'URL_REGISTER'  =>  $this->url->linkToPage('register'),
            'ERROR'         =>  view_error($this->getLastErrorMsg()),
        ));

        return $this->tpl->parse('OutPut', 'register');
    }
}

?>
