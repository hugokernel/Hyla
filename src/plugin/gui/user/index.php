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

class plugin_gui_user extends plugin_gui {

    /**
     *  Constructor
     */
    function plugin_gui_user() {
        parent::plugin_gui();
   }

    function act() {
        $ret = false;

        if ($_POST) {

            global $cuser;

            if (empty($_POST['user_password']) || empty($_POST['user_password_bis'])) {
                return new tError(__('All the fields must be filled'), $this);
            }

            // Test login
            $act = plugins::get(PLUGIN_TYPE_WS, 'user');
            if (!$act->run('auth', array('username' => $cuser->name, 'password' => $_POST['user_password_current']))) {
                return new tError(__('Error during authentification !', $cuser->name), $this);
            }

            if ($_POST['user_password'] != $_POST['user_password_bis']) {
                return new tError(__('Passwords are different'), $this);
            }

            if ($act->run('setpassword', array('password' => $_POST['user_password']))) {
                $this->last_status = __('Password changed !');
                $ret = true;
            }

            if ($act->status) {
                $this->last_error = $act->status;
            }
        }

        return $ret;
    }

    function aff() {

        $this->tpl->set_file('user', 'tpl/user.tpl');

        $this->tpl->set_var(array(
            'URL_CHANGE_PASSWORD'   =>  $this->url->linkToCurrentObj('user'),
            'MSG'                   =>  ($this->last_error) ? view_error($this->last_error) : view_status($this->last_status),
        ));

        return $this->tpl->parse('OutPut', 'user');
    }
}

?>
