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
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Hyla; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA     02111-1307  USA
 */

require 'src/db/ldap.class.php';

class usersldap extends users {

    /* Tente d'authentifier l'utilisateur en se basant sur l'annuaire.
        @param  string  $login      Le login
        @param  string  $password   Le mot de passe
        @return Null en cas d'erreur sinon, renvoie un objet tUser
     */
    function auth($login, $password) {
        $ret = null;

        $ldap = new ldap();

        if (!$ldap->bind(LDAP_HOST, LDAP_RDNATTR.'=$login,'.LDAP_BASEUSER, $password)) {
            return null;
        }

        $sql = "SELECT usr_id, usr_name, usr_type
                FROM    {$this->_users_table}
                WHERE usr_name = '$login' AND usr_type IN (".USR_TYPE_USER.",".USR_TYPE_SUPERVISOR.",".USR_TYPE_ADMIN.")";
        if (!$var = $this->_bdd->execQuery($sql))
            trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
        else {
            if (!$this->_bdd->getNumRows($var)) {   // L'utilisateur est authentifié mais n'existe pas dans la base

                // Nous ne devons pas le créer automatiquement
                if (!LDAP_AUTOCREATE)
                    return null;

                //Création automatique de l'utilisateur
                $sql = "INSERT INTO hyla_users (usr_name, usr_type) VALUES ('" . addslashes($login) . "', " . USR_TYPE_USER . ");";
                if (!$var = $this->_bdd->execQuery($sql)) {
                    trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
                    return null;
                }

                $ret = new tUser;
                $ret->id = $this->_bdd->getInsertID();
                $ret->name = $login;
                $ret->type = USR_TYPE_USER;

            } else {
                $res = $this->_bdd->fetchAssoc($var);
                $ret = new tUser;
                $ret->id = $res['usr_id'];
                $ret->name = $res['usr_name'];
                $ret->type = $res['usr_type'];
            }
        }

        return $ret;
    }
}

?>
