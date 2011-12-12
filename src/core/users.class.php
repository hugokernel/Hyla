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

require HYLA_ROOT_PATH.'src/core/grp.class.php';

class tUser {
    var $id;                // Id
    var $name;              // Nom
    var $type;              // Type
    var $email;             // Email
    var $lost_pass_token;   // Token
    var $date_last_login;   // Date last login
}

class users extends grp {

    var     $bdd;

    var     $_users_table;
    var     $_grp_usr_table;

    function users() {
        $this->bdd = plugins::get(PLUGIN_TYPE_DB);
        $this->_users_table = TABLE_USERS;
        $this->_grp_usr_table = TABLE_GRP_USR;
    }

    /*  Tente d'authentifier l'utilisateur
        @param  string  $login      Le login
        @param  string  $password   Le mot de passe
        @return Null en cas d'erreur sinon, renvoie un objet tUser
     */
    function auth($login, $password) {
        $ret = null;

        if (users::testLogin($login)) {
            $sql = "SELECT  usr_id, usr_name, usr_password_hash, usr_type, usr_email, usr_date_last_login
                    FROM    {$this->_users_table}
                    WHERE   usr_name = '$login' AND usr_type IN (".USR_TYPE_USER.",".USR_TYPE_SUPERVISOR.",".USR_TYPE_ADMIN.")";
            if ($var = $this->bdd->execQuery($sql)) {
                $res = $this->bdd->fetchAssoc($var);
                if (crypt($password, CRYPT_SALT) == $res['usr_password_hash']) {
                    $ret = new tUser;
                    $ret->id = $res['usr_id'];
                    $ret->name = $res['usr_name'];
                    $ret->type = $res['usr_type'];
                    $ret->email = $res['usr_email'];
                    $ret->date_last_login = $res['usr_date_last_login'];
                }

                // Update date last login
                $this->bdd->execQuery("UPDATE {$this->_users_table} SET usr_date_last_login = '".system::time()."' WHERE usr_id = '{$ret->id}'");
            }
        }

        return $ret;
    }

    /**
     *  Add user
     *  @param  string  $name       User name
     *  @param  string  $password   Password
     *  @param  string  $email      Email
     *  @return New user id
     */
    function addUser($name, $password, $email) {
        $ret = 0;

        $name = strtolower($name);

        // Test user name
        if ($this->checkLogin($name) < 1) {
            system::log(L_FATAL, __('Invalid user name (%s)', $name));
        }

        // Test email
        if (!$this->checkEmail($email)) {
            system::log(L_FATAL, __('Invalid email (%s)', $email));
        }

        $password = crypt($password, CRYPT_SALT);
        $sql = "INSERT INTO {$this->_users_table}
                (usr_name, usr_password_hash, usr_type, usr_email, usr_date_create)
                VALUES
                ('$name', '$password', '".USR_TYPE_USER."', '$email', '".system::time()."');";
        if ($var = $this->bdd->execQuery($sql)) {
            $ret = $this->bdd->getInsertID();
        }
        return $ret;
    }

    /*  Retourne la structure tUser contenant les infos des utilisateurs
        @param  bool    $grp    Retourner également les groupes
     */
    function getUsers($grp = false) {
        $tab = array();
        $str = !$grp ? '    WHERE usr_type != '.USR_TYPE_GRP : null;
        $sql = "SELECT  usr_id, usr_name, usr_type, usr_email
                FROM    {$this->_users_table}
                $str
                ORDER   BY usr_id ASC";
        if ($var = $this->bdd->execQuery($sql)) {
            for ($i = 0; $res = $this->bdd->nextTuple($var); $i++) {
                $tab[$i] = new tUser;
                $tab[$i]->id = $res['usr_id'];
                $tab[$i]->name = $res['usr_name'];
                $tab[$i]->type = $res['usr_type'];
                $tab[$i]->email = $res['usr_email'];
            }
        }
        return $tab;
    }

    /*  Retourne un tableau contenant la liste des utilisateurs
        @param  int $id L'id de l'utilisateur voulu ou son nom
     */
    function getUser($id) {
        $ret = null;
        $rsql = (is_numeric($id)) ? "usr_id = '$id'" : "usr_name = '$id'";
        $sql = "SELECT  usr_id, usr_name, usr_type, usr_email, usr_lost_pass_token
                FROM    {$this->_users_table}
                WHERE   $rsql";
        if ($var = $this->bdd->execQuery($sql)) {
            $res = $this->bdd->fetchAssoc($var);
            if ($res) {
                $ret = new tUser;
                $ret->id = (int)$res['usr_id'];
                $ret->name = $res['usr_name'];
                $ret->type = $res['usr_type'];
                $ret->email = $res['usr_email'];
                $ret->lost_pass_token = $res['usr_lost_pass_token'];
            }
        }

        return $ret;
    }

    /**
     *  Create token user
     *  @param  mixed   $user   User id or username
     */
    function createLostPasswordToken($user) {
        $ret = 0;
        $token = system::getUniqueID(16);
        $qry = (is_numeric($user)) ? "usr_id = '$user'" : "usr_name = '$user'";
        $sql = "UPDATE {$this->_users_table} SET usr_lost_pass_token = '$token' WHERE $qry";
        if ($this->bdd->execQuery($sql)) {
            $ret = $token;
        }
        return $ret;
    }

    /*  Modifie le type d'un utilisateur
        @param  int     $id     L'utilisateur
        @param  string  $type   Le type
     */
    function setType($id, $type) {
        $sql = "UPDATE {$this->_users_table} SET usr_type = '".intval($type)."' WHERE usr_id = '".intval($id)."'";
        return $this->bdd->execQuery($sql);
    }

    /**
     *  Set email
     *  @param  int     $id     User id
     *  @param  string  $email  New email
     */
    function setEmail($id, $email) {

        // Test email
        if ($this->checkEmail($email)) {
            system::log(L_FATAL, __('Invalid email (%s)', $email));
        }

        $sql = "UPDATE {$this->_users_table} SET usr_email = '$email' WHERE usr_id = '".intval($id)."' AND usr_type IN (".USR_TYPE_USER.", ".USR_TYPE_SUPERVISOR.", ".USR_TYPE_ADMIN.")";
        return $this->bdd->execQuery($sql);
    }

    /*  Modifie le mot de passe de l'utilisateur
        @param  mixed   $user       L'utilisateur
        @param  string  $password   Le mot de passe
        @param  string  $token      Token (lorsque le user à perdu son pass)
     */
    function setPassword($user, $password, $token = null) {

        $password = crypt($password, CRYPT_SALT);
        if ($token) {
            $qry = "UPDATE {$this->_users_table} SET usr_password_hash = '$password', usr_lost_pass_token = '' WHERE usr_name = '$user' AND usr_type IN (".USR_TYPE_USER.", ".USR_TYPE_SUPERVISOR.", ".USR_TYPE_ADMIN.") AND usr_lost_pass_token = '$token'";
        } else {
            $qry = "UPDATE {$this->_users_table} SET usr_password_hash = '$password' WHERE usr_id = '".intval($user)."' AND usr_type IN (".USR_TYPE_USER.", ".USR_TYPE_SUPERVISOR.", ".USR_TYPE_ADMIN.")";
        }

        return $this->bdd->execQuery($qry);
    }

    /*  Supprime un utilisateur
        @param  int $id L'id de l'utilisateur
     */
    function delUser($id) {
        global $obj;
        $sql = "DELETE  usr, grp_usr
                FROM    {$this->_users_table} usr
                        LEFT JOIN {$this->_grp_usr_table} grp_usr ON grp_usr.grpu_usr_id = usr.usr_id
                WHERE   grp_usr.grpu_usr_id = '".intval($id)."' OR usr.usr_id = '".intval($id)."'";
        $obj->delUserRights($id);
        return $this->bdd->execQuery($sql);
    }

    /*  Validation des noms des users à l'inscription
        @param  string  $login  Login à valider
        return -1 si le login n'est pas valide, 0 si l'utilisateur existe déjà, 1 si tout est ok
     */
    function checkLogin($login) {
        $ret = -1;
        if (!empty($login) && users::testLogin($login)) {
            $ret = (!$this->getUser($login)) ? 1 : 0;
        }
        return $ret;
    }

    /**
     *  Email test
     *  @param  string  $email  Email
     */
    function checkEmail($email) {
        return preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9._-]+)+$/", $email);
    }

    /**
     *  Lexical login test
     *  @param  string  $login  Login to test
     *  @return false if bad format
     */
    function testLogin($login) {
        return preg_match('/^[A-Zéèçà]{1}[A-Zéèçà0-9._-]{1,31}$/i', $login);
    }
}

?>
