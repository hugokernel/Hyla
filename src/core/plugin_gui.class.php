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

require_once HYLA_ROOT_PATH.'src/lib/template.class.php';

class tPluginManifestGui extends tPluginManifest {
    var $cat = null;
    var $rights = 0;
    var $priority = 0;
    var $menu = null;
    var $obj_type = 0;
}

class plugin_gui extends plugin
{
    var $obj;

    var $bdd;

    var $tpl;

    var $url;

    var $l10n;

    var $events;

    var $_url_2_plugin;

    function plugin_gui() {
        parent::plugin();

        $this->obj = obj::getInstance();

        $this->tpl = new Template(null);
        $this->tpl->set_root(HYLA_ROOT_PATH.$this->plugin_dir.$this->plugin_name);

        $this->url = plugins::get(PLUGIN_TYPE_URL);

        $this->l10n = l10n::getInstance();

        $this->events = array('onsuccess'  => null);    // array('msg' => null, 'redirect' => null));

        $this->_url_2_plugin = system::getHost().HYLA_ROOT_URL.$this->plugin_dir.$this->plugin_name.'/';
    }

    /**
     *  Run gui
     */
    function run() {

        run_trigger(PRE_PLUGIN_GUI_RUN, $this->plugin_name);

        $class = null;
        $p = array();

        // Get manifest
        $manifest = $this->getManifest();
        if (!$manifest) {
            return new tError(__('No manifest found !'), $this);
        }

        // Test rights
        if ($manifest->rights) {

            // Test user
            if (array_key_exists('user', $manifest->rights) && !acl::test($manifest->rights['user'])) {
                return new tError(__('You do not have the sufficient rights !'), $this);
            }

            // Test user
            if (array_key_exists('user_type', $manifest->rights) && !acl::test($manifest->rights['user_type'])) {
                return new tError(__('You do not have the sufficient rights !'), $this);
            }

            // Test rights
            if (array_key_exists('acl', $manifest->rights) && !acl::ok($manifest->rights['acl'])) {
                return new tError(__('You do not have the sufficient rights !'), $this);
            }
        }

        // l10n is present ?
        $l10n_file = $this->plugin_dir.$this->plugin_name.'/l10n';
        if ($this->l10n->testFile($l10n_file, 'manifest.php')) {
            $this->l10n->setSpecialFile($this->plugin_dir.$this->plugin_name, 'messages.php');
        }

        // Run action
        if (method_exists($this, 'act')) {
            $ret = $this->act();
            if ($ret && !system::isError($ret) && $this->events['onsuccess']) {
                $cobj = $this->obj->getCurrentObj();

                switch ($this->events['onsuccess']['redirect']) {
                    case 'root':
                        $path = $this->url->linkToObj(file::downPath($cobj->path));
                        break;
                    case 'current':                                      
                        $path = $this->url->linkToObj($cobj->file);
                        break;
                    case 'last':
                        $path = $this->url->linkToObj('/');    // ToDo: save last object before auth
                        break;
                    default:
                        $path = $this->events['onsuccess']['redirect'];
                }

                $msg = ($this->events['onsuccess']['msg']) ? $this->events['onsuccess']['msg'] : __('You will be redirected towards the object !');
                redirect(__('Ok'), $path, $msg);
                system::end();
            }

            // Save last error
            if (system::isError($ret)) {
                $this->last_error = $ret;
//                exit('Sys error in '.$this->getContext());
            }
        }

        // Run aff
        $aff = $this->aff();

        run_trigger(POST_PLUGIN_GUI_RUN, $this->plugin_name, $aff);

        return $aff;
    }

    /**
     *  Get manifest
     *  @param  string  $name   Plugin name
     */
    function getManifest($name = null) {

        $ret = false;

        if (!$name) {
            $name = $this->plugin_name;
        }

        $manifest = plugin::getManifestFile(PLUGIN_TYPE_GUI, $name);
        if (!$manifest) {
            return $ret;
        }

        $ret = new tPluginManifestGui;
        $ret->dir = HYLA_ROOT_PATH.DIR_PLUGINS_GUI.$name.'/';
        $ret->name = $manifest[M_NAME];
        $ret->description = $manifest[M_DESCRIPTION];

        if (isset($manifest[M_AUTHOR])) {
            $ret->author = $manifest[M_AUTHOR];
        }

        if (isset($manifest[M_VERSION])) {
            $ret->version = $manifest[M_VERSION];
        }

        if (isset($manifest[M_MENU])) {
            $ret->menu = $manifest[M_MENU];
        }

        if (isset($manifest[M_CAT])) {
            $ret->cat = $manifest[M_CAT];
        }

        if (isset($manifest[M_PRIORITY])) {
            $ret->priority = $manifest[M_PRIORITY];
        }

        if (isset($manifest[M_RIGHTS])) {
            $ret->rights = $manifest[M_RIGHTS];
        }

        if (isset($manifest[M_OBJ_TYPE])) {
            $ret->obj_type = $manifest[M_OBJ_TYPE];
        }

        if (isset($manifest[M_TRIGGERS])) {
            $trig = trigger::getInstance();
            foreach ($manifest[M_TRIGGERS] as $name => $param) {
                $trig->register($name, $param[M_CALLBACK], $param[M_CONTEXT]);
            }
        }

        return $ret;
    }

    /**
     *  Get plugin
     *  @param  string  $cat        Category
     *  @param  string  $context    Context
     *  @static
     */
    function getPlugin($cat, $context = null) {
        global $cuser;

        $l10n = l10n::getInstance();

        $ret = array();

        $plugin_dir = plugins::getDirFromType(PLUGIN_TYPE_GUI);
        $hdl = dir(HYLA_ROOT_PATH.$plugin_dir);
        if (!$hdl) {
            return $ret;
        }

        while (false !== ($dir = $hdl->read())) {

            $manifest = plugin_gui::getManifest($dir);
            if (!$manifest) {
                continue;
            }

            // Test right
            if (array_key_exists('user_type', $manifest->rights) && !acl::compare($manifest->rights['user_type'])) {
                continue;
            }

            // Test right
            if (array_key_exists('user', $manifest->rights) && !acl::test($manifest->rights['user'])) {
                continue;
            }

            // Get cat
            if (!$manifest->cat || $manifest->cat != $cat) {
                continue;
            }

            // Get context
            if ($context && $manifest->context && $manifest['context'] != $context) {
                continue;
            }

            // Test if l10n manifest is present
            $l10n_file = $plugin_dir.$dir;
            if ($l10n->testFile($l10n_file, 'manifest.php')) {
                $l10n->setSpecialFile($plugin_dir.$dir, 'manifest.php');
            }

            $ret[$dir] = $manifest;
        }

        // Sorting
        if ($ret) {
            uasort($ret, array('plugin_gui', 'sort'));
        }

        return $ret;
    }

    /**
     *  Sort plugin from manifest
     */
    function sort($a, $b) {

        if ($a->priority == $b->priority) {
            return 0;
        }

        return ($a->priority < $b->priority ? -1 : 1);
    }
}

?>
