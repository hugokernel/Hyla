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

    You should have received a copy of the GNU General Public Licensetod
    along with Hyla; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

require 'src/inc/acl.class.php';
require 'src/inc/archive.class.php';

class tComment {
    var $id;
    var $author;
    var $mail;
    var $url;
    var $content;
    var $date;
}

/*  Information venant de la base de données
 */
class tFileInfo {
    var $id;                // L'id de la BDD
    var $date_last_update;  // Dernière modification
    var $description;       // Description de l'objet
    var $nbr_comment;       // Nombre de commentaires
    var $comment;           // Objet commentaire
    var $plugin;            // Le plugin par défaut (pour les répertoires)
    var $dcount;            // Nombre de téléchargement
    var $rights;            // Les droits

    /*  La seule info venant de la BDD ne se trouvant pas dans cette structure est
        icon car elle vient à la base du système de fichiers.
     */

    function tFileInfo() {
        $thid->date_last_update = 0;
        $this->description = null;
        $this->nbr_comment = 0;
        $this->comment = array();
        $this->plugin = null;
        $this->dcount = 0;
        $this->rights = 0;
    }
}

/*  Informations sur le nom, l'adresse et le type du fichier
 */
class tFile {
    var $type;          // Fichier, Répertoire, Archive
    var $path;          // Le chemin depuis la racine FOLDER_ROOT
    var $name;          // Le nom du fichier, Vide si répertoire
    var $file;          // path + name  (Très utile pour éviter d'avoir à faire des $path.$name par la suite)
    var $target;        // La cible lorsque l'on pointe à l'intérieur d'un fichier (fichier contenu dans une archive)
    var $extension;     // L'extension du fichier
    var $realpath;      // Le chemin depuis la racine du système /!\ ATTENTION /!\ Pour des raisons de sécurité, ce chemin ne doit jamais être montré au connecté

    var $cat;           // La catégorie du fichier (image, document...)

    var $size;          // La taille du fichier !
    var $icon;          // L'icone correspondante au type du fichier

    var $info;

    var $prev;          // Nom du fichier précédent
    var $next;          // Nom du fichier suivant

    function tFile() {

        $this->type = TYPE_UNKNOW;
        $this->path = '/';
        $this->name = null;
        $this->file = null;
        $this->target = null;
        $this->extension = null;
        $this->realpath = null;

        $this->cat = null;

        $this->size = null;
        $this->icon = null;

        $this->prev = null;
        $this->next = null;

        $this->info = new tFileInfo;
    }
}


class obj extends acl {

    var     $_bdd;              // L'objet base de données

    var     $_folder_root;      // La racines

    var     $_object_table;     // La table des objets
    var     $_comment_table;    // La table des commentaires

    var     $_current_obj;      // L'objet courant...

    var     $_tri;              // Le tri à appliquer

    var     $view_hidden_file;  // Voir les fichiers cachés ?

    var     $_nbr_object;       // Le nombre d'éléments total dans le répertoire courant (utile pour la pagination)

    var     $_cache_rights;     // Tableau contenant des infos de droits en cache
    var     $_all_rights;       // Tableau contenant tous les droits
    var     $_error_rights;     // Tableau contenant des erreurs dans les droits

    /*  Le constructeur
     */
    function obj($_folder_root) {

        global  $bdd, $conf;

        $this->_bdd = &$bdd;

        // Delete final /
        if ($_folder_root{strlen($_folder_root) - 1} == '/') {
            $_folder_root = substr($_folder_root, 0, strlen($_folder_root) - 1);
        }
        $this->_folder_root = $_folder_root;

        $this->_object_table = TABLE_OBJECT;
        $this->_acontrol_table = TABLE_ACONTROL;
        $this->_comment_table = TABLE_COMMENT;
        $this->_users_table = TABLE_USERS;
        $this->_grp_usr_table = TABLE_GRP_USR;

        $this->_nbr_object = 0;

        $this->_current_obj = null;

        $this->_tri = $conf['sort_config'];

        $this->_cache_rights = array();
        $this->_all_rights = array();
        $this->_error_rights = array();

        $this->acl();

        $this->view_hidden_file = $conf['view_hidden_file'];
    }

    /*  Renvoie un tableau contenant les informations sur un objet (fichier ou dossier)
        @param  string  $name   Le nom de l'objet
        @param  bool    $data   Récupérer les infos en base de données (true)
        @param  bool    $pnext  Récupère ou non les objet précédent et suivant
     */
    function getInfo($name, $data = true, $pnext = true) {

        global $conf;

        $obj = null;

        $target = null;
        @list($name, $target) = @explode('!', $name, 2);

        $name = stripslashes($name);

        $dir = file::getRealDir($name, $this->_folder_root);

        $rights = $this->getCUserRights4Path($dir);

        // L'utilisateur courant a-t-il le droit ?
        if ($dir && ($rights & AC_VIEW)) {

            $obj = new tFile;
            $obj->info->rights = $this->_getRight4Path($dir);

            // L'objet est contenu dans une archive
            if ($target) {

                $obj->type = TYPE_ARCHIVED;
                $obj->path = $dir;
                $obj->name = file::getRealFile($name, $this->_folder_root);
                $obj->file = $obj->path.$obj->name;
                $obj->target = $target;
                $obj->extension = file::getExtension(file::baseName($target));
                $obj->realpath = $this->_folder_root.$obj->file;
                $obj->icon = get_icon($obj->extension);

                if ($pnext) {
                    $ret = $this->getPrevNext($obj);
                    $obj->prev = ($ret['prev'] ? $this->getInfo($name.'!'.$ret['prev'], false, false) : null);
                    $obj->next = ($ret['next'] ? $this->getInfo($name.'!'.$ret['next'], false, false) : null);
                }

            // L'objet est un fichier
            } else if (is_file($this->_folder_root.$name)) {

                $obj->type = TYPE_FILE;
                $obj->path = $dir;
                $obj->name = file::getRealFile($name, $this->_folder_root);
                $obj->file = $obj->path.$obj->name;
                $obj->extension = file::getExtension($obj->name);
                $obj->realpath = $this->_folder_root.$obj->file;

                $obj->cat = get_cat($obj->extension);

                $obj->size = filesize($this->_folder_root.$obj->file);

                if ($data) {
                    $obj->info->nbr_comment = 0;

                    // On récupère la description de l'objet et les commentaires
                    $sql = "SELECT  obj_id, obj_date_last_update, obj_description, obj_plugin, obj_dcount, comment_id, comment_author, comment_mail, comment_url, comment_date, comment_content
                            FROM    {$this->_object_table} LEFT JOIN {$this->_comment_table} ON obj_id = comment_obj_id
                            WHERE   obj_file = '".obj::format($obj->file)."'";
                    if (!$var = $this->_bdd->execQuery($sql))
                        trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
                    for ($i = 0; $res = $this->_bdd->nextTuple($var); $i++) {
                        if (!$i) {
                            $obj->info->id = $res['obj_id'];
                            $obj->info->date_last_update = $res['obj_date_last_update'];
                            $obj->info->description = $res['obj_description'];
                            $obj->info->dcount = (int)$res['obj_dcount'];
                        }
                        if ($res['comment_id']) {
                            $obj->info->comment[$i]             = new tComment();
                            $obj->info->comment[$i]->id         = $res['comment_id'];
                            $obj->info->comment[$i]->author     = $res['comment_author'];
                            $obj->info->comment[$i]->mail       = $res['comment_mail'];
                            $obj->info->comment[$i]->url        = $res['comment_url'];
                            $obj->info->comment[$i]->content    = $res['comment_content'];
                            $obj->info->comment[$i]->date       = $res['comment_date'];
                            $obj->info->nbr_comment++;
                        }
                    }
                }

                $obj->icon = get_icon($obj->extension);

                if ($pnext) {
                    $ret = $this->getPrevNext($obj);
                    $obj->prev = ($ret['prev'] ? $this->getInfo($ret['prev'], false, false) : null);
                    $obj->next = ($ret['next'] ? $this->getInfo($ret['next'], false, false) : null);
                }

            // L'objet est un dossier
            } else if (is_dir($this->_folder_root.$name)) {

                $obj->type = TYPE_DIR;
                $obj->path = $dir;
                $obj->name = (file::baseName($name) == null) ? '/' : file::baseName($this->_folder_root.$name);     // ToDo: est-ce bien d'utiliser basename en lieu et place de getRealFile ?
                $obj->file = $obj->path;
                $obj->realpath = $this->_folder_root.$obj->file;

                if ($data) {
                    $obj->info->nbr_comment = 0;

                    // On récupère la description de l'objet et les commentaires
                    $sql = "SELECT  obj_id, obj_date_last_update, obj_description, obj_plugin, obj_icon, obj_dcount, comment_id, comment_author, comment_mail, comment_url, comment_date, comment_content
                            FROM    {$this->_object_table} LEFT JOIN {$this->_comment_table} ON obj_id = comment_obj_id
                            WHERE   obj_file = '".obj::format($obj->file)."'";
                    if (!$var = $this->_bdd->execQuery($sql))
                        trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
                    for ($i = 0; $res = $this->_bdd->nextTuple($var); $i++) {
                        if (!$i) {
                            $obj->info->id = $res['obj_id'];
                            $obj->info->date_last_update = $res['obj_date_last_update'];
                            $obj->info->description = $res['obj_description'];
                            $obj->info->dcount = (int)$res['obj_dcount'];
                            $obj->info->plugin = strtolower($res['obj_plugin'] ? $res['obj_plugin'] : null);    // -> On met null, c'est mieux pour le par défaut !

                            $obj->icon = ($res['obj_icon']) ? REAL_ROOT_URL.$res['obj_icon'] : null;
                        }

                        if ($res['comment_id']) {
                            $obj->info->comment[$i]             = new tComment();
                            $obj->info->comment[$i]->id         = $res['comment_id'];
                            $obj->info->comment[$i]->author     = $res['comment_author'];
                            $obj->info->comment[$i]->mail       = $res['comment_mail'];
                            $obj->info->comment[$i]->url        = $res['comment_url'];
                            $obj->info->comment[$i]->content    = $res['comment_content'];
                            $obj->info->comment[$i]->date       = $res['comment_date'];
                            $obj->info->nbr_comment++;
                        }
                    }
                }

                if (!$obj->icon) {
                    $icon = DIR_PLUGINS_OBJ.(($obj->info->plugin) ? $obj->info->plugin.'/' : '').DIR_ICON;
                    $obj->icon = REAL_ROOT_URL.(file_exists($icon) ? $icon : DIR_PLUGINS_OBJ.strtolower($conf['plugin_default_dir']).'/'.DIR_ICON);
                }
            }
        }

        if ($data && ($pnext || $obj->type == TYPE_DIR)) {
            $this->_current_obj = $obj;
        }

        return $obj;
    }

    /*  Renvoie un tableau contenant les nom des fichiers précédent et suivant en tenant compte du tri
        @param  string  $obj        L'objet
        @param  string  $archive    Spécifie si l'objet est dans une archive
     */
    function getPrevNext($obj) {

        global $conf;

        $ret = array('prev' => null, 'next' => null);

        if ($obj->type == TYPE_ARCHIVED) {
            $ret = archive::getPrevNext($obj->realpath, $obj->target);
        } else {
            $this->_tri = (isset($_SESSION['sess_sort'])) ? $_SESSION['sess_sort'] : $conf['sort_config'];  // ToDo : Remplacez $_SESSION[... par une variable de classe
            $tab = $this->getDirContent($obj->path, $this->_tri, 0, -1, -1, false);
            $size = sizeof($tab);
            for ($i = 0, $prev = null; $i < $size; $i++) {
                if ($tab[$i]->file == $obj->file) {
                    $ret['prev'] = $prev->file;
                    $ret['next'] = (($i + 1 >= $size) ? null : $tab[++$i]->file);
                    break;
                }
                $prev = $tab[$i];
            }
        }

        return $ret;
    }

    /*  Renvoie un tableau contenant toutes les informations sur un dossier ainsi que les fichiers et dossiers contenu dedans
        @param  string  $name       Le nom de l'objet
        @param  int     $tri        Le tri à appliquer
        @param  int     $start      A partir d'oû on commence à compter
        @param  int     $nbr        Le nombre d'occurence à afficher (si 0, config par défaut, si -1, affiche tout)
        @param  array   $tab        Tableau de données
        @param  array   $filter     Filtrage dans la structure tFile
     */
    function getDirContent($name, $tri = null, $start = 0, $nbr = 0, $tab = -1, $filter = null) {

        global  $bdd, $conf;

        if ($tri)
            $this->_tri = $tri;

        if ($nbr == 0)
            $nbr = $conf['nbr_obj'];

        $arr = array();

        //  Récupère les fichiers et répertoire du système de fichiers - Todo: C'est pas beau ce -1 !!
        if ($tab == -1) {
            $tab = obj::_getDirContent($name, $this->_folder_root);
        }

        $s = null;
        if ($tab) {
            foreach ($tab as $occ) {
                $s .= 'OR obj_file LIKE \'%'.obj::format($occ).'\' ';
            }
        }

        // On récupère les infos des objets en BDD
        $sql = "SELECT  obj_id, obj_date_last_update, obj_file, obj_description, obj_plugin, obj_icon, COUNT(comment_author) as nbr_comment
                FROM    {$this->_object_table} LEFT JOIN {$this->_comment_table}
                ON      comment_obj_id = obj_id
                WHERE   obj_file = '".obj::format($name)."' $s
                GROUP   BY obj_file
                ORDER   BY obj_file ASC";
        if (!$var = $bdd->execQuery($sql))
            trigger_error($bdd->getErrorMsg(), E_USER_ERROR);
        for ($i = 0; $res = $bdd->nextTuple($var); $i++) {
            $a[$res['obj_file']]['id'] = $res['obj_id'];
            $a[$res['obj_file']]['date_last_update'] = $res['obj_date_last_update'];
            $a[$res['obj_file']]['description'] = $res['obj_description'];
            $a[$res['obj_file']]['plugin'] = strtolower($res['obj_plugin']);
            $a[$res['obj_file']]['icon'] = strtolower($res['obj_icon']);
            $a[$res['obj_file']]['nbr_comment'] = $res['nbr_comment'];
        }

        // On mixe les données provenant du système de fichiers et de la base de données
        if ($tab) {
            $i = 0;
            foreach ($tab as $occ) {
                $tmp = $this->getInfo($occ, false, false);

                // Exécution des filtres
                if ($filter) {
                    if (!obj::filter($tmp, $filter)) {
                        continue;
                    }
                }

                if ($tmp) {
                    $tab_out[$i] = $tmp;
                    if (isset($a[$tab_out[$i]->file]['id'])) {
                        $tab_out[$i]->info->id = $a[$tab_out[$i]->file]['id'];
                        $tab_out[$i]->info->date_last_update = $a[$tab_out[$i]->file]['date_last_update'];
                        $tab_out[$i]->info->description = $a[$tab_out[$i]->file]['description'];
                        $tab_out[$i]->info->plugin = $a[$tab_out[$i]->file]['plugin'];

                        if ($tab_out[$i]->type == TYPE_DIR) {
                            if ($a[$tab_out[$i]->file]['icon'])
                                $tab_out[$i]->icon = REAL_ROOT_URL.$a[$tab_out[$i]->file]['icon'];
                            else {
                                $icon = DIR_PLUGINS_OBJ.$tab_out[$i]->info->plugin.'/'.DIR_ICON;
                                $tab_out[$i]->icon = REAL_ROOT_URL.(($tab_out[$i]->info->plugin) ? (file_exists($icon) ? $icon : get_icon('.')) : DIR_PLUGINS_OBJ.$conf['plugin_default_dir'].'/'.DIR_ICON);
                            }
                        }
                        $tab_out[$i]->info->nbr_comment = isset($a[$tab_out[$i]->file]['nbr_comment']) ? $a[$tab_out[$i]->file]['nbr_comment'] : null;
                    } else {
                        if ($tab_out[$i]->type == TYPE_DIR) {
                            $tab_out[$i]->icon = REAL_ROOT_URL.DIR_PLUGINS_OBJ.($tab_out[$i]->info->plugin ? $tab_out[$i]->info->plugin.'/' : '').$conf['plugin_default_dir'].'/'.DIR_ICON;
                        }
                    }
                    $i++;
                }
            }
            $tab = null;
        }

        if (isset($tab_out)) {
            // Et maintenant, on tri les données
            if ($tri & SORT_NAME_ALPHA || $tri & SORT_NAME_ALPHA_R)
                usort($tab_out, array($this, '_sort_name'));
            else if ($tri & SORT_EXT_ALPHA || $tri & SORT_EXT_ALPHA_R)
                usort($tab_out, array($this, '_sort_ext'));
            else if ($tri & SORT_CAT_ALPHA || $tri & SORT_CAT_ALPHA_R)
                usort($tab_out, array($this, '_sort_cat'));
            else if ($tri & SORT_SIZE || $tri & SORT_SIZE_R)
                usort($tab_out, array($this, '_sort_size'));
            else if ($tri & SORT_DATE || $tri & SORT_DATE_R)
                usort($tab_out, array($this, '_sort_date'));

            // Et on affiche uniquement certain
            if ($nbr != -1) {
                $tab = array();
                $this->_nbr_object = sizeof($tab_out);

                if ($start)
                    $start = ($start >= $this->_nbr_object) ? (($this->_nbr_object - $nbr < 0) ? 0 : $this->_nbr_object - 1) : $start;
                for ($i = 0, $c = 0, $cmpt = 0; $i < $this->_nbr_object; $i++) {            // ToDo : Nettoyer, Optimiser le code ici
                    if ($i >= $start) {
                        $tab[$c++] = &$tab_out[$i];
                        $cmpt++;
                    }
                    if ($nbr && $cmpt >= $nbr)
                        break;
                }
            } else
                $tab = $tab_out;
        }

        return $tab;
    }

    /*  Filtrage d'objet
        @param  obj     $obj    L'objet proposé
        @param  array   $filter Le filtrage à appliquer
                array(operator, op0, value) : voir _filter
                    * operator  : !, =
                    * op0       : Variable membre de tFile
                    * value     : Valeur quelconque
        @access static
     */
    function filter($obj, $filter) {
        $ok = false;
        if (is_array($filter[0])) {
            foreach ($filter as $f) {
                $ok = obj::_filter($obj, $f);
                if (!$ok)
                    break;
            }
        } else
            $ok = obj::_filter($obj, $filter);
        return $ok;
    }

    /*  Renvoie le nombre d'objet dans le répertoire courant (faire après getDirContent)
     */
    function getNbrObject() {
        return $this->_nbr_object;
    }

    /*  Ajout d'un commentaire à l'objet courant
        @param  string  $author L'auteur du commentaire
        @param  string  $mail   Son email
        @param  string  $url    Son site
        @param  string  $content Le contenu
     */
    function addComment($author, $mail, $url, $content) {
        $id = $this->getId($this->_current_obj->file);
        $sql = "INSERT INTO {$this->_comment_table}
                (comment_obj_id, comment_author, comment_mail, comment_url, comment_date, comment_content)
                VALUES
                ('$id', '$author', '$mail', '$url', '".system::time()."', '$content');";
        if (!$var = $this->_bdd->execQuery($sql))
            trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
        return $this->_bdd->getInsertID();
    }

    /*  Suppression d'un ou de plusieurs commentaires par leur ID
        @param  int $id L'id du commentaire
     */
    function delComment($id) {
        if (is_array($id)) {
            $id = implode(',', $id);
            $id = "comment_id IN ($id)";
        } else {
            $id = "comment_id = '$id'";
        }
        $sql = "DELETE
                FROM    {$this->_comment_table}
                WHERE   $id";
        if (!$ret = $this->_bdd->execQuery($sql))
            trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
    }

    /*  Renvoie les derniers commentaires...
     */
    function getLastComment() {
        global $conf;
        $tab = array();
        $sql = "SELECT  obj_file, obj_icon, obj_plugin, comment_id, comment_author, comment_mail, comment_url, comment_date, comment_content
                FROM    {$this->_object_table} INNER JOIN {$this->_comment_table}
                ON      obj_id = comment_obj_id
                ORDER   BY comment_date DESC";
        if (!$var = $this->_bdd->execQuery($sql))
            trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
        for ($i = 0; $res = $this->_bdd->nextTuple($var);) {
            if ($this->getCUserRights4Path($res['obj_file']) & AC_VIEW) {
                $tab[$i] = new tComment;
                $tab[$i]->object = $res['obj_file'];
                $tab[$i]->icon = $res['obj_icon'];
                $tab[$i]->id = $res['comment_id'];
                $tab[$i]->author = $res['comment_author'];
                $tab[$i]->mail = $res['comment_mail'];
                $tab[$i]->url = $res['comment_url'];
                $tab[$i]->date = $res['comment_date'];
                $tab[$i]->content = $res['comment_content'];

                // Get icon !
                if (is_dir(FOLDER_ROOT.$tab[$i]->object)) {
                    if ($tab[$i]->object == '/') {
                        $tab[$i]->icon = DIR_IMAGE.'/home.png';
                    } else {
                        if ($res['obj_icon']) {
                            $tab[$i]->icon = REAL_ROOT_URL.$tab[$i]->icon;
                        } else {
                            $tab[$i]->icon = REAL_ROOT_URL;
                            $tab[$i]->icon .= ($res['obj_plugin'] ? DIR_PLUGINS_OBJ.$res['obj_plugin'].'/' : DIR_PLUGINS_OBJ.strtolower($conf['plugin_default_dir'])).'/'.DIR_ICON;
                        }
                    }
                } else {
                    $tab[$i]->icon = get_icon(file::getExtension($tab[$i]->object));
                }

                $i++;
            }
        }
        return $tab;
    }

    /*  Renvoie les derniers commentaires d'un dossier...
        @param  string  $file   Le dossier en question
        @param  int     $nbr    Nombre de commentaires à retourner (si 0, retourne tout)
     */
    function getCommentDir($file, $nbr = 0) {
        global $conf;
        $tab = array();
        $nbr = ($nbr) ? ' LIMIT 0, '.intval($nbr) : null;
        $sql = "SELECT  obj_file, obj_icon, comment_id, comment_author, comment_mail, comment_url, comment_date, comment_content
                FROM    {$this->_object_table} INNER JOIN {$this->_comment_table}
                ON      obj_id = comment_obj_id
                WHERE   obj_file LIKE   '".obj::format($file)."%'
                ORDER   BY comment_date DESC
                $nbr";
        if (!$var = $this->_bdd->execQuery($sql))
            trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
        for ($i = 0; $res = $this->_bdd->nextTuple($var);) {
            if ($this->getCUserRights4Path($res['obj_file']) & AC_VIEW) {
                $tab[$i] = new tComment;
                $tab[$i]->object = $res['obj_file'];
                $tab[$i]->icon = $res['obj_icon'];
                $tab[$i]->id = $res['comment_id'];
                $tab[$i]->author = $res['comment_author'];
                $tab[$i]->mail = $res['comment_mail'];
                $tab[$i]->url = $res['comment_url'];
                $tab[$i]->date = $res['comment_date'];
                $tab[$i]->content = $res['comment_content'];
                $i++;
            }
        }
        return $tab;
    }

    /*  Ajout d'un fichier anonyme
        @param string   $file   Le chemin + le nom du fichier
     */
    function addAnonFile($file, $description) {
        $id_file = $this->getId(file::dirName($file));
        $name = obj::format(file::baseName($file));
        $sql = "INSERT INTO {$this->_object_table} (obj_file, obj_description, obj_date_last_update, obj_flag, obj_id_ref) VALUES ('/$name', '$description', '".system::time()."', '".FLAG_ANON."', '$id_file');";
        if (!$var = $this->_bdd->execQuery($sql))
            trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
    }

    /*  Retourne un tableau contenant les correspondances pour l'emplacement des fichiers anonymes
        Note: Cette manière de faire est temporaire et sera supprimé dans les prochaines versions...
     */
    function getAnonFile() {
        $tab = array();
        $sql = "SELECT  obj0.obj_file obj0, obj1.obj_file obj1
                FROM    {$this->_object_table} obj0
                INNER   JOIN    {$this->_object_table} obj1 ON obj0.obj_id = obj1.obj_id_ref
                ORDER   BY obj0.obj_file DESC";
        if (!$var = $this->_bdd->execQuery($sql))
            trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
        for ($i = 0; $res = $this->_bdd->nextTuple($var);) {
            $tab[$res['obj1']] = $res['obj0'];
        }
        return $tab;
    }

    /*  Accepte un fichier anonyme
        @param  string  $file   Le fichier
        @param  string  $root   La racine du dépot
     */
    function acceptAnonFile($file, $root) {
        $ret = null;
        // Le fichier existe-t-il en base de données ?
        $sql = "SELECT  obj0.obj_file as destination, obj1.obj_file as name
                FROM    {$this->_object_table} obj0
                INNER   JOIN    {$this->_object_table} obj1 ON obj0.obj_id = obj1.obj_id_ref
                WHERE   obj1.obj_file = '".obj::format($file)."' AND obj1.obj_flag = '".FLAG_ANON."'";
        if (!$var = $this->_bdd->execQuery($sql))
            trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
        if ($tab = $this->_bdd->fetchArray($var)) {
            $destination = $tab['destination']. '/' .substr($tab['name'], 1, strlen($tab['name']));
        } else {
            $destination = $file;
        }

        $source = get_anon_path() . $file;

        $ret = rename($source, $root.$destination);
        if ($ret && $tab) {
            $sql = "UPDATE {$this->_object_table} SET obj_file = '".obj::format($destination)."', obj_flag = '".FLAG_NONE."', obj_id_ref = NULL WHERE obj_file = '".obj::format($file)."'";
            if (!$var = $this->_bdd->execQuery($sql))
                trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
        }

        $ret = file::dirName($destination);
        return $ret;
    }

    /*  Modification d'une description
        @param  string  $content    La description formatée comme il faut
        @param  string  $file       L'objet
     */
    function setDescription($content, $file = null) {
        $file = $file ? $file : $this->_current_obj->file;
        $id = $this->getId($file);
        $sql = "UPDATE {$this->_object_table} SET obj_description = '$content', obj_date_last_update = '".system::time()."' WHERE obj_id = '$id'";
        if (!$var = $this->_bdd->execQuery($sql))
            trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
        return $var;
    }

    /*  Modification du plugin du répertoire courant
        @param  string  $plugin Le plugin voulu
     */
    function setPlugin($plugin) {
        $var = null;

        // On change uniquement si le plugin est différent et existant !
        if (empty($plugin) or $plugin != $this->_current_obj->info->plugin && plugins::isValid($plugin, PLUGIN_TYPE_OBJ)) {
            $id = $this->getId($this->_current_obj->file);
            $sql = "UPDATE {$this->_object_table} SET obj_plugin = '$plugin', obj_date_last_update = '".system::time()."' WHERE obj_id = '{$id}'";
            if (!$var = $this->_bdd->execQuery($sql)) {
                trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
            }
        }
        return $var;
    }

    /*  Modification de l'icone du répertoire courant
        @param  string  $icon   L'image voulu
     */
    function setIcon($icon) {
        global $conf;
        // On change uniquement si l'icone est différent et existant !
        if ($icon != $this->_current_obj->icon) {
            $id = $this->getId($this->_current_obj->file);
            $sql = "UPDATE {$this->_object_table} SET obj_icon = '$icon', obj_date_last_update = '".system::time()."' WHERE obj_id = '{$id}'";
            if (!$var = $this->_bdd->execQuery($sql))
                trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);

            if (!$icon) {
                $icon = DIR_PLUGINS_OBJ.(($this->_current_obj->info->plugin) ? $this->_current_obj->info->plugin.'/' : '').DIR_ICON;
                $icon = (file_exists($icon)) ? $icon : DIR_PLUGINS_OBJ.strtolower($conf['plugin_default_dir']).'/'.DIR_ICON;
            }
        }
        return REAL_ROOT_URL.$icon;
    }

    /*  Ajouter un téléchargement à l'objet courant
     */
    function addDownload() {
        global $cobj;
        $id = $this->getId($cobj->file);
        $sql = "UPDATE {$this->_object_table} SET obj_dcount = obj_dcount + 1 WHERE obj_id = '{$id}'";
        if (!$var = $this->_bdd->execQuery($sql))
            trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
    }

    /*  Retourne l'id d'un objet à partir du chemin, si il n'existe pas, on le créé !
        @param  string  $file   Le chemin + le nom (ex: /test/toto.txt)
        @param  bool    $create Créer l'objet si il n'existe pas ?
     */
    function getId($file, $create = true) {
        $id = 0;
        $file = stripslashes($file);
        if ($file) {
            if ($file == $this->_current_obj->file && $this->_current_obj->info->id) {
                $id = $this->_current_obj->info->id;
            } else if ($create) {
                // Si l'objet demandé n'est pas l'objet courant
                if ($file != $this->_current_obj->file) {
                    $sql = "SELECT obj_id FROM {$this->_object_table} WHERE obj_file = '".obj::format($file)."'";
                    if (!$var = $this->_bdd->execQuery($sql))
                        trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
                    if ($tab = $this->_bdd->fetchArray($var))
                        $id = $tab['obj_id'];
                }

                // Pas d'id ? on le créer !
                if (!$id) {
                    // Si il existe...
                    if (file_exists($this->_folder_root.$file)) {
                        $sql = "INSERT INTO {$this->_object_table} (obj_file) VALUES ('".obj::format($file)."');";
                        if (!$var = $this->_bdd->execQuery($sql))
                            trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
                        $id = $this->_bdd->getInsertID();
                        if ($file == $this->_current_obj->file)
                            $this->_current_obj->info->id = $id;
                    }
                }
            }
        }

        return $id;
    }

    /*  Retourne le chemin (file) d'un objet à partir de son id
        @param  int $id L'id !
     */
    function getFile($id) {
        $file = null;
        if ($id == $this->_current_obj->info->id) {
            $file = $this->_current_obj->info->file;
        } else {
            $sql = "SELECT obj_file FROM {$this->_object_table} WHERE obj_id = '{$id}'";
            if (!$var = $this->_bdd->execQuery($sql))
                trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
            if ($tab = $this->_bdd->fetchArray($var))
                $file = $tab['obj_file'];
        }
        return $file;
    }

    /*  Supprime l'objet
        @param  object  $obj    L'objet concerné !
     */
    function delete($obj) {

        global $conf;
        $ret = false;

        switch ($obj->type) {
            case TYPE_FILE:
                if (unlink($obj->realpath)) {
                    $id = $this->getId($obj->file, false);
                    if ($id) {
                        $sql = "DELETE  obj, comment
                                FROM    {$this->_object_table} obj, {$this->_comment_table} comment
                                WHERE   obj.obj_id = comment.comment_obj_id AND (obj.obj_id = '$id' OR comment.comment_obj_id = '$id')";
                        if (!$ret = $this->_bdd->execQuery($sql))
                            trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
                    }
                    $ret = true;
                }
                break;

            case TYPE_DIR:
                $ret = file::rmDirs($obj->realpath);
                $id = $this->getId($obj->file);
                if ($id) {
                    $sql = "DELETE  obj, cnt
                            FROM    {$this->_object_table} obj, {$this->_comment_table} cnt
                            WHERE   obj.obj_id = cnt.comment_obj_id AND (cnt.comment_obj_id = '$id' OR obj.obj_file LIKE '".obj::format($obj->file)."%')";
                    if (!$ret = $this->_bdd->execQuery($sql))
                        trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
                    $this->delRights();
                }
                break;
        }

        return $ret;
    }

    /*  Copie l'objet vers la destination spécifié
        @param  string  $copy           L'objet
        @param  string  $destination    La destination de l'objet
        @param  string  $base           La base
     */
    function copy($copy, $destination, $base = null) {
        global $conf;
        $ret = null;

        $base = $base ? $base : $this->_folder_root;
        if (is_file($base.$copy)) {
            $ret = copy($base.$copy, $base.$destination);
        } else if (is_dir($base.$copy)) {
            // Création du dossier de destination
            if ($ret = mkdir($base.$destination, $conf['dir_chmod'])) {
                $ret = 1;
                $ret += file::copyDir($base.$copy, $base.$destination, $conf['dir_chmod']);
            }
        }

        return $ret;
    }

    /*  Déplace l'objet vers la destination spécifié
        @param  string  $copy           L'objet
        @param  string  $destination    La destination de l'objet
        @param  string  $base           La base si elle est autre que $this->_folder_root
     */
    function move($copy, $destination, $base = null) {

        global $conf;
        $ret = null;

        $base = $base ? $base : $this->_folder_root;

        if (is_file($base.$copy)) {
//            $file = substr($copy, strlen($base), (strlen($copy) - strlen($base)));
            $ret = rename($base.$copy, $base.$destination);
            if ($ret) {
                $file = $copy;
                $sql = "UPDATE {$this->_object_table} SET obj_file = '".obj::format($destination)."' WHERE obj_file = '".obj::format($file)."'";
                if (!$var = $this->_bdd->execQuery($sql))
                    trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
                $ret = 1;
            }
        } else if (is_dir($base.$copy)) {
            if ($ret = mkdir($base.$destination, $conf['dir_chmod'])) {
                $ret = 1;
                $ret += file::copyDir($base.$copy, $base.$destination, $conf['dir_chmod']);
                if ($ret) {
                    file::rmDirs($base.$copy);
                    $sql = "UPDATE {$this->_object_table} SET obj_file = REPLACE(obj_file, '".obj::format($copy)."', '".obj::format($destination)."') WHERE obj_file LIKE '".obj::format($copy)."%'";
                    if (!$var = $this->_bdd->execQuery($sql))
                        trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
                }

            }
        }

        return $ret;
    }

    /*  Renomme un objet
        @param  string  $name       L'objet
        @param  string  $newname    Son nouveau nom
     */
    function rename($name, $newname) {
        $ret = null;
        $newname = (file::dirName($name) == '/' ? null : file::dirName($name)).'/'.$newname;
        if (is_file($this->_folder_root.$name)) {
            $ret = rename($this->_folder_root.$name, $this->_folder_root.$newname);
            if ($ret) {
                $sql = "UPDATE {$this->_object_table} SET obj_file = '".obj::format($newname)."' WHERE obj_file = '".obj::format($name)."'";
                if (!$var = $this->_bdd->execQuery($sql))
                    trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
            }
        } else if ($this->_folder_root.$name) {
            $ret = rename($this->_folder_root.$name, $this->_folder_root.$newname);
            if ($ret) {
                $newname .= '/';
                $sql = "UPDATE {$this->_object_table} SET obj_file = REPLACE(obj_file, '".obj::format($name)."', '".obj::format($newname)."') WHERE obj_file LIKE '".obj::format($name)."%'";
                if (!$var = $this->_bdd->execQuery($sql))
                    trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
            }
        }
        return $ret;
    }

    /*  Effectue une synchronisation de la base de données
     */
    function syncBdd() {

        $tab = array();

        $qry = null;
        $cmpt = 0;

        $tab_qry = array(   'obj'   =>  "DELETE FROM {$this->_object_table} WHERE obj_id IN (",
                            'cmt'   =>  "DELETE FROM {$this->_comment_table} WHERE comment_id IN (",
                            'acl'   =>  "DELETE FROM {$this->_acontrol_table} WHERE ac_obj_id IN (",
                        );

        $sql = "SELECT obj_id as id, obj_file as object, obj_flag as flag FROM {$this->_object_table}";
        if (!$var = $this->_bdd->execQuery($sql))
            trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
        for ($y = 0; $res = $this->_bdd->nextTuple($var); $y++) {

            if ($res['flag'] == FLAG_ANON) {
                $file = get_anon_path() . '/' . file::baseName($res['object']);
            } else {
                $file = $this->_folder_root.$res['object'];
            }

            if (!file_exists($file)) {

                $virg = ($cmpt ? ', ' : null);

                $tab_qry['obj'] .= $virg.$res['id'];
                $tab_qry['cmt'] .= $virg.$res['id'];
                $tab_qry['acl'] .= $virg.$res['id'];

                $cmpt++;
            }
        }

        if ($cmpt) {
            $tab_qry['obj'] .= ')';
            $tab_qry['cmt'] .= ')';
            $tab_qry['acl'] .= ')';
            foreach ($tab_qry as $key => $value) {
                if (!$ret = $this->_bdd->execQuery($value))
                    trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
            }
        }

        return $cmpt;
    }

    /*  Scan un dossier de manière récursive
     */
    function scanDir($_folder_root, $hidden = true, $_folder = null) {
        static $tab_dir, $tab = null;
        static $cmpt = 0;
        $cmpt++;

        $_folder .= '/';

        $hdl = dir($_folder_root.$_folder);
        if ($hdl) {
            while (false !== ($_occ = $hdl->read())) {
                if ($_occ != '.' && $_occ != '..' && is_dir($_folder_root.$_folder.$_occ)) {

                    $rights = $this->getCUserRights4Path($_folder.$_occ.'/');
                    if (!($rights & AC_VIEW)) {
                        continue;
                    }

                    if ($_occ{0} != '.' || $hidden) {
                        $tab_dir[] = $_folder.$_occ.'/';
                        $this->scanDir($_folder_root, $hidden, $_folder.$_occ);
                    }
                }
            }
            $hdl->close();
        }

        if ($cmpt == 1) {
            $tab = $tab_dir;
            $tab_dir = null;
        }
        $cmpt--;

        return $tab;
    }

    /*  Renvoie le contenu d'un répertoire trié comme on veut
        @access  private
     */
    function _getDirContent($folder, $base) {

        global $conf;
        $tab = array();

        $hdl = dir($base.$folder);
        if ($hdl) {
            while (false !== ($occ = $hdl->read())) {

                if ($folder == '/' && $occ{0} == '.' && isset($occ{1}) && $occ{1} == '.')
                    continue;

                if ($occ == '.' || $occ == '..')  ## Si on veut afficher les .. dans les répertoires, ça se passe ici !
                    continue;

                // Si on a un fichier caché...
                if ($occ{0} == '.' && isset($occ{1}) && $occ{1} != '.' && !$this->view_hidden_file) {
                    continue;
                }

                $tab[] = $folder.$occ.(is_dir($this->_folder_root.$folder.$occ) ? '/' : null);
            }
        }

        return $tab;
    }

    /*  Formate le chemin, le nom d'un objet pour passer dans une requête sql
        @access static
     */
    function format($file) {
        return addslashes($file);
    }

    /*  Tri les répertoires en premier
        @access private
     */
    function _sort_folder_first($a, $b) {
        $ret = 0;

        $bas_a = file::baseName($a->name);
        $bas_b = file::baseName($b->name);

        if ($bas_a == '..' || $bas_a == '.' )
            $ret = -1;

        if ($bas_b == '..' || $bas_b == '.')
            $ret = 1;

        if (!$ret && $this->_tri & SORT_FOLDER_FIRST) {
            if (is_dir($a->realpath) && is_dir($b->realpath)) {
                $ret = strcasecmp($a->file, $b->file);
                if ($this->_tri & SORT_NAME_ALPHA_R)
                    $ret = ($ret == 1) ? -1 : 1;
            } else {
                if (is_dir($a->realpath))
                    $ret = -1;
                if (is_dir($b->realpath))
                    $ret = 1;
            }
        }
        return $ret;
    }

    /*  Tri sur les noms
        @access private
     */
    function _sort_name($a, $b) {
        $ret = $this->_sort_folder_first($a, $b);
        if (!$ret) {
            if ($this->_tri & SORT_NAME_ALPHA_R) {
                $c = $a;
                $a = $b;
                $b = $c;
            }
            return strcasecmp($a->file, $b->file);
        }
        return $ret;
    }

    /*  Tri sur les extensions
        @access private
     */
    function _sort_ext($a, $b) {
        $ret = $this->_sort_folder_first($a, $b);
        if (!$ret) {
            if ($this->_tri & SORT_EXT_ALPHA_R) {
                $c = $a;
                $a = $b;
                $b = $c;
            }

            $a->extension = strtolower($a->extension);
            $b->extension = strtolower($b->extension);
            if (!strcmp($a->extension, $b->extension))
                return strcmp($a->name, $b->name);
            else
                return strcmp($a->extension, $b->extension);
        }
        return $ret;
    }

    /*  Tri sur les catégories
        @access private
     */
    function _sort_cat($a, $b) {
        $ret = $this->_sort_folder_first($a, $b);
        if (!$ret) {
            if ($this->_tri & SORT_CAT_ALPHA_R) {
                $c = $a;
                $a = $b;
                $b = $c;
            }

            if (!strcmp($a->cat, $b->cat))
                return strcmp($a->name, $b->name);
            else
                return strcmp($a->cat, $b->cat);
        }
        return $ret;
    }

    /*  Tri sur les tailles
        @access private
     */
    function _sort_size($a, $b) {
        $ret = $this->_sort_folder_first($a, $b);
        if (!$ret) {
            if ($this->_tri & SORT_SIZE_R) {
                $c = $a;
                $a = $b;
                $b = $c;
            }

            if ($a->size == $b->size)
                $ret = strcmp($a->name, $b->name);
            else
                $ret = ($a->size < $b->size) ? -1 : 1;
        }
        return $ret;
    }

    /*  Tri sur les dates
        @access private
     */
    function _sort_date($a, $b) {
        $ret = $this->_sort_folder_first($a, $b);
        if (!$ret) {
            if ($this->_tri & SORT_DATE_R) {
                $c = $a;
                $a = $b;
                $b = $c;
            }

            $d_a = ($a->info->date_last_update) ? $a->info->date_last_update : filectime($a->realpath);
            $d_b = ($b->info->date_last_update) ? $b->info->date_last_update : filectime($b->realpath);

            $ret = strcmp($d_b, $d_a);
        }
        return $ret;
    }

    /*  Filtrage (voir filter() pour plus d'infos...)
        @access private
     */
    function _filter($obj, $filter) {
        $ok = true;
        switch ($filter[0]) {
            case '=':
                if ($obj->$filter[1] == $filter[2]) {
                    $ok = $ok ? true : false;
                } else
                    $ok = false;
                break;
            case '!':
                if ($obj->$filter[1] != $filter[2]) {
                    $ok = $ok ? true : false;
                } else
                    $ok = false;
                break;
        }
        return $ok;
    }
}

?>
