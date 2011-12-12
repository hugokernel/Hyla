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

class plugin_ws_user extends plugin_ws {

    /**
     *  Constructor
     */
    function plugin_ws_user() {
        parent::plugin_ws();
    }

    /**
     *  Set email
     *  @param  string  $email      New email
     *  @param  string  $user_id    User id
     */
    function setEmail($email, $user_id = -1) {
        global $cuser;
        $usr = new users();
        
        if ($user_id == -1) {
            $user_id = $cuser->id;
        } else {
            if (!$cuser || !in_array($cuser->type, array(USR_TYPE_SUPERVISOR, USR_TYPE_ADMIN))) {

                return new tError(__('You do not have the sufficient rights !'), $this);
            }
        }

        if (in_array($user_id, array(AUTHENTICATED_ID, ANONYMOUS_ID, ANY_ID))) {
           // return $this->error(__('Invalid user id password !'));
             return new tError(__('Invalid user id password !'), $this);
        }

        if (empty($email)) {
            return new tError(__('All the fields must be filled'), $this); 
           // return $this->error(__('All the fields must be filled'));
        }

        if (!$usr->checkEmail($email) ) {
             return new tError(__('Invalid email (%s)', $email),$this);
        }


        // Ok, password modification !
       
        if (!$usr->setEmail($user_id, $email)) {
         return new tError(__('Unknow error while set "%s" user email !', $user_id), $this);
        }

        return true;
    }

    /**
     *  Set password
     *  @param  string  $password   New password
     *  @param  mixed   $user_id    User id
     */
    function setPassword($password, $user_id = -1) {
        global $cuser;

        if ($user_id == -1) {
            $user_id = $cuser->id;
        } else {
            if (!$cuser || !in_array($cuser->type, array(USR_TYPE_SUPERVISOR, USR_TYPE_ADMIN))) {
                return new tError(__('You do not have the sufficient rights !'), $this);
            }
        }

        if (in_array($user_id, array(AUTHENTICATED_ID, ANONYMOUS_ID, ANY_ID))) {
            return new tError(__('Invalid user id password !'), $this);
        }

        if (empty($password)) {
            return new tError(__('All the fields must be filled'), $this);
        }
        
        if (strlen($password) < MIN_PASSWORD_SIZE) {
            return new tError(__('Password must have at least %s characters !', MIN_PASSWORD_SIZE), $this);
        }

        // Ok, password modification !
        $usr = new users();
        if (!$usr->setPassword($user_id, $password)) {
            return new tError(__('Unknow error while set "%s" user password !', $user_id), $this);
        }

        return true;
    }

    /**
     *  Set password with token
     *  @param  string  $user_id    User id
     *  @param  string  $token      Token
     *  @param  string  $password   New password
     */
    function setPasswordWithToken($username, $token, $password) {
/*
        if (in_array($user_id, array(AUTHENTICATED_ID, ANONYMOUS_ID, ANY_ID))) {
            return new tError(__('Invalid user id password !'), $this);
        }
*/
        if (empty($password) || empty($token)) {
            return new tError(__('All the fields must be filled'), $this);
        }
        
        if (strlen($password) < MIN_PASSWORD_SIZE) {
            return new tError(__('Password must have at least %s characters !', MIN_PASSWORD_SIZE), $this);
        }

        // Ok, password modification !
        $usr = new users();
        if (!$usr->setPassword($username, $password, $token)) {
            return new tError(__('Unknow error while set "%s" user password !', $username), $this);
        }

        return true;
    }

    /*  Modifie le type d'un utilisateur
        @param  int     $id     L'utilisateur
        @param  string  $type   Le type
     */
    function setType($user_id = -1,$type) {
        global $cuser;

        $usr = new users();
        if ($user_id == -1) {
            $user_id = $cuser->id;
        } else {
            if (!$cuser || !in_array($cuser->type, array(USR_TYPE_SUPERVISOR, USR_TYPE_ADMIN))) {
                return new tError(__('You do not have the sufficient rights !'), $this);
            }
        }

        $tab_type = array(0 => USR_TYPE_USER, 1 => USR_TYPE_SUPERVISOR, 2 => USR_TYPE_ADMIN);
        if (!array_key_exists($type, $tab_type))
            $type = 0;

        if ($user_id == $cuser->id) {
            $msg = view_error(__('Unable to change his own administration permission !'));
        } else {
            $ret  = $usr->setType($user_id, $tab_type[$type]);
        }
        
     
        return $ret;
    }

    /**
     *  Authenticate an user
     *  @param  string  $username   User name
     *  @param  string  $password   Password
     */
    function auth($username, $password) {

        if (empty($username) || empty($password)) {
            return new tError(__('All the fields must be filled'), $this);
        }

        $act = plugins::get(PLUGIN_TYPE_AUTH);
        if (!$ret = $act->auth($username, $password)) {
            return new tError(__('Error during authentification !'), $this);
        }

        session_regenerate_id();
        $_SESSION['sess_cuser_id'] = $ret->id;
        $_SESSION['sess_cuser_date_last_login'] = $ret->date_last_login;

        return session_id();
    }

    /**
     *  Log out current user
     */
    function logOut() {
        global $auth;
        return $auth->logout();
    }
  
    
    /**
     *  Add user
     *  @param  string  $username   User name
     *  @param  string  $email      Email
     *  @param  string  $password   Password
     */
    function add($username, $email, $password = null) {
        
        $ret = false;
        
        $usr = new users();
        
        // Test user name
        if (!$usr->checkLogin($username)) {
            return new tError(__('Invalid user name (%s)', $username), $this);
        }

        // Test email
        if (!$usr->checkEmail($email)) {
            return new tError(__('Invalid email (%s)', $email), $this);
        }
        
        // If password is null, generate it !
        if (!$password) {
            for ($i = 0; $i < 8; $i++) {
                $number = round(rand(33, 122), 0);
                $password .= chr($number);
            }
        }
        
        // Then, create user !
        if (!$usr->addUser($username, $password, $email)) {
            return new tError(__('Unknow error while add user "%s" !', $username), $this);
        }
        
        return $ret;
    }

    /**
     *  Send a lost password request to user
     *  @param  string  $username   User name
     *  @param  string  $email      Email
     */
    function createLostPasswordToken($username, $email) {

        if (empty($username) || empty($email)) {
            return new tError(__('All the fields must be filled'), $this);
        }

        // Verify if it is a existing user
        $usr = new users();
        $user = $usr->getUser($username);
        if (!$user || $user->email != $email) {
            return new tError(__('Error while getting user information !'), $this);
        }

        return $usr->createLostPasswordToken($user->id);
    }

    /**
     *  Test token
     *  @param  string  $username   User name
     *  @param  string  $token      Token
     */
    function testLostPasswordToken($username, $token) {

        if (empty($username) || empty($token)) {
            return new tError(__('All the fields must be filled'), $this);
        }

        // Verify if it is a existing user
        $usr = new users();
        $user = $usr->getUser($username);
        if (!$user || $user->lost_pass_token == null || $user->lost_pass_token != $token) {
            return new tError(__('Error while getting user information !'), $this);
        }

        return true;

    }
}

?>
