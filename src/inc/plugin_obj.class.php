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

class plugin_obj extends plugin
{
    public $tpl;
    public $obj;
    public $url;
    public $cobj;

    public $real_file;        // Le chemin d'accès à l'objet courant, très utile car, il va chercher dans le cache (ex: fichier contenus dans zip, tar...)

    public function __construct() {

        global $tpl, $obj, $cobj, $url, $cuser;

        parent::__construct();

        $this->tpl = $tpl;

        $this->obj = &$obj;
        $this->cobj = $cobj;

        $this->url = &$url;

        $this->_conf = array();

        $this->real_file = get_real_directory();
    }

    /*  Renvoie un tableau contenant les plugins disponibles pour un répertoire
        @access static
        /!\ Factoriser le code ci dessous avec le reste du code, c'est pas très propre... /!\
     */
    public static function getDirPlugins() {
        return plugin_obj::_getPlugins(true);
    }

    /*  Renvoie un tableau contenant les plugins disponibles pour un fichier
        @access static
     */
    public static function getFilePlugins() {
        return plugin_obj::_getPlugins(false);
    }

    private static function _getPlugins($type = true) {

        $tab = array();

        $hdl = dir(DIR_PLUGINS_OBJ);
        if ($hdl) {
            while (false !== ($occ = $hdl->read())) {

                // Si on a un fichier caché...
                if ($occ{0} == '.')
                    continue;

                // Si ce n'est pas un répertoire
                if (!is_dir(DIR_PLUGINS_OBJ.'/'.$occ))
                    continue;

                $xfile = DIR_PLUGINS_OBJ.$occ.'/info.xml';
                if (file_exists($xfile)) {
                    $xml = new XPath($xfile);
                    $exp = $type ? '/plugin[contains(@target,"dir")]/*' : '/plugin[contains(@target,"file") ]/*';
                    $res = $xml->match($exp);
                    if ($res) {
                        $tab[] = array(
                                    'dir'           =>  $xfile,
                                    'name'          =>  $xml->getData('/plugin/name'),
                                    'description'   =>  $xml->getData('/plugin/description'),
                                    'author'        =>  $xml->getData('/plugin/author'),
                                    'version'       =>  $xml->getData('/plugin/version'),
                                );
                    }
                }
            }
        }

        return $tab;
    }
    
    /*  Get the plugin for the object
        @param  object  $cobj   Current object
     */
    public function searchFilePlugin($cobj) {
        $ret = null;
        $hdl = dir(DIR_PLUGINS_OBJ);
        if ($hdl) {
            $last_priority = -1;
            while (false !== ($occ = $hdl->read())) {

                // Si on a un fichier caché...
                if ($occ{0} == '.' || !is_dir(DIR_PLUGINS_OBJ.'/'.$occ)) {
                    continue;
                }
        
                $infos = plugin::loadInfo(DIR_PLUGINS_OBJ.$occ);
                if ($infos) {
                    if (in_array($cobj->extension, $infos->extension)) {
                        if (!$ret || $infos->priority > $last_priority) {
                            $ret = $infos;
                        }
                        $last_priority = $infos->priority;
                    }
                }
            }
        }
        return $ret;
    }

    /*  Search plugin and return it !
        @access static
     */
    public static function search() {

        global $conf, $cobj, $dcache;

        // Already cached ?
        $ret = $dcache->get('plugin_obj', $cobj->extension);
        if (!$ret) {
            switch ($cobj->type) {
                case TYPE_ARCHIVED:

                    if (!cache::getFilePath($cobj->file, $file)) {
                        archive::extract($cobj->realpath, $file);
                    }

                    // File not found in archive
                    if (!file_exists($file.'/'.$cobj->target)) {
                        system::end(__('Error: File not found !'));
                        break;
                    }

                    // Try to open a new archive ?
                    if ($cobj->extension == 'zip' ||
                        $cobj->extension == 'gz' ||
                        $cobj->extension == 'tar' ||
                        $cobj->extension == 'tar.gz' ||
                        $cobj->extension == 'tgz') {
                        system::end(__('Error: Not implemented !'));
                        break;
                    }

                case TYPE_FILE:
                    $ret = plugin_obj::searchFilePlugin($cobj);
                    break;

                case TYPE_DIR:
                    $infos = plugin::loadInfo(DIR_PLUGINS_OBJ.(($cobj->info->plugin) ? $cobj->info->plugin : strtolower($conf['plugin_default_dir'])));
                    $ret = &$infos;
                    break;
            }

            if ($ret) {
                include_once(DIR_PLUGINS_OBJ.$ret->name.'/index.php');
            }

            // Cache result
            $dcache->add('plugin_obj', $cobj->extension, $ret);
        }

        // If plugin not found
        if (!$ret) {
            $ret = plugin::loadInfo(DIR_PLUGINS_OBJ.'default');
            $dcache->add('plugin_obj', $cobj->extension, $ret);
        }

        return $ret;
    }

    /*  Charge le plugin correspondant
        @return On renvoie le contenu généré par le plugin
        @access static
     */
    public static function load($plugin = null) {

        global $conf, $l10n, $cobj, $url;
        
        $var_tpl = null;

        $plugin_dir = $plugin->name;

        $pfile = DIR_PLUGINS_OBJ.$plugin_dir.'/index.php';

        if (file_exists($pfile)) {
            include_once($pfile);

            $pname = 'plugin_obj_';
            $pname .= $plugin_dir;

            // Y'a t-il un fichier de langue ?
            $l10n_file = DIR_PLUGINS_OBJ.$plugin_dir.'/l10n';
            if (file_exists($l10n_file)) {
                $l10n->setSpecialFile(DIR_PLUGINS_OBJ.$plugin_dir.'/', 'messages.php');
            }

            // Chargement de la classe
            $o = new $pname($cobj->file);

            // Ajout de la css commune de plugins
            $name = template::get_file(DIR_TEMPLATE.'/css/plugins.css');
            if ($name) {
                add_stylesheet_plugin($name, $o->plugin_name);
            }

            // Exécution de act()
            if (method_exists($o, 'act')) {
                $o->act($url->getParam('pact'));
            }

            // Y'a t-il une méthode fullscreen ?
            if (method_exists($o, 'fullscreen') && $plugin) {
                /*  Si la méthode fullscreen renvoie true, on stoppe l'exécution,
                    sinon, on continue
                 */
                if ($o->fullscreen($url->getParam('paff'))) {
                    system::end();
                }
            }

            // Exécution de aff()
            $var_tpl = $o->aff($url->getParam('paff'));
            $o = null;
        } else {
            system::end(__('Error : Inexistant plugin %s !', $plugin->name));
        }

        return $var_tpl;
    }

    /*  Session var
     */

    /*  Sauve une variable dans la session courante
     */
    public function saveVar($name, $value) {
        return $_SESSION['sess_'.$this->plugin_name.'_'.$name] = $value;
    }

    /*  Récupère une variable de la session courante
     */
    public function getVar($name) {
        $key = 'sess_'.$this->plugin_name.'_'.$name;
        if (isset($_SESSION) && array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }
    }

    /*  Ajoute la css d'un plugin
        @param  string  $name   La css, à partir du chemin du plugin
     */
    public function addStyleSheet($name) {
        add_stylesheet_plugin($this->plugin_dir.$this->plugin_name.'/'.$name, $this->plugin_name);
    }

    /*  Add header
        @param  string  $markup     Markup name
        @param  array   $attr       Attribut
        @param  string  $content    Content
     */
    public function addHeader($markup, $attr, $content = null) {
        add_page_header($markup, $attr, $content);
    }
}
