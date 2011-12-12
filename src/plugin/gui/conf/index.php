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

class plugin_gui_conf extends plugin_gui {

    function plugin_gui_conf() {
       parent::plugin_gui();
    }

    function act() {

        // Saving configuration
        if ($this->url->getParam('aff', 3) == 'save') {
            

            // Affichage
            $this->conf->set(array('title'        =>     $_POST['conf_title']));
          

            list($template, $style) = explode('|', $_POST['conf_template']);
            $this->conf->set(array('template'     =>     $template));
            $this->conf->set(array('style'        =>     $style));

            $this->conf->set(array('view_toolbar' =>     $_POST['conf_view_toolbar']));
            $this->conf->set(array('view_tree'    =>     $_POST['conf_view_tree']));
            $this->conf->set(array('view_hidden_file'=>  $_POST['conf_view_hidden_file']));

            // Ajout de fichiers et dossiers
            $this->conf->set(array('file_chmod'   =>     $_POST['conf_file_chmod']));
            $this->conf->set(array('dir_chmod'    =>     $_POST['conf_dir_chmod']));
            $this->conf->set(array('anon_file_send'=>    $_POST['conf_anon_file_send']));

            // Listage de répertoires
            $this->conf->set(array('sort'         =>     $_POST['conf_sort']));
            $this->conf->set(array('folder_first' =>     $_POST['conf_folder_first']));
            $this->conf->set(array('group_by_sort'=>     $_POST['conf_group_by_sort']));
            $this->conf->set(array('nbr_obj'      =>     $_POST['conf_nbr_obj']));

            // Divers
            $this->conf->set(array('webmaster_mail'=>    $_POST['conf_webmaster_mail']));
            $this->conf->set(array('lng'          =>     $_POST['conf_lng']));
            $this->conf->set(array('download_counter'=>   $_POST['conf_download_counter']));

            $this->conf->set(array('time_of_redirection'=>   $_POST['conf_time_of_redirection'] < 1 ? 1 : $_POST['conf_time_of_redirection']));

            $this->conf->set(array('download_dir'  =>    $_POST['conf_download_dir']));

            $this->conf->set(array('fs_charset_is_utf8' => $_POST['conf_fs_charset_is_utf8']));

            $this->conf->set(array('register_user' =>      $_POST['conf_register_user']));

            if (plugins::isValid($_POST['conf_plugin_default_dir'])) {
                $this->conf->set(array('plugin_default_dir'=>    $_POST['conf_plugin_default_dir']));
            }

            $tpl_changed = ($template != $this->conf->get('template_name')) ? true : false;

              /* Save configuration */
                 $this->conf->save();
                 
               /* Load configuration */
                 $this->conf->load();

                // Si le template change, on redirige
                if ($tpl_changed) {
                    redirect($this->cobj->file, $this->url->linkToPage(array('admin', 'conf')), __('The new template will be applied !'));
                    system::end();
                }

                $msg_status = __('Configuration was correctly recorded !');
        

          
        }
    }

    function aff() {
    
    
        $msg = null;
        $msg_error = null;
    
        $this->tpl->set_root($this->plugin_dir.'conf');
        $this->tpl->set_file('conf', 'tpl/conf.tpl');
        $this->tpl->set_block('conf', array(
            'aff_conf_template_style'   =>  'Hdlaff_conf_template_style',
            'aff_conf_template'         =>  'Hdlaff_conf_template',
            'aff_conf_plugin'           =>  'Hdlaff_conf_plugin'
        ));

        $this->tpl->set_var(array(
            'WEBMASTER_MAIL'        =>  $this->conf->get('webmaster_mail'),
            'TIME_OF_REDIRECTION'   =>  $this->conf->get('time_of_redirection'),
            'CURRENT_TEMPLATE'      =>  $this->conf->get('template_name'),
    
            'TITLE'                 =>  $this->conf->get('title'),
            'LNG'                   =>  $this->conf->get('lng'),
            'FILE_CHMOD'            =>  decoct($this->conf->get('file_chmod')),
            'DIR_CHMOD'             =>  decoct($this->conf->get('dir_chmod')),
    
            'ADMIN_PAGE_SAVECONF'   =>  $this->url->linkToPage(array('admin', 'conf', 'save'))
            )
        );

        // Action en cas de fichier anonyme...
        switch ($this->conf->get('anon_file_send')) {
            case 0:   $this->tpl->set_var('CONF_ANON_FILE_SEND_0', 'selected="selected"');    break;
//            case 2:   $tpl->set_var('CONF_ANON_FILE_SEND_2', 'selected="selected"');    break;
//            case 3:   $tpl->set_var('CONF_ANON_FILE_SEND_3', 'selected="selected"');    break;
            case 1:
            default:    $this->tpl->set_var('CONF_ANON_FILE_SEND_1', 'selected="selected"');  break;
        }

        // Listage des répertoires de tpl/
        $hdl = dir(DIR_ROOT.DIR_TPL);
        if ($hdl) {
            while (false !== ($tpl_name = $hdl->read())) {

                $xfile = DIR_ROOT.DIR_TPL.$tpl_name.'/info.xml';
                if (!file_exists($xfile)) {
                    continue;
                }

                $this->tpl->set_var('Hdlaff_conf_template_style');
                $this->tpl->set_var('TEMPLATE_NAME', $tpl_name);

                $xml =& new XPath($xfile);
                $res = $xml->match('/template');
                if ($res) {
                    $res = $xml->match('/template/stylesheets/stylesheet');
                    if (!$res) {
                        continue;
                    }
                    
                    foreach ($res as $occ) {
                        $style_title = $xml->getData($occ.'/title');
                        $style_file = $xml->getData($occ.'/href');
                        $this->tpl->set_var(array(
                            'STYLE_NAME'            =>  $style_title,
                            'STYLE_FILE'            =>  $style_file,
                            'CONF_TEMPLATE_NAME'    =>  ($tpl_name == $this->conf->get('template_name') && $style_file == $this->conf->get('style')) ? 'selected="selected"' : null
                            )
                        );
                        $this->tpl->parse('Hdlaff_conf_template_style', 'aff_conf_template_style', true);
                    }
                }

                $this->tpl->parse('Hdlaff_conf_template', 'aff_conf_template', true);
            }
            unset($hdl);
        }

        $folder_first = false;

        switch ($this->conf->get('sort_config')) {
            case SORT_DEFAULT:
                $this->tpl->set_var('CONF_SORT_0', 'selected="selected"');
                break;
            case SORT_NAME_ALPHA:
                $this->tpl->set_var('CONF_SORT_1', 'selected="selected"');
                break;
            case SORT_NAME_ALPHA | SORT_FOLDER_FIRST:
                $this->tpl->set_var('CONF_SORT_1', 'selected="selected"');
                $folder_first = true;
                break;
            case SORT_NAME_ALPHA_R:
                $this->tpl->set_var('CONF_SORT_2', 'selected="selected"');
                break;
            case SORT_NAME_ALPHA_R | SORT_FOLDER_FIRST:
                $this->tpl->set_var('CONF_SORT_2', 'selected="selected"');
                $folder_first = true;
                break;
            case SORT_EXT_ALPHA:
                $this->tpl->set_var('CONF_SORT_3', 'selected="selected"');
                break;
            case SORT_EXT_ALPHA | SORT_FOLDER_FIRST:
                $this->tpl->set_var('CONF_SORT_3', 'selected="selected"');
                $folder_first = true;
                break;
            case SORT_EXT_ALPHA_R:
                $this->tpl->set_var('CONF_SORT_4', 'selected="selected"');
                break;
            case SORT_EXT_ALPHA_R | SORT_FOLDER_FIRST:
                $this->tpl->set_var('CONF_SORT_4', 'selected="selected"');
                $folder_first = true;
                break;
            case SORT_CAT_ALPHA:
                $this->tpl->set_var('CONF_SORT_5', 'selected="selected"');
                break;
            case SORT_CAT_ALPHA | SORT_FOLDER_FIRST:
                $this->tpl->set_var('CONF_SORT_5', 'selected="selected"');
                $folder_first = true;
                break;
            case SORT_CAT_ALPHA_R:
                $this->tpl->set_var('CONF_SORT_6', 'selected="selected"');
                break;
            case SORT_CAT_ALPHA_R | SORT_FOLDER_FIRST:
                $this->tpl->set_var('CONF_SORT_6', 'selected="selected"');
                $folder_first = true;
                break;
            case SORT_SIZE:
                $this->tpl->set_var('CONF_SORT_7', 'selected="selected"');
                break;
            case SORT_SIZE | SORT_FOLDER_FIRST:
                $this->tpl->set_var('CONF_SORT_7', 'selected="selected"');
                $folder_first = true;
                break;
            case SORT_SIZE_R:
                $this->tpl->set_var('CONF_SORT_8', 'selected="selected"');
                break;
            case SORT_SIZE_R | SORT_FOLDER_FIRST:
                $this->tpl->set_var('CONF_SORT_8', 'selected="selected"');
                $folder_first = true;
                break;
        }

        $this->tpl->set_var(array(
            ($folder_first ? 'CONF_FOLDER_FIRST_1' : 'CONF_FOLDER_FIRST_0')     =>  'selected="selected"',
            ($this->conf->get('group_by_sort') ? 'CONF_GROUP_BY_SORT_1' : 'CONF_GROUP_BY_SORT_0')  =>  'selected="selected"',
            'NBR_OBJ'       =>  $this->conf->get('nbr_obj'),
            )
        );

        $this->tpl->set_var(array(
            ($this->conf->get('view_hidden_file') ? 'CONF_VIEW_HIDDEN_FILE_1' : 'CONF_VIEW_HIDDEN_FILE_0') =>  'selected="selected"',
            ($this->conf->get('download_counter') ? 'CONF_DOWNLOAD_COUNTER_1' : 'CONF_DOWNLOAD_COUNTER_0') =>  'selected="selected"',
            ($this->conf->get('download_dir') ? 'CONF_DOWNLOAD_DIR_1' : 'CONF_DOWNLOAD_DIR_0') =>  'selected="selected"',
            ($this->conf->get('fs_charset_is_utf8') ? 'CONF_FS_CHARSET_IS_UTF8_1' : 'CONF_FS_CHARSET_IS_UTF8_0')   =>  'selected="selected"',
            ($this->conf->get('view_toolbar') ? 'CONF_VIEW_TOOLBAR_1' : 'CONF_VIEW_TOOLBAR_0')     =>  'selected="selected"',
            ($this->conf->get('register_user') ? 'CONF_REGISTER_USER_1' : 'CONF_REGISTER_USER_0')  =>  'selected="selected"',
            'CONF_VIEW_TREE_0'      =>  ($this->conf->get('view_tree') == 0) ? 'selected="selected"' :  null,
            'CONF_VIEW_TREE_1'      =>  ($this->conf->get('view_tree') == 1) ? 'selected="selected"' :  null,
            'CONF_VIEW_TREE_2'      =>  ($this->conf->get('view_tree') == 2) ? 'selected="selected"' :  null
            )
        );

        // Listage des répertoires des plugins
        $tab_plugins = plugin_obj::getDirPlugins();
        foreach ($tab_plugins as $occ) {
            $this->tpl->set_var(array(
                'PLUGIN_NAME'           =>  $occ['name'],
                'PLUGIN_DESCRIPTION'    =>  $occ['description'],
                'CONF_PLUGIN_NAME'      =>  (strtolower($occ['name']) == strtolower($this->conf->get('plugin_default_dir')) ? 'selected="selected"' : null)
                )
            );
            $this->tpl->parse('Hdlaff_conf_plugin', 'aff_conf_plugin', true);
        }

        $this->tpl->set_var(array(
            'ERROR'     =>  isset($msg_error) ? view_error($msg_error) : null,
            'STATUS'    =>  isset($msg_status) ? view_status($msg_status) : null,
            )
        );

        return $this->tpl->parse('OutPut', 'conf');
    }
}

?>
