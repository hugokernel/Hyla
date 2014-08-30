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

require 'src/inc/grp.class.php';

class tUser {
    var $id;        // Id
    var $name;      // Nom
    var $type;      // Type
}

class users extends grp {

    private $_bdd;

    private $_users_table;

    public function __construct() {
        global  $bdd;
		
		parent::__construct();
		
        $this->_bdd = &$bdd;
        $this->_users_table = TABLE_USERS;
        $this->_grp_usr_table = TABLE_GRP_USR;
    }

    /*  Tente d'authentifier l'utilisateur
        @param  string  $login      Le login
        @param  string  $password   Le mot de passe
        @return Null en cas d'erreur sinon, renvoie un objet tUser
     */
    public function auth($login, $password) {
        $ret = null;

        if (users::testLex($login)) {
            $sql = "SELECT  usr_id, usr_name, usr_password_hash, usr_type
                    FROM    {$this->_users_table}
                    WHERE   usr_name = '$login' AND usr_type IN (".USR_TYPE_USER.",".USR_TYPE_SUPERVISOR.",".USR_TYPE_ADMIN.")";
            if (!$var = $this->_bdd->execQuery($sql))
                trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
            else {
                $res = $this->_bdd->fetchAssoc($var);
                if (crypt($password, CRYPT_SALT) == $res['usr_password_hash']) {
                    $ret = new tUser;
                    $ret->id = $res['usr_id'];
                    $ret->name = $res['usr_name'];
                    $ret->type = $res['usr_type'];
                }
            }
        }

        return $ret;
    }

    /*  Ajoute un utilisateur
        @param  string  $name       Le nom
        @param  string  $password   Le mot de passe en clair
        @return Renvoie l'id du nouvel utilisateur
     */
    public function addUser($name, $password) {
        $ret = null;
        $name = strtolower($name);
        $password = crypt($password, CRYPT_SALT);
        $sql = "INSERT INTO {$this->_users_table}
                (usr_name, usr_password_hash, usr_type)
                VALUES
                ('$name', '$password', '".USR_TYPE_USER."');";
        if (!$var = $this->_bdd->execQuery($sql))
            trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
        $ret = $this->_bdd->getInsertID();
        return $ret;
    }

    /*  Retourne la structure tUser contenant les infos des utilisateurs
        @param  bool    $grp    Retourner également les groupes
     */
    public function getUsers($grp = false) {
        $tab = array();
        $str = !$grp ? '    WHERE usr_type != '.USR_TYPE_GRP : null;
        $sql = "SELECT  usr_id, usr_name, usr_type
                FROM    {$this->_users_table}
                $str
                ORDER   BY usr_id ASC";
        if (!$var = $this->_bdd->execQuery($sql))
            trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
        for ($i = 0; $res = $this->_bdd->nextTuple($var); $i++) {
            $tab[$i] = new tUser;
            $tab[$i]->id = $res['usr_id'];
            $tab[$i]->name = $res['usr_name'];
            $tab[$i]->type = $res['usr_type'];
        }
        return $tab;
    }

    /*  Retourne un tableau contenant la liste des utilisateurs
        @param  int $id L'id de l'utilisateur voulu ou son nom
     */
    public function getUser($id) {
        $ret = null;
        $rsql = (is_numeric($id)) ? "usr_id = '$id'" : "usr_name = '$id'";
        $sql = "SELECT  usr_id, usr_name, usr_type
                FROM    {$this->_users_table}
                WHERE   $rsql";
        if (!$var = $this->_bdd->execQuery($sql))
            trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
        else {
            $res = $this->_bdd->fetchAssoc($var);
            if ($res) {
                $ret = new tUser;
                $ret->id = $res['usr_id'];
                $ret->name = $res['usr_name'];
                $ret->type = $res['usr_type'];
            }
        }

        return $ret;
    }

    /*  Modifie le type d'un utilisateur
        @param  int     $id     L'utilisateur
        @param  string  $type   Le niveau
     */
    public function setType($id, $type) {
        $sql = "UPDATE {$this->_users_table} SET usr_type = '".intval($type)."' WHERE usr_id = '".intval($id)."'";
        if (!$var = $this->_bdd->execQuery($sql))
            trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
    }

    /*  Modifie le mot de passe de l'utilisateur
        @param  int     $id         L'utilisateur
        @param  string  $password   Le mot de passe
     */
    public function setPassword($id, $password) {
        $password = crypt($password, CRYPT_SALT);
        $sql = "UPDATE {$this->_users_table} SET usr_password_hash = '$password' WHERE usr_id = '".intval($id)."'";
        if (!$var = $this->_bdd->execQuery($sql))
            trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
        return (bool)$var;
    }

    /*  Supprime un utilisateur
        @param  int $id L'id de l'utilisateur
     */
    public function delUser($id) {
        global $obj;
        $sql = "DELETE  usr, grp_usr
                FROM    {$this->_users_table} usr
                        LEFT JOIN {$this->_grp_usr_table} grp_usr ON grp_usr.grpu_usr_id = usr.usr_id
                WHERE   grp_usr.grpu_usr_id = '".intval($id)."' OR usr.usr_id = '".intval($id)."'";
        $obj->delUserRights($id);
        if (!$ret = $this->_bdd->execQuery($sql))
            trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
    }

    /*  Validation des noms des users à l'inscription
        @param  string  $login  Login à valider
        return -1 si le login n'est pas valide, 0 si l'utilisateur existe déjà, 1 si tout est ok
     */
    public function testLogin($login) {
        $ret = -1;
        if (!empty($login) && users::testLex($login)) {
            $ret = (!$s = $this->getUser($login)) ? 1 : 0;
        }
        return $ret;
    }

    /*  Test lexical du login
        @param  string  $login  Le login à tester
     */
    public function testLex($login) {
        // Récupère le plugin auth pour accéder au testLex
        $auth = plugins::get(PLUGIN_TYPE_AUTH, $conf['plugin_default_auth']);
        return $auth->testLex($login);
    }
}
