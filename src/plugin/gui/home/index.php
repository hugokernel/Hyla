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

class plugin_gui_home extends plugin_gui {

    function plugin_gui_home() {
        parent::plugin_gui();
    }

    function act() {

        switch ($this->url->getParam('aff', 2)) {
            case 'testver':
                if (!ini_get('allow_url_fopen')) {
                    return new tError(__('Remote download is disabled in your configuration !'), $this);
                }

                $var = file::getContent(URL_TEST_VERSION);
                if (!$var) {
                    break;
                }

                if (strcmp(trim(HYLA_VERSION), trim($var)) < 0) {
                    $this->last_status_msg = __('A new version ( %s ) is disponible !', $var);
                } else {
                    $this->last_status_msg = __('You have the latest version !');
                }

                break;
        }

    }

    function aff() {

        // Declare tpl
        $this->tpl->set_file('home', 'tpl/home.tpl');
        $this->tpl->set_block('home', array(
            'test_version'      =>  'Hdltest_version',
            'test_ok'           =>  'Hdltest_ok',
            'test_no'           =>  'Hdltest_no',
        ));

        // Test if remote download is enabled
        if (ini_get('allow_url_fopen')) {
            $this->tpl->parse('Hdltest_version', 'test_version', true);
        }

        $ok = $this->tpl->get_var('test_ok');
        $no = $this->tpl->get_var('test_no');

        $root_dir = $this->obj->getRoot();
        $this->tpl->set_var(array(
            'CONFIG_FILE'               =>  CONFIG_FILE,

            'FOLDER_ROOT'               =>  $root_dir,
            'FOLDER_ROOT_READING'       =>  is_readable($root_dir) ? $ok : $no,
            'FOLDER_ROOT_WRITING'       =>  is_writable($root_dir) ? $ok : $no,

            'FOLDER_ROOT_ERROR_MSG'     =>  !is_readable($root_dir) ? view_error(__('The root dir is not readable !', CONFIG_FILE)) : null,

            'PATH_TO_SCRIPT'            =>  dirname($_SERVER['SCRIPT_FILENAME']),

            'WEBMASTER_MAIL'            =>  $this->conf->get('webmaster_mail') ? __('Webmaster mail is %s', $this->conf->get('webmaster_mail')) : __('Webmaster mail is not set !'),

            'CONFIG_ALLOW_URL_FOPEN'    =>  ini_get('allow_url_fopen') ? $ok : $no,
            'CONFIG_FILE_UPLOADS'       =>  ini_get('file_uploads') ? $ok : $no,
            'CONFIG_UPLOAD_MAX_FILESIZE'=>  ini_get('upload_max_filesize'),

            'DIR_CACHE'                 =>  DIR_CACHE,
            'DIR_ANON'                  =>  DIR_ANON,

            'ACCESS_DIR_CACHE'          =>  is_writable(HYLA_RUN_PATH.DIR_CACHE) ? $ok : $no,
            'ACCESS_DIR_ANON'           =>  is_writable(HYLA_RUN_PATH.DIR_ANON) ? $ok : $no,

            'EXTENSION_GD'              =>  extension_loaded('gd') ? $ok : $no,
            'EXTENSION_EXIF'            =>  extension_loaded('exif') ? $ok : $no,

            'MSG_ERROR'                 =>  ($this->last_error_msg) ? view_error($this->last_error_msg) : null,
            'STATUS_VERSION'            =>  ($this->last_status_msg) ? $this->last_status_msg : null,

            'URL_TEST_VERSION'          =>  $this->url->linkToPage(array('admin', 'testver'))
        ));

        return $this->tpl->parse('OutPut', 'home');
    }
}

?>
