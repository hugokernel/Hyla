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

class grp {

    /*  Ajoute un groupe
        @param  string  $name       Le nom
        @return Renvoie l'id du nouveau groupe
     */
    public function addGroup($name) {
        $ret = null;
        $name = strtolower($name);
        $sql = "INSERT INTO {$this->_users_table}
                (usr_name, usr_type)
                VALUES
                ('$name', '".USR_TYPE_GRP."');";
        if (!$var = $this->_bdd->execQuery($sql))
            trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
        $ret = $this->_bdd->getInsertID();
        return $ret;
    }

    /*  Retourne la structure tUser contenant les infos des groupes
     */
    public function getGroups() {
        $tab = array();
        $sql = "SELECT  usr_id, usr_name, usr_type
                FROM    {$this->_users_table}
                WHERE   usr_type = '".USR_TYPE_GRP."'
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

    /*  Retourne les utilisateurs appartenant à un groupe
        @param  int $id Id du groupe
     */
    public function getUsersGroup($id) {
        $tab = array();
        $sql = "SELECT  usr_id, usr_name, usr_type
                FROM    {$this->_users_table}
                        LEFT JOIN {$this->_grp_usr_table} ON usr_id = grpu_usr_id
                WHERE   grpu_grp_id = '$id'
                ORDER   BY usr_name ASC";
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

    /*  Retourne les utilisateurs n'appartenant pas à un groupe
        @param  int $id Id du groupe
     */
    public function getUsersNotInGroup($id) {
        $tab = array();
        $sql = "SELECT usr_id, usr_name, usr_type
                FROM {$this->_users_table}
                WHERE usr_name NOT IN (SELECT usr_name FROM {$this->_users_table} LEFT JOIN {$this->_grp_usr_table} ON usr_id = grpu_usr_id WHERE grpu_grp_id = '$id')
                AND usr_type IN ('".USR_TYPE_USER."', '".USR_TYPE_SUPERVISOR."', '".USR_TYPE_ADMIN."')";
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

    /*  Ajoute un utilisateur dans un groupe
        @param  int     $id     L'id du groupe
        @param  string  $name   L'id de l'utilisateur
     */
    public function addUserInGroup($grp_id, $usr_id) {
        $sql = "INSERT INTO {$this->_grp_usr_table}
                (grpu_usr_id, grpu_grp_id)
                VALUES
                ('$usr_id', '$grp_id');";
        if (!$var = $this->_bdd->execQuery($sql))
            trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
        return $var;
    }

    /*  Supprimer un utilisateur d'un groupe
        @param  int     $id     L'id du groupe
        @param  string  $name   L'id de l'utilisateur
     */
    public function delUserInGroup($grp_id, $usr_id) {
        $sql = "DELETE
                FROM    {$this->_grp_usr_table}
                WHERE   grpu_grp_id = '$grp_id' AND grpu_usr_id = '$usr_id'";
        if (!$var = $this->_bdd->execQuery($sql))
            trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
        return $var;
    }
}
