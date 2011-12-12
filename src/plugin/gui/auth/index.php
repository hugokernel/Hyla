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

class plugin_gui_auth extends plugin_gui {

    /**
     *  Constructor
     */
    function plugin_gui_auth() {
        parent::plugin_gui();
   }

    function act() {
        $ret = false;

        if ($_POST) {
            $act = plugins::get(PLUGIN_TYPE_WS, 'user');
            $res = $act->run('auth', array('username' => $_POST['user_name'], 'password' => $_POST['user_password']));
            if (system::isError($res)) {
                return $res;    //$act->getLastError();
            }

            $this->events['onsuccess'] =    array(
                                                'msg'       =>  array(
                                                                    __('You are now authenticated !'),
                                                                    __('Your last connection dated %s.', format_date($_SESSION['sess_cuser_date_last_login'], 1))
                                                                ),
                                                'redirect'  =>  'last'
                                            );

            $ret = true;
        }

        return $ret;
    }

    function aff() {

        $this->tpl->set_file('auth', 'tpl/auth.tpl');
        $this->tpl->set_block('auth', array(
            'register'  =>  'Hdlregister',
        ));

        if ($this->getLastError()) {    // Uggly, test return code !
            header('HTTP/1.x 401 Authorization Required');
            $this->tpl->set_var('ERROR', view_error($this->getLastErrorMsg()));
        }

        // View register
        if ($this->conf->get('register_user')) {
            $this->tpl->set_var('PAGE_REGISTER', $this->url->linkToPage('register'));
            $this->tpl->parse('Hdlregister', 'register', true);
        }

        $this->tpl->set_var(array(
                'PAGE_LOST_PASSWORD'    =>  $this->url->linkToPage('lostpass'),
                'URL_LOGIN'             =>  $this->url->linkToPage('auth'),
                'NAME'                  =>  (isset($_POST['user_name']) ? stripslashes(htmlentities($_POST['user_name'])) : null),
                'SUGGESTION'            =>  get_suggest(array(__('Saisissez votre mot de passe à l\'abri des regards indiscret !'))),
                ));

        return $this->tpl->parse('OutPut', 'auth');
    }
}

?>
