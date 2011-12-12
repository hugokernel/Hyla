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

require HYLA_ROOT_PATH.'src/core/acl.class.php';
require HYLA_ROOT_PATH.'src/core/datasrc.class.php';
require HYLA_ROOT_PATH.'src/core/archive.class.php';

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
    var $path;          // Le chemin depuis la racine
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

    var $_bdd;              // L'objet base de données

    var $_folder_root;      // La racines

    var $_object_table;     // La table des objets
    var $_comment_table;    // La table des commentaires

    var $_current_obj;      // L'objet courant...

    var $site_id;          // Site id

    var $_sort;             // Le tri à appliquer

    var $view_hidden_file;  // Voir les fichiers cachés ?

    var $_nbr_object;       // Le nombre d'éléments total dans le répertoire courant (utile pour la pagination)

    var $_cache_rights;     // Tableau contenant des infos de droits en cache
    var $_all_rights;       // Tableau contenant tous les droits
    var $_error_rights;     // Tableau contenant des erreurs dans les droits

    var $conf;

    // Public
    var $datasource;              // Data source array

    /*  Le constructeur
     */
    function obj($site_id) {//$folder_root) {

        $this->conf = conf::getInstance();

        $this->bdd = plugins::get(PLUGIN_TYPE_DB);
/*
if ($folder_root) {
    echo '<h1>ATTENTION, Parameter forbidden in obj !!</h1>';
}
*/
//        $this->setRoot($folder_root);

        $this->site_id = intval($site_id);

        $this->_object_table = TABLE_OBJECT;
        $this->_acontrol_table = TABLE_ACONTROL;
        $this->_comment_table = TABLE_COMMENT;
        $this->_users_table = TABLE_USERS;
        $this->_grp_usr_table = TABLE_GRP_USR;
        $this->_site_table = TABLE_SITE;

        $this->_nbr_object = 0;

        $this->_current_obj = array();

        $this->_sort = $this->conf->get('sort_config');

        $this->_cache_rights = array();
        $this->_all_rights = array();
        $this->_error_rights = array();

        $this->acl();

        $this->view_hidden_file = $this->conf->get('view_hidden_file');

        // Register data source
        $this->datasource = new datasrc();
    }

    function load() {
        $sql = "SELECT  site_description AS description, site_shared_dir AS shared_dir, site_url AS url
                FROM    {$this->_site_table} 
                WHERE   site_id = '{$this->site_id}'";
        if (!($var = $this->bdd->execQuery($sql))) {
            system::log(L_FATAL, $this->bdd->getErrorMsg(), 'obj');
        }

        $res = $this->bdd->nextTuple($var);
        $this->setRoot($res['shared_dir']);

        return $res;
    }

    /**
     *  Get obj (Singleton...)
     */
    function getInstance($site_id = null) {
        static $obj = null;
        if ($obj == null) {
            $obj = new obj($site_id);
        }
        return $obj;
    }

    /**
     *  Register a new data source
     *  @param  string  $name       Data source name
     *  @param  string  $callback   Callback function
     *  @param  mixed   $param      Parameter
     */
    /*
    function registerDataSource($name, $callback, $param = null) {
        $id = $this->datasource->register($name, $callback, $param);
        $this->datasource->setCurrent($id);
        $this->setCurrentObj('/', $id);
        return $id;
    }
    */

    /**
     *  Get current obj according to data source
     */
    function getCurrentObj() {
        return ($this->_current_obj) ? $this->_current_obj[$this->datasource->getCurrent()] : null;
    }

    /**
     *  Set the current obj to current data source
     *  @param  string  $file   File
     */
    function setCurrentObj($file, $id = -1) {
        global $cobj;
        if ($obj = $this->getInfo($file)) {
            $id = ($id >= 0) ? $id : $this->datasource->getCurrent();
            $this->_current_obj[$id] = $obj;
            $cobj = &$this->_current_obj[$id];
        }
        return $this->_current_obj[$id];
    }

    /**
     *  Get the root dir
     */
    function getRoot() {
        return $this->_folder_root;
    }

    /**
     *  Set root dir
     *  @param  string  $folder_root    Folder root
     */
    function setRoot($folder_root) {
        // Delete final /
        if ($folder_root{strlen($folder_root) - 1} == '/') {
            $folder_root = substr($folder_root, 0, strlen($folder_root) - 1);
        }
        $this->_folder_root = $folder_root;
        return $this->_folder_root;
    }

    /*  Renvoie un tableau contenant les informations sur un objet (fichier ou dossier)
        @param  string  $name   Le nom de l'objet
        @param  bool    $data   Récupérer les infos en base de données (true)
        @param  bool    $pnext  Récupère ou non les objet précédent et suivant
     */
    function getInfo($name, $data = true, $pnext = true) {

        $obj = null;
        $target = null;

        // Search !
        if (strstr($name, '!') !== false) {
            @list($name, $target) = @explode('!', $name, 2);
        }

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
                            WHERE   obj_file = '".obj::format($obj->file)."' AND obj_site_id = '{$this->site_id}'";
                    if (!($var = $this->bdd->execQuery($sql))) {
                        system::log(L_FATAL, $this->bdd->getErrorMsg(), 'obj');
                    }

                    for ($i = 0; $res = $this->bdd->nextTuple($var); $i++) {
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
                            WHERE   obj_file = '".obj::format($obj->file)."' AND obj_site_id = '{$this->site_id}'";
                    if (!($var = $this->bdd->execQuery($sql))) {
                        system::log(L_FATAL, $this->bdd->getErrorMsg(), 'obj');
                    }

                    for ($i = 0; $res = $this->bdd->nextTuple($var); $i++) {
                        if (!$i) {
                            $obj->info->id = $res['obj_id'];
                            $obj->info->date_last_update = $res['obj_date_last_update'];
                            $obj->info->description = $res['obj_description'];
                            $obj->info->dcount = (int)$res['obj_dcount'];
                            $obj->info->plugin = strtolower($res['obj_plugin'] ? $res['obj_plugin'] : null);    // -> On met null, c'est mieux pour le par défaut !

                            $obj->icon = ($res['obj_icon']) ? $res['obj_icon'] : null;
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
                    $default_dir = strtolower($this->conf->get('plugin_default_dir'));
                    $obj->icon = (file_exists($icon) ? $icon : DIR_PLUGINS_OBJ.$default_dir.'/'.DIR_ICON);
                    $obj->icon = HYLA_ROOT_URL.$obj->icon;
                }
            }
        }

/*
        if ($data && ($pnext || $obj->type == TYPE_DIR)) {
//            $this->_current_obj = $obj;
            // No setCurrentObj here !!
            $this->_current_obj[$this->datasource->getCurrent()] = &$obj;
//            $this->setCurrentObj($obj);
        }
*/
        return $obj;
    }

    /*  Renvoie un tableau contenant les nom des fichiers précédent et suivant en tenant compte du tri
        @param  string  $obj        L'objet
        @param  string  $archive    Spécifie si l'objet est dans une archive
     */
    function getPrevNext($obj) {

        $ret = array('prev' => null, 'next' => null);

        if ($obj->type == TYPE_ARCHIVED) {
            $ret = archive::getPrevNext($obj->realpath, $obj->target);
        } else {
            $this->_sort = (isset($_SESSION['sess_sort'])) ? $_SESSION['sess_sort'] : $this->conf->get('sort_config');  // ToDo : Remplacez $_SESSION[... par une variable de classe
            $tab = $this->getDirContent($obj->path, $this->_sort, 0, -1, -1, false);
            $size = sizeof($tab);
            for ($i = 0, $prev = null; $i < $size; $i++) {
                if ($tab[$i]->file == $obj->file) {
                    $ret['prev'] = (isset($prev->file) ? $prev->file : null);
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
        @param  int     $sort       Le tri à appliquer
        @param  int     $start      A partir d'oû on commence à compter
        @param  int     $nbr        Le nombre d'occurence à afficher (si 0, config par défaut, si -1, affiche tout)
        @param  array   $tab        Tableau de données
        @param  array   $filter     Filtrage dans la structure tFile
     */
    function getDirContent($name = '/', $sort = null, $start = 0, $nbr = 0, $tab = -1, $filter = null) {

        $bdd = plugins::get(PLUGIN_TYPE_DB);

        $this->_sort = ($sort) ? $sort : SORT_DEFAULT;
        $nbr = ($nbr == 0) ? $this->conf->get('nbr_obj') : $nbr;

        $arr = array();

        //  Récupère les fichiers et répertoire du système de fichiers - Todo: C'est pas beau ce -1 !!
        if ($tab == -1) {
            $tab = $this->datasource->get(file::format($name));
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
                WHERE   obj_file = '".obj::format($name)."' $s AND obj_site_id = '{$this->site_id}'
                GROUP   BY obj_file
                ORDER   BY obj_file ASC";
        if (!$var = $bdd->execQuery($sql)) {
            system::log(L_FATAL, $this->bdd->getErrorMsg(), 'obj');
        }

        for ($i = 0; $res = $bdd->nextTuple($var); $i++) {
            $a[$res['obj_file']]['id'] = $res['obj_id'];
            $a[$res['obj_file']]['date_last_update'] = $res['obj_date_last_update'];
            $a[$res['obj_file']]['description'] = $res['obj_description'];
            $a[$res['obj_file']]['plugin'] = strtolower($res['obj_plugin']);
            $a[$res['obj_file']]['icon'] = $res['obj_icon'];
            $a[$res['obj_file']]['nbr_comment'] = $res['nbr_comment'];
        }

        // On mixe les données provenant du système de fichiers et de la base de données
        if ($tab) {
            $i = 0;
            foreach ($tab as $occ) {
                $tmp = $this->getInfo($occ, false, false);
                if (!$tmp) {
                    continue;
                }

                // Exécution des filtres
                if ($filter && !obj::filter($tmp, $filter)) {
                    continue;
                }

                $tab_out[$i] = $tmp;
                if (isset($a[$tab_out[$i]->file]['id'])) {
                    $tab_out[$i]->info->id = $a[$tab_out[$i]->file]['id'];
                    $tab_out[$i]->info->date_last_update = $a[$tab_out[$i]->file]['date_last_update'];
                    $tab_out[$i]->info->description = $a[$tab_out[$i]->file]['description'];
                    $tab_out[$i]->info->plugin = $a[$tab_out[$i]->file]['plugin'];

                    if ($tab_out[$i]->type == TYPE_DIR) {
                        if ($a[$tab_out[$i]->file]['icon'])
                            $tab_out[$i]->icon = $a[$tab_out[$i]->file]['icon'];
                        else {
                            $icon = DIR_PLUGINS_OBJ.$tab_out[$i]->info->plugin.'/'.DIR_ICON;
                            $default_dir = strtolower($this->conf->get('plugin_default_dir'));
                            $tab_out[$i]->icon = (($tab_out[$i]->info->plugin) ? (file_exists($icon) ? $icon : get_icon('.')) : HYLA_ROOT_URL.DIR_PLUGINS_OBJ.$default_dir.'/'.DIR_ICON);
                        }
                    }
                    $tab_out[$i]->info->nbr_comment = isset($a[$tab_out[$i]->file]['nbr_comment']) ? $a[$tab_out[$i]->file]['nbr_comment'] : null;
                } else {
                    if ($tab_out[$i]->type == TYPE_DIR) {
                        $default_dir = strtolower($this->conf->get('plugin_default_dir'));
                        $tab_out[$i]->icon = HYLA_ROOT_URL.DIR_PLUGINS_OBJ.($tab_out[$i]->info->plugin ? $tab_out[$i]->info->plugin.'/' : '').$default_dir.'/'.DIR_ICON;
                    }
                }
                $i++;
            }
            $tab = null;
        }

        if (isset($tab_out)) {

            // Et maintenant, on tri les données
            if ($sort & SORT_NAME_ALPHA || $sort & SORT_NAME_ALPHA_R) {
                usort($tab_out, array($this, '_sort_name'));
            } else if ($sort & SORT_EXT_ALPHA || $sort & SORT_EXT_ALPHA_R) {
                usort($tab_out, array($this, '_sort_ext'));
            } else if ($sort & SORT_CAT_ALPHA || $sort & SORT_CAT_ALPHA_R) {
                usort($tab_out, array($this, '_sort_cat'));
            } else if ($sort & SORT_SIZE || $sort & SORT_SIZE_R) {
                usort($tab_out, array($this, '_sort_size'));
            } else if ($sort & SORT_DATE || $sort & SORT_DATE_R) {
                usort($tab_out, array($this, '_sort_date'));
            }

            // Et on affiche uniquement certain
            if ($nbr != -1) {
                $tab = array();
                $this->_nbr_object = count($tab_out);

                if ($start) {
                    $start = ($start >= $this->_nbr_object) ? (($this->_nbr_object - $nbr < 0) ? 0 : $this->_nbr_object - 1) : $start;
                }

                for ($i = 0, $c = 0, $cmpt = 0; $i < $this->_nbr_object; $i++) {            // ToDo : Nettoyer, Optimiser le code ici
                    if ($i >= $start) {
                        $tab[$c++] = &$tab_out[$i];
                        $cmpt++;
                    }

                    if ($nbr && $cmpt >= $nbr) {
                        break;
                    }
                }
            } else {
                $tab = &$tab_out;
            }
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
                if (!$ok) {
                    break;
                }
            }
        } else {
            $ok = obj::_filter($obj, $filter);
        }
        return $ok;
    }

    /*  Renvoie le nombre d'objet dans le répertoire courant (faire après getDirContent)
     */
    function getNbrObject() {
        return $this->_nbr_object;
    }

    /*  Renvoie les derniers commentaires d'un dossier...
        @param  string  $file   Le dossier en question
        @param  int     $nbr    Nombre de commentaires à retourner (si 0, retourne tout)
     */
    /*
    function getCommentDir($file, $nbr = 0) {
        $tab = array();
        $nbr = ($nbr) ? ' LIMIT 0, '.intval($nbr) : null;
        $sql = "SELECT  obj_file, obj_icon, comment_id, comment_author, comment_mail, comment_url, comment_date, comment_content
                FROM    {$this->_object_table} INNER JOIN {$this->_comment_table}
                ON      obj_id = comment_obj_id
                WHERE   obj_file LIKE   '".obj::format($file)."%'
                ORDER   BY comment_date DESC
                $nbr";
        if (!$var = $this->bdd->execQuery($sql)) {
            system::log(L_FATAL, $this->bdd->getErrorMsg(), 'obj');
        }

        for ($i = 0; $res = $this->bdd->nextTuple($var);) {
            if (!($this->getCUserRights4Path($res['obj_file']) & AC_VIEW)) {
                continue;
            }

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
        return $tab;
    }
    */

    /*  Ajout d'un fichier anonyme
        @param string   $file   Le chemin + le nom du fichier
     */
    /*
    function addAnonFile($file, $description) {
        $id_file = $this->getId(file::dirName($file));
        $name = obj::format(file::baseName($file));
        $sql = "INSERT INTO {$this->_object_table} (obj_file, obj_description, obj_date_last_update, obj_flag, obj_id_ref) VALUES ('/$name', '$description', '".system::time()."', '".FLAG_ANON."', '$id_file');";
        return $this->bdd->execQuery($sql);
    }
    */

    /*  Retourne un tableau contenant les correspondances pour l'emplacement des fichiers anonymes
        Note: Cette manière de faire est temporaire et sera supprimé dans les prochaines versions...
     */
    /*
    function getAnonFile() {
        $tab = array();
        $sql = "SELECT  obj0.obj_file obj0, obj1.obj_file obj1
                FROM    {$this->_object_table} obj0
                INNER   JOIN    {$this->_object_table} obj1 ON obj0.obj_id = obj1.obj_id_ref
                ORDER   BY obj0.obj_file DESC";
        if ($var = $this->bdd->execQuery($sql)) {
            for ($i = 0; $res = $this->bdd->nextTuple($var);) {
                $tab[$res['obj1']] = $res['obj0'];
            }
        }
        return $tab;
    }
    */

    /*  Accepte un fichier anonyme
        @param  string  $file   Le fichier
        @param  string  $root   La racine du dépot
     */
    /*
    function acceptAnonFile($file, $root) {
        $ret = null;
        // Le fichier existe-t-il en base de données ?
        $sql = "SELECT  obj0.obj_file as destination, obj1.obj_file as name
                FROM    {$this->_object_table} obj0
                INNER   JOIN    {$this->_object_table} obj1 ON obj0.obj_id = obj1.obj_id_ref
                WHERE   obj1.obj_file = '".obj::format($file)."' AND obj1.obj_flag = '".FLAG_ANON."'";
        if (!$var = $this->bdd->execQuery($sql)) {
            system::log(L_FATAL, $this->bdd->getErrorMsg(), 'obj');
        }

        if ($tab = $this->bdd->fetchArray($var)) {
            $destination = $tab['destination'].($tab['destination']{0} ? null : '/').substr($tab['name'], 1, strlen($tab['name']));
        } else {
            $destination = $file;
        }

        $source = HYLA_RUN_PATH.DIR_ANON.$file;

        $ret = rename($source, $root.$destination);
        if ($ret && $tab) {
            $sql = "UPDATE {$this->_object_table} SET obj_file = '".obj::format($destination)."', obj_flag = '".FLAG_NONE."', obj_id_ref = NULL WHERE obj_file = '".obj::format($file)."'";
            if (!$var = $this->bdd->execQuery($sql)) {
                system::log(L_FATAL, $this->bdd->getErrorMsg(), 'obj');
            }
        }

        $ret = file::dirName($destination);
        return $ret;
    }
    */

    /*  Modification du plugin du répertoire courant
        @param  string  $plugin Le plugin voulu
     */
/*
    function setPlugin($plugin) {
        $var = null;

        // On change uniquement si le plugin est différent et existant !
        if (empty($plugin) or $plugin != $this->_current_obj->info->plugin && plugins::isValid($plugin, PLUGIN_TYPE_OBJ)) {
            $id = $this->getId($this->_current_obj->file);
            $sql = "UPDATE {$this->_object_table} SET obj_plugin = '$plugin', obj_date_last_update = '".system::time()."' WHERE obj_id = '{$id}'";
            $var = $this->bdd->execQuery($sql);
        }
        return $var;
    }

    /*  Ajouter un téléchargement à l'objet courant
     */
    function addDownload() {
        global $cobj;
        $id = $this->getId($cobj->file);
        $sql = "UPDATE {$this->_object_table} SET obj_dcount = obj_dcount + 1 WHERE obj_id = '{$id}'";
        return $this->bdd->execQuery($sql);
    }

    /*  Retourne l'id d'un objet à partir du chemin, si il n'existe pas, on le créé !
        @param  string  $file   Le chemin + le nom (ex: /test/toto.txt)
        @param  bool    $create Créer l'objet si il n'existe pas ?
     */
    function getId($file, $create = true) {
        $id = 0;
        $file = stripslashes($file);
        if ($file) {
            $current_obj = $this->getCurrentObj();
            if ($current_obj && $file == $current_obj->file && $current_obj->info->id) {
                $id = $current_obj->info->id;
            } else if ($create) {
                // Si l'objet demandé n'est pas l'objet courant
                if (!$current_obj || $file != $current_obj->file) {
                    $sql = "SELECT obj_id FROM {$this->_object_table} WHERE obj_file = '".obj::format($file)."' AND obj_site_id = '{$this->site_id}'";
                    if (!$var = $this->bdd->execQuery($sql)) {
                        system::log(L_FATAL, $this->bdd->getErrorMsg(), 'obj');
                    }

                    if ($tab = $this->bdd->fetchArray($var))
                        $id = $tab['obj_id'];
                }

                // Pas d'id ? on le créer !
                if (!$id) {
                    // Si il existe...
                    if (file_exists($this->_folder_root.$file)) {
                        $sql = "INSERT INTO {$this->_object_table} (obj_file, obj_site_id) VALUES ('".obj::format($file)."', '{$this->site_id}');";
                        if (!$var = $this->bdd->execQuery($sql)) {
                            system::log(L_FATAL, $this->bdd->getErrorMsg(), 'obj');
                        }

                        $id = $this->bdd->getInsertID();
                        if ($current_obj && $file == $current_obj->file)
                            $current_obj->info->id = $id;
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

        $current_obj = $this->getCurrentObj();
        if ($id == $current_obj->info->id) {
            $file = $current_obj->info->file;
        } else {
            $sql = "SELECT obj_file FROM {$this->_object_table} WHERE obj_id = '{$id}'";
            if (!$var = $this->bdd->execQuery($sql)) {
                system::log(L_FATAL, $this->bdd->getErrorMsg(), 'obj');
            }

            if ($tab = $this->bdd->fetchArray($var)) {
                $file = $tab['obj_file'];
            }
        }
        return $file;
    }

    /*  Effectue une synchronisation de la base de données
     */
    /*
    function syncBdd() {

        $tab = array();

        $qry = null;
        $cmpt = 0;

        $tab_qry = array(   'obj'   =>  "DELETE FROM {$this->_object_table} WHERE obj_id IN (",
                            'cmt'   =>  "DELETE FROM {$this->_comment_table} WHERE comment_id IN (",
                            'acl'   =>  "DELETE FROM {$this->_acontrol_table} WHERE ac_obj_id IN (",
                        );

        $sql = "SELECT obj_id as id, obj_file as object, obj_flag as flag FROM {$this->_object_table}";
        if (!$var = $this->bdd->execQuery($sql)) {
            system::log(L_FATAL, $this->bdd->getErrorMsg(), 'obj');
        }

        for ($y = 0; $res = $this->bdd->nextTuple($var); $y++) {

            if ($res['flag'] == FLAG_ANON) {
                $file = HYLA_RUN_PATH.DIR_ANON.'/'.file::baseName($res['object']);
            } else
                $file = $this->_folder_root.$res['object'];

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
                if (!$ret = $this->bdd->execQuery($value)) {
                    system::log(L_FATAL, $this->bdd->getErrorMsg(), 'obj');
                }
            }
        }

        return $cmpt;
    }
    */

    /**
     *  Wrapper data source
     */
    function wrapper($dir, $only_dir = false, $recursive = false, $view_hidden = false) {
        return $this->scanDir($this->getRoot(), $dir);  //, $only_dir, $recursive, $view_hidden);
    }

    /**
     *  Scan dir content
     *  @param  string  $root_dir       Root dir
     *  @param  string  $dir            Dir in root dir
     *  @param  bool    $only_dir       Get dir only
     *  @param  bool    $recursive      Recursive
     *  @param  bool    $view_hidden    Get hidden dir and file
     */
    function scanDir($root_dir, $dir = null, $only_dir = false, $recursive = false, $view_hidden = false) {

        static $tab, $tab_dir = null;
        static $cmpt = 0;

        $cmpt++;
        $is_dir = false;

        $hdl = dir($root_dir.$dir);
        if (!$hdl) {
            return null;
        }

        while (false !== ($item = $hdl->read())) {

            // System dir ?
            if ($item == '.' || $item == '..') {
                continue;
            }

            // Only dir ?
            $is_dir = is_dir($root_dir.$dir.'/'.$item);
            if ($only_dir && !$is_dir) {
                continue;
            }

            // Hidden item ?
            if ($item{0} == '.' && !$view_hidden) {
                continue;
            }

            // Format
            $obj = file::format($dir.'/'.$item.($is_dir ? '/' : null));

            // Test rights
            $rights = $this->getCUserRights4Path($obj);
            if (!($rights & AC_VIEW)) {
                continue;
            }

            // Save
            $tab_dir[] = $obj;

            if ($is_dir && $recursive) {
                $this->scanDir($root_dir, $dir.'/'.$item, $only_dir, true, $view_hidden);
            }
        }

        $hdl->close();

        // Reset static tab when scan is complete !
        if ($cmpt == 1) {
            $tab = $tab_dir;
            $tab_dir = null;
        }

        $cmpt--;

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

        if (!$ret && $this->_sort & SORT_FOLDER_FIRST) {
            if (is_dir($a->realpath) && is_dir($b->realpath)) {
                $ret = strcasecmp($a->file, $b->file);
                if ($this->_sort & SORT_NAME_ALPHA_R)
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
            if ($this->_sort & SORT_NAME_ALPHA_R) {
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
            if ($this->_sort & SORT_EXT_ALPHA_R) {
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
            if ($this->_sort & SORT_CAT_ALPHA_R) {
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
            if ($this->_sort & SORT_SIZE_R) {
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
            if ($this->_sort & SORT_DATE_R) {
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
        $ok = false;
        switch ($filter[0]) {
            case '=':
                if ($obj->$filter[1] == $filter[2]) {
                    $ok = true;
                }
                break;
            case '!':
                if ($obj->$filter[1] != $filter[2]) {
                    $ok = true;
                }
                break;
            default:
                $ok = true;
                break;
        }
        return $ok;
    }
}

?>
