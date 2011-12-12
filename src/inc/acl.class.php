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

    You should have received a copy of the GNU General Public Licensetod
    along with Hyla; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

class acl {

    var $acl_rights;

    var $_origin;       // L'origine, pour les problèmes de droits

    function acl() {

        // Tableau des droits
        $this->acl_rights = array(
                'AC_NONE'               =>  __('None'),
                'AC_VIEW'               =>  __('View'),

                'AC_ADD_COMMENT'        =>  __('Add comment'),
                'AC_EDIT_DESCRIPTION'   =>  __('Description edition'),
                'AC_EDIT_PLUGIN'        =>  __('Plugin edition'),
                'AC_EDIT_ICON'          =>  __('Icon edition'),
                'AC_ADD_FILE'           =>  __('Add file'),
                'AC_CREATE_DIR'         =>  __('Make dir'),
                'AC_COPY'               =>  __('Copy'),
                'AC_MOVE'               =>  __('Move'),
                'AC_RENAME'             =>  __('Rename'),
                'AC_DEL_DIR'            =>  __('Dir deletion'),
                'AC_DEL_FILE'           =>  __('File deletion'),
                );

        $this->_origin = null;
    }

    /*  Get all ACL
     */
    function getAcl() {
        $out = array();
        foreach ($this->acl_rights as $name => $txt) {
            if (constant($name)) {
                $out[$name] = $txt;
            }    
        }
        return $out;
    }

    /*  Charge tous les droits
        @param  bool    $group  Si true, retourne les droits avec les id des groupes, sinon des utilisateurs
     */
    function loadRights($group = false) {
        $this->_all_rights = null;
        $sql = "SELECT  obj_file, usr_type, usr_id, usr_name, usr_type, ac_obj_id, ac_usr_id, ac_rights, grpu_usr_id, grpu_grp_id
                FROM    {$this->_acontrol_table}    LEFT JOIN {$this->_grp_usr_table} ON ac_usr_id = grpu_grp_id
                                                    LEFT JOIN {$this->_users_table} ON ac_usr_id = usr_id
                                                    LEFT JOIN {$this->_object_table} ON obj_id = ac_obj_id
                ORDER   BY obj_file ASC, usr_type ASC, ac_usr_id ASC";      // Ne pas changer l'ordre du tri, c'est déterminant pour les priorités !
        if (!$var = $this->_bdd->execQuery($sql))
            trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
        for ($i = 0; $res = $this->_bdd->nextTuple($var); $i++) {
            if ($group) {
                if ($res['grpu_usr_id']) {
                    $this->_all_rights[$res['obj_file']][$res['grpu_grp_id']] = $res['ac_rights'];
                } else {
                    $this->_all_rights[$res['obj_file']][$res['usr_id']] = $res['ac_rights'];
                }
            } else {
                $userid = ($res['usr_type'] == USR_TYPE_GRP) ? $res['grpu_usr_id'] : $res['usr_id'];
                $this->_all_rights[$res['obj_file']][$userid] = $res['ac_rights'];
            }
        }
    }

    /*  Renvoie les droits pour le chemin spécifié
        @param  string  $path   Le chemin
     */
    function _getRight4Path($path) {
        $rights = array();
        if (array_key_exists($path, $this->_all_rights)) {
            $rights = $this->_all_rights[$path];
        }
        return $rights;
    }

    /*  Remet les droits en ordre
     */
    function findError($repair = false) {

        $final_errors = null;

        $this->loadRights(true);

        // Lecture de tous les droits
        foreach ($this->_all_rights as $path => $rights) {

            // Lecture des associations utilisateurs / droits
            foreach ($rights as $userid => $right) {

                $i = 0;
                $toto = $path;

                // Si on est dans un dossier ayant un droit alors que ce dernier ne serait pas accessible
                while ($toto && ($right & AC_VIEW) && !($this->getUserRights4Path($toto, $userid) & AC_VIEW)) {

                    // Une erreur à été trouvée, on test le parent
                    $parent = $this->getParentHaveRights($toto, $userid);

                    $toto = $parent;

                    // On récupère les droits du premier dossier parent pour l'utilisateur courant, c'est à dire, celui qui bloque ...
                    $this->_origin = null;
                    $rright_parent = $this->getUserRights4Path($parent, $userid);

                    // ... et si il n'a pas le droit de visualisation
                    if (!($rright_parent & AC_VIEW)) {

                        if (!$parent) {
                            $parent = '/';
                        }

                        // Analyse du problème par rapport à l'origine
                        switch ($this->_origin['user_id']) {
                            case ANY_ID:
                                switch ($userid) {
                                    case ANY_ID:
                                        $final_errors[$path][$userid] = 'normal';
                                        $this->_addError($parent, ANY_ID);
                                        break;
                                    default:
                                        $final_errors[$path][$userid] = 'normal';
                                        $this->_addError($parent, $userid);
                                        break;
                                }
                                break;
                            case ANONYMOUS_ID:
                                $final_errors[$path][$userid] = 'normal';
                                $this->_addError($parent, ANONYMOUS_ID);
                                break;
                            case AUTHENTICATED_ID:
                                switch ($userid) {
                                    case AUTHENTICATED_ID:
                                        $final_errors[$path][$userid] = 'normal';
                                        $this->_addError($parent, AUTHENTICATED_ID);
                                        break;
                                    default:
                                        $final_errors[$path][$userid] = 'normal';
                                        $this->_addError($parent, $userid);
                                        break;
                                }
                                break;
                            default:
                                $final_errors[$path][$userid] = 'normal';
                                $this->_addError($parent, $userid);
                                break;
                        }
                    }
                }
            }
        }

        // Réparation des droits
        if ($repair) {
            foreach ($this->_error_rights as $dir => $userid) {
                foreach ($userid as $id => $type) {
                    if ($type == 'c') {
                        $this->addRight($dir, $id, AC_VIEW);
                    } else {
                        $this->setRight($dir, $id, AC_VIEW);
                    }
                }
            }
        }

        $this->loadRights();

        return $final_errors;
    }

    /*  Renvoie les droits de l'utilisateur courant du dossier spécifié
        @param  string  $path   Le chemin
     */
    function getCUserRights4Path($path) {
        global $cuser;

        if (!array_key_exists($path, $this->_cache_rights)) {
            if ($cuser->type == USR_TYPE_ADMIN || $cuser->type == USR_TYPE_SUPERVISOR) {
                $right =    AC_VIEW | AC_ADD_COMMENT | AC_EDIT_DESCRIPTION | AC_EDIT_PLUGIN |
                            AC_ADD_FILE | AC_CREATE_DIR | AC_COPY | AC_MOVE | AC_RENAME | AC_DEL_DIR | AC_DEL_FILE;
            } else {
                $right = $this->getUserRights4Path($path, $cuser->id);
            }
            $this->_cache_rights[$path] = $right;
        } else {
            $right = $this->_cache_rights[$path];
        }

        return $right;
    }

    /*  Renvoie les droits de l'utilisateur spécifié et du dossier spécifié
        @param  string  $path       Le chemin
        @param  int     $cuserid    L'id de l'utilisateur
     */
    function getUserRights4Path($path, $cuserid) {

        $right = 0;

        $dir = null;
        $dirs = explode('/', $path);

        $dirs[0] = '/';
        array_pop($dirs);

        $cmpt = 0;
        foreach($dirs as $occ) {
            $dir .= ($occ == '/') ? '/' : $occ.'/';

            if (array_key_exists($dir, $this->_all_rights)) {
                $right = 0;
                $found = false;
                foreach ($this->_all_rights[$dir] as $userid => $r) {

                    if ($userid == ANY_ID) {
                        $right = $r;
                        $this->_origin['user_id'] = $userid;
                        $found = true;
                    }

                    if ($userid == AUTHENTICATED_ID && $cuserid != ANONYMOUS_ID) {
                        $right = $r;
                        $this->_origin['user_id'] = $userid;
                        $found = true;
                    }

                    if ($userid == ANONYMOUS_ID && $cuserid == ANONYMOUS_ID) {
                        $right = $r;
                        $this->_origin['user_id'] = $userid;
                        $found = true;
                    }

                    if ($userid == $cuserid) {
                        $right = $r;
                        $this->_origin['user_id'] = $userid;
                        $found = true;
                        break;
                    }
                }

                if (!($right & AC_VIEW) && $cmpt && $found) {
                    break;
                }

            } else if ($cmpt >= 1 && !$right) {
                break;
            }

            $cmpt++;
        }

        return $right;
    }

    /*  Renvoie les droits de l'utilisateur dans le dossier
        @param  int     $usr_id L'id de l'utilisateur
        @param  string  $path   Le chemin
     */
    function getRightsFromUserAndPath($usr_id, $path) {
        $right = 0;
        $dir = null;
        $tab = array();
        $sql = "SELECT  obj_file, ac_usr_id, ac_rights
                FROM    {$this->_object_table} INNER JOIN {$this->_acontrol_table}
                ON      obj_id = ac_obj_id
                WHERE   ac_usr_id = '$usr_id' AND obj_file = '".obj::format($path)."'
                ORDER   BY obj_file, ac_usr_id ASC";
        if (!$var = $this->_bdd->execQuery($sql))
            trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
        for ($i = 0; $res = $this->_bdd->nextTuple($var); $i++) {
            if (!array_key_exists($res['obj_file'], $tab)) {
                $tab[$res['obj_file']] = null;
            }
            $right |= $res['ac_rights'];
        }
        return $right;
    }

    /*  Renvoie le premier dossier parent comportant des droits
        @param  string  $path       Le chemin enfant
        @param  int     $userid     Concernant cet utilisateur
     */
    function getParentHaveRights($path, $userid = 0) {
        $parent = null;
        $found = false;

        $p = strrpos($path, '/');
        $path = substr($path, 0, $p);

        while ($path) {
            $p = strrpos($path, '/');
            $path = substr($path, 0, $p);
            if (array_key_exists($path.'/', $this->_all_rights)) {
                $parent = $path.'/';
                if ($userid) {
                    foreach ($this->_all_rights[$path.'/'] as $id => $right) {
                        if ($id == ANY_ID) {
                            $found = true;
                            break;
                        }

                        if ($id == AUTHENTICATED_ID && $userid != ANONYMOUS_ID) {
                            $found = true;
                            break;
                        }

                        if ($id == ANONYMOUS_ID && $userid == ANONYMOUS_ID) {
                            $found = true;
                            break;
                        }

                        if ($id == $userid) {
                            $found = true;
                            break;
                        }
                    }

                    if ($found)
                        break;
                } else
                    break;
            }
        }

        return $parent;
    }

    /*  Ajoute un droit
        @param  string  $file       Le dossier
        @param  int     $user_id    L'id de l'utilisateur
        @param  int     $perm       Le droit
     */
    function addRight($file, $user_id, $perm) {
        $id = $this->getId($file);
        if ($id) {
            $sql = "INSERT INTO {$this->_acontrol_table}
                    (ac_usr_id, ac_obj_id, ac_rights)
                    VALUES
                    ('$user_id', '$id', '$perm');";
            if (!$var = $this->_bdd->execQuery($sql))
                trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
        }
    }

    /*  Spécifie un droit
        @param  string  $file       Le dossier
        @param  int     $user_id    L'id de l'utilisateur
        @param  int     $perm       Le droit
     */
    function setRight($file, $user_id, $perm) {
        $id = (is_numeric($file)) ? $file : $this->getId($file);
        if ($id) {
            $sql = "UPDATE {$this->_acontrol_table} SET ac_rights = '$perm' WHERE ac_obj_id = '$id' AND ac_usr_id = '$user_id'";
            if (!$var = $this->_bdd->execQuery($sql))
                trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
        }
    }

    /*  Supprime un droit
        @param  mixed   $file       Le dossier (int ou string)
        @param  int     $user_id    L'id de l'utilisateur
     */
    function delRight($file, $user_id) {
        $id = (is_numeric($file)) ? $file : $this->getId($file);
        $sql = "DELETE
                FROM    {$this->_acontrol_table}
                WHERE   ac_obj_id = '{$id}' AND ac_usr_id = '$user_id'";
        if (!$var = $this->_bdd->execQuery($sql))
            trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
    }

    /*  Supprime tous les droits de l'objet courant
     */
    function delRights() {
        $sql = "DELETE
                FROM    {$this->_acontrol_table}
                WHERE   ac_obj_id = '{$this->_current_obj->info->id}'";
        if (!$var = $this->_bdd->execQuery($sql))
            trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
    }

    /*  Supprimer à un utilisateur ou à un groupe des droits
        @param  int $user_id    Id
     */
    function delUserRights($user_id) {
        $sql = "DELETE
                FROM    {$this->_acontrol_table}
                WHERE   ac_usr_id = '$user_id'";
        if (!$var = $this->_bdd->execQuery($sql))
            trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
    }

    /*  Renvoie tous les utilisateurs et groupes de l'objet
        @param  string  $file   Le chemin
     */
    function getObjRights($file) {
        global $cuser;
        $tab = array();
        $sql = "SELECT  obj_file, usr_id, usr_name, usr_type, ac_obj_id, ac_usr_id, ac_rights, grpu_usr_id, grpu_grp_id
                FROM {$this->_acontrol_table}   LEFT JOIN {$this->_grp_usr_table} ON ac_usr_id = grpu_grp_id
                                                LEFT JOIN {$this->_users_table} ON ac_usr_id = usr_id
                                                LEFT JOIN {$this->_object_table} ON obj_id = ac_obj_id
                WHERE   obj_file = '".obj::format($file)."'
                        AND usr_name != ''
                GROUP   BY usr_name
                ORDER   BY usr_type, usr_name ASC";
        if (!$var = $this->_bdd->execQuery($sql))
            trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
        for ($i = 0; $res = $this->_bdd->nextTuple($var); $i++) {
            $tab[$res['usr_name']] = array('id' =>  $res['usr_id'], 'perm' => $res['ac_rights'], 'type' => $res['usr_type']);
        }
        return $tab;
    }

    /*  Renvoie tous les droits
     */
    function getAllRights() {
        global $cuser;
        $tab = array();
        $sql = "SELECT  obj_id, obj_file, usr_id, usr_name, usr_type, ac_obj_id, ac_usr_id, ac_rights, grpu_usr_id, grpu_grp_id
                FROM {$this->_acontrol_table}   LEFT JOIN {$this->_grp_usr_table} ON ac_usr_id = grpu_grp_id
                                                LEFT JOIN {$this->_users_table} ON ac_usr_id = usr_id
                                                LEFT JOIN {$this->_object_table} ON obj_id = ac_obj_id
                GROUP   BY obj_id, usr_name
                ORDER   BY obj_file, usr_type ASC, usr_name ASC";
        if (!$var = $this->_bdd->execQuery($sql))
            trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
        for ($i = 0; $res = $this->_bdd->nextTuple($var); $i++) {
            $tab[] = array( 'obj_id'    =>  $res['obj_id'],
                            'obj_file'  =>  $res['obj_file'],
                            'user_id'   =>  $res['usr_id'],
                            'user_name' =>  $res['usr_name'],
                            'user_type' =>  $res['usr_type'],
                            'perm'      =>  $res['ac_rights']
                        );
        }
        return $tab;
    }

    /*  Renvoie la liste des droits de manière textuel
        @param  int $right  Les droits
     */
    function getTextRights($right) {
        global $conf;

        $str = null;

        if ($right) {
            foreach ($this->acl_rights as $key => $val) {
                if ($right & constant($key)) {
                    $str .= $val.', ';
                }
            }
            $str = $str ? substr($str, 0, -2) : null;
        } else
            $str = $this->acl_rights['AC_NONE'];

        return $str;
    }

    /*  Calcul les bons droits
        @param  array   $tab_rights Tableau des droits
     */
    function calculateRights($tab_rights) {
        $right = AC_NONE;

        if (isset($tab_rights)) {
            foreach ($tab_rights as $perm) {
                $right |= $perm;
            }
        }

        if (!($right & AC_VIEW)) {
            $right = AC_NONE;
        }

        return $right;
    }

    /*  Test les droits (sans paramètre, renvoie true si on est admin, sinon false)
        @access static
     */
    function ok() {
        global $obj, $cobj, $cuser;
        $ret = false;
        $auth_only = false;
        $admin_only = false;

        if ($cuser->type == USR_TYPE_ADMIN || $cuser->type == USR_TYPE_SUPERVISOR) {
            $arg = func_get_arg(0);
            if (is_string($arg) && $arg == ADMINISTRATOR_ONLY) {
                $ret = ($cuser->type == USR_TYPE_ADMIN) ? true : false;
            } else
                $ret = true;
        } else {
            $num = func_num_args();
            for ($i = 0; $i < $num; $i++) {
                $niv = func_get_arg($i);
                if ($niv && ($obj->getCUserRights4Path($cobj->path) & $niv)) {
                    $ret = true;
                    break;
                }
            }
        }

        return $ret;
    }


    /*  Ajoute une erreur
        @access private
     */
    function _addError($path, $userid) {
        if (!($this->_all_rights[$path][$userid] & AC_VIEW)) {
            /*  Simplification 1
                - Authenticated + Anonymous = All
             */
            if ($userid == ANONYMOUS_ID || $userid == AUTHENTICATED_ID) {

                if ($userid == ANONYMOUS_ID) {
                    $test0 = ANONYMOUS_ID;
                    $test1 = AUTHENTICATED_ID;
                } else if ($userid == AUTHENTICATED_ID) {
                    $test0 = AUTHENTICATED_ID;
                    $test1 = ANONYMOUS_ID;
                }

                if ($userid == $test0) {
                    if (array_key_exists($test1, $this->_error_rights[$path])) {
                        $this->_error_rights[$path] = null;
                        $userid = ANY_ID;
                    }
                }
            }

            /*  Simplification 2
                - userX + userY + userZ  = ANY
             */
            if ($userid == ANY_ID) {
                $this->_error_rights[$path] = null;
                /*  'c' ->  right to create
                    'm' ->  right to modify
                 */
                $this->_error_rights[$path][$userid] = (!array_key_exists($userid, $this->_all_rights[$path])) ? 'c' : 'm';
            }

            if (!array_key_exists(ANY_ID, $this->_error_rights[$path]))
                $this->_error_rights[$path][$userid] = (!array_key_exists($userid, $this->_all_rights[$path])) ? 'c' : 'm';
        }
    }
}

?>
