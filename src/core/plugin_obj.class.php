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

class tPluginManifestObj extends tPluginManifest {
    var $priority = 0;
    var $target = null;
    var $extension = array();
}

class plugin_obj extends plugin
{
    var $tpl;
    var $obj;
    var $url;
    var $cobj;

    var $real_file;        // Le chemin d'accès à l'objet courant, très utile car, il va chercher dans le cache (ex: fichier contenus dans zip, tar...)

    var $_url_2_plugin;     // L'url pour accéder au dossier du plugin courant

    function plugin_obj() {
        parent::plugin();

        $this->tpl = new Template(null);
        $this->tpl->set_root(HYLA_ROOT_PATH.$this->plugin_dir.$this->plugin_name);

        $this->obj = obj::getInstance();
        $this->cobj = $this->obj->getCurrentObj();

        $this->url = plugins::get(PLUGIN_TYPE_URL);

        $this->real_file = get_real_directory();
        
        $this->_url_2_plugin = system::getHost().HYLA_ROOT_URL.$this->plugin_dir.$this->plugin_name.'/';
    }

    function getContent($sort, $start) {
        return $this->obj->getDirContent($this->cobj->file, $sort, $start, 0, -1);//, array('=', 'path', $url->current->obj));
    }

    /*  Renvoie un tableau contenant les plugins disponibles pour un répertoire
        @access static
        /!\ Factoriser le code ci dessous avec le reste du code, c'est pas très propre... /!\
     */
    function getDirPlugins() {
        return plugin_obj::_getPlugins(true);
    }

    /*  Renvoie un tableau contenant les plugins disponibles pour un fichier
        @access static
     */
    function getFilePlugins() {
        return plugin_obj::_getPlugins(false);
    }

    function _getPlugins($type = true) {

        $tab = array();

        $hdl = dir(DIR_PLUGINS_OBJ);
        if (!$hdl) {
            return;
        }

        while (false !== ($item = $hdl->read())) {

            // Si on a un fichier caché...
            if ($item{0} == '.')
                continue;

            // Si ce n'est pas un répertoire
            if (!is_dir(DIR_PLUGINS_OBJ.'/'.$item))
                continue;

            $manifest = plugin_obj::getManifest($item);
            if ($manifest) {
                $tab[] = $manifest;
            }
        }

        return $tab;
    }

    /**
     *  Get manifest
     *  @param  string  $name   Plugin name
     */
    function getManifest($name) {

        $ret = false;

        $manifest = plugin::getManifestFile(PLUGIN_TYPE_OBJ, $name);
        if (!$manifest) {
            return $ret;
        }

        $ret = new tPluginManifestObj;
        $ret->dir = HYLA_ROOT_PATH.DIR_PLUGINS_OBJ.$name.'/';
        $ret->name = $manifest[M_NAME];
        $ret->description = $manifest[M_DESCRIPTION];
        $ret->author = $manifest[M_AUTHOR];
        $ret->version = $manifest[M_VERSION];

        if (isset($manifest[M_CONF])) {
            $ret->conf = $manifest[M_CONF];
        }

        if (isset($manifest[M_MINVERSION])) {
            $ret->minversion = $manifest[M_MINVERSION];
        }

        if (isset($manifest[M_PRIORITY])) {
            $ret->priority = $manifest[M_PRIORITY];
        }

        if (isset($manifest[M_TARGET])) {
            $ret->target = strtolower($manifest[M_TARGET]);
        }

        if (isset($manifest[M_EXTENSION])) {
            $ret->extension = strtolower($manifest[M_EXTENSION]);
            $ret->extension = explode(',', $ret->extension);
            $ret->extension = array_map('strtolower', $ret->extension);
            $ret->extension = array_map('trim', $ret->extension);
        }

        return $ret;
    }

    /*  Get the plugin for the object
        @param  object  $cobj   Current object
     */
    function searchFilePlugin($cobj) {
        $ret = null;
        $hdl = dir(DIR_PLUGINS_OBJ);
        if ($hdl) {
            $last_priority = -1;
            while (false !== ($item = $hdl->read())) {

                // Si on a un fichier caché...
                if ($item{0} == '.' || !is_dir(DIR_PLUGINS_OBJ.'/'.$item)) {
                    continue;
                }

                $manifest = plugin_obj::getManifest($item);
                if ($manifest && in_array($cobj->extension, $manifest->extension)) {
                    if (!$ret || $manifest->priority > $last_priority) {
                        $ret = $manifest;
                    }
                    $last_priority = $manifest->priority;
                }
            }
        }

        return $ret;
    }

    /**
     *  Search plugin for $obj and return it !
     *  @param  object  $obj   Object
     *  @access static
     */
    function search($obj) {

        global $dcache;
        
        $conf = conf::getInstance();

        // Already cached ?
        $ret = $dcache->get('plugin_obj', $obj->extension);
        if (!$ret) {
            switch ($obj->type) {
                case TYPE_ARCHIVED:

                    require_once HYLA_ROOT_PATH.'src/inc/cache.class.php';
                    if (!cache::getFilePath($obj->file, $file)) {
                        archive::extract($obj->realpath, $file);
                    }

                    // File not found in archive
                    if (!file_exists(HYLA_ROOT_PATH.'/'.$file.'/'.$obj->target)) {
                        system::end(__('Error: File not found !'));
                    }

                    // Try to open a new archive ?
                    if ($obj->extension == 'zip' ||
                        $obj->extension == 'gz' ||
                        $obj->extension == 'tar' ||
                        $obj->extension == 'tar.gz' ||
                        $obj->extension == 'tgz') {
                        system::end(__('Error: Not implemented !'));
                    }

                case TYPE_FILE:
                    $ret = plugin_obj::searchFilePlugin($obj);
                    break;

                case TYPE_DIR:
                    $default_dir = strtolower($conf->get('plugin_default_dir'));
                    $ret = plugin_obj::getManifest(($obj->info->plugin) ? $obj->info->plugin : $default_dir);
                    break;
            }

            if ($ret) {
                include_once($ret->dir.'index.php');
            }

            // Cache result
            $dcache->add('plugin_obj', $obj->extension, $ret);
        }

        // If plugin not found
        if (!$ret) {
            $ret = plugin_obj::getManifest('default');
            $dcache->add('plugin_obj', $obj->extension, $ret);
        }

        return $ret;
    }

    /**
     *  Load plugin
     *  @param  string  $plugin Plugin
     *  @return On renvoie le contenu généré par le plugin
     *  @access static
     */
    function load($plugin = null) {

        $obj = obj::getInstance();
        $cobj = $obj->getCurrentObj();
        $url = plugins::get(PLUGIN_TYPE_URL);
        $l10n = l10n::getInstance();

        $plugin_dir = strtolower($plugin->name);  // ToDo: trouvé une soluce pour les strtolower

        $pfile = $plugin->dir.'/index.php';

        // Exists plugin ?
        if (!file_exists($pfile)) {
            system::end(__('Error : Inexistant plugin %s !', $plugin->name));
        }

        include_once($pfile);

        $pname = 'plugin_obj_'.strtolower($plugin->name);

        // Y'a t-il un fichier de langue ?
        $l10n_file = $plugin->dir.'/l10n';
        if (file_exists($l10n_file)) {
            $l10n->setSpecialFile($plugin->dir, 'messages.php');
        }

        // Chargement de la classe
        $o = new $pname($cobj->file);

        // Ajout de la css commune de plugins
//        echo 'modify here !!';
//        $name = template::get_file(DIR_TEMPLATE.'/css/plugins.css');
        $name = $this->tpl->get_special_file(array( HYLA_RUN_PATH,
                                                    HYLA_ROOT_PATH), DIR_TPL, 'css/plugins.css');//, false);
//        system::end();
//echo '<h1>'.$name.'</h1>';
        if ($name) {
            add_stylesheet($name, $o->plugin_name, 'text/css', 'screen/projection', true);
        }

        // Exécution de act()
        if (method_exists($o, 'act')) {
            $o->act($url->getParam('pact'));
        }

        // Y'a t-il une méthode fullscreen ?
        if (method_exists($o, 'fullscreen') && $plugin) {

            //  If fullscreen return, true, then exit
            if ($o->fullscreen($url->getParam('paff'))) {
                system::end();
            }
        }

        // Exécution de aff()
        $var_tpl = $o->aff($url->getParam('paff'));
        $o = null;

        return $var_tpl;
    }

    /*  Session var
     */

    /*  Sauve une variable dans la session courante
     */
    function saveVar($name, $value) {
        return $_SESSION['sess_'.$this->plugin_name.'_'.$name] = $value;
    }

    /*  Récupère une variable de la session courante
     */
    function getVar($name) {
        $key = 'sess_'.$this->plugin_name.'_'.$name;
        if (isset($_SESSION) && array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }
    }

    /*  Ajoute la css d'un plugin
        @param  string  $name   La css, à partir du chemin du plugin
     */
    function addStyleSheet($name) {
        add_stylesheet_plugin($this->plugin_dir.$this->plugin_name.'/'.$name, $this->plugin_name);
    }

    /*  Add header
        @param  string  $markup     Markup name
        @param  array   $attr       Attribut
        @param  string  $content    Content
     */
    function addHeader($markup, $attr, $content = null) {
        add_page_header($markup, $attr, $content);
    }
}

?>
