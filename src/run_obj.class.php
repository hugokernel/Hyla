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
require_once HYLA_ROOT_PATH.'src/inc/function.inc.php';
require_once HYLA_ROOT_PATH.'tpl/default/function.php';    // To delete !!

class run_obj {

    var $tpl;
    var $url;

    var $view_title;
    var $view_tree;
    var $view_pagination;
    var $view_comments;
    var $entire_page;
    var $view_toolbar;
    var $only_plugin_content;
    
    var $content;

    var $actions;   // Array action
    
    var $conf;

    var $cobj;

    /**
     *  Constructor
     */
    function run_obj($cobj = null) {

        global $url;    //, $tpl;
//        $this->tpl = &$tpl;
//        $this->tpl = new Template(HYLA_ROOT_PATH.DIR_TEMPLATE, DIR_TEMPLATE);
        $this->url = &$url;

        $conf = conf::getInstance();

        $this->tpl = new Template(DIR_TPL, array(   HYLA_RUN_PATH,
                                                    HYLA_ROOT_PATH), $conf->get('template_name'));

        $this->view_title = true;
        $this->view_pagination = true;
        $this->entire_page = false;
        $this->view_comments = false;
        $this->view_tree = $conf->get('view_tree');
        $this->view_toolbar = true;
        $this->only_plugin_content = false;

        $this->tpl->set_file('obj_n', 'obj_n.tpl');

        $this->tpl->set_block('obj_n', array(
                'action'                =>  'Hdlaction',
                'actions'               =>  'Hdlactions',

                'rss_obj'               =>  'Hdlrss_obj',
                'rss_comment'           =>  'Hdlrss_comment',

                'html_header'           =>  'Hdlhtml_header',
                'main'                  =>  'Hdlmain',
                'main_end'              =>  'Hdlmain_end',
                'html_footer'           =>  'Hdlhtml_footer',

                'title'                 =>  'Hdltitle',

                'dir_previous_page'     =>  'Hdldir_previous_page',
                'dir_page_num_cur'      =>  'Hdldir_page_num_cur',
                'dir_page_num'          =>  'Hdldir_page_num',
                'dir_page'              =>  'Hdldir_page',
                'dir_next_page'         =>  'Hdldir_next_page',
                'dir_pagination'        =>  'Hdldir_pagination',

                'file_previous_page'    =>  'Hdlfile_previous_page',
                'file_next_page'        =>  'Hdlfile_next_page',
                'file_pagination'       =>  'Hdlfile_pagination',
                
                'sort'                  =>  'Hdlsort',

//                'description'           =>  'Hdldescription',

                'tree'                  =>  'Hdltree',
                'no_tree'               =>  'Hdlno_tree'
                ));

        $this->content = null;
        
        $this->title = null;
        $this->icon = null;

        $this->conf = conf::getInstance();

        if (!$cobj) {
            $obj = obj::getInstance();
            $cobj = $obj->getCurrentObj();
        }

        $this->cobj = &$cobj;

//        echo '#'.$this->cobj;
    }

    /**
     *  Parameters...
     */
    function viewTitle($head)           { $this->view_title = $head; }
    function viewTree($tree)            { $this->view_tree = $tree; }
    function viewPagination($page)      { $this->view_pagination = $page; }
    function viewComments($comments)    { $this->view_comments = $comments; }
    function viewHeader($page)          { $this->entire_page = $page; }
    function viewToolbar($toolbar)      { $this->view_toolbar = $toolbar; }
    function onlyPluginContent($plugin) { $this->only_plugin_content = $plugin; }


    /**
     *  Set title
     */
    function setTitle($title, $icon = null) {
        $this->title = $title;
        $this->icon = $icon;
    }

    /**
     *  Set content
     */
    function setContent($content) {
        $this->content = $content;
    }

    /**
     *  Run plugin and format output
     *  @param  $plugin Force plugin
     */
    function run($plugin = null) {

        // Sort, à laisser ici à cause des sess_sort...etc...
        if ($this->cobj->type == TYPE_DIR) {
            $this->parseSort();
        }

        // Title
        if ($this->view_title) {

            if ($this->title) {
                $this->tpl->set_var('OBJECT_TITLE', $this->title);
                $this->tpl->set_var('OBJECT_ICON', $this->icon);
            } else
                $this->tpl->set_var('OBJECT_TITLE', format_title($this->cobj->file));

            $this->tpl->parse('Hdltitle', 'title', true);
        }

        // Load plugin object
        if ($this->content) {
            $out = $this->content;
        } else {
            $plugin = plugins::get(PLUGIN_TYPE_OBJ, $plugin, $this->cobj);
            $out = plugin_obj::load($plugin);

            if ($this->only_plugin_content) {
                return $this->tpl->finish($out);
            }

        }

        // View tree
        if ($this->view_tree) {
            $this->tpl->set_var('TREE_ELEM', get_tree());
            $this->tpl->parse('Hdltree', 'tree', true);
        } else {
            $this->tpl->parse('Hdlno_tree', 'no_tree', true);
        }

        // Pagination if true or if current object is dir
        if ($this->view_pagination || $this->cobj->type == TYPE_DIR) {
            if ($this->cobj->type == TYPE_FILE || $this->cobj->type == TYPE_ARCHIVED) {
                $this->parseFilePagination();
            } else if ($this->cobj->type == TYPE_DIR) {
                $this->parseDirPagination();
            }
        }

        // Toolbar
        if ($this->view_toolbar) {
            $this->tpl->set_var('TOOLBAR', get_toolbar());
        }

        // Actions
        $this->parseAction();

        // View comments
        if ($this->view_comments) {
            $this->tpl->set_var('COMMENTS', get_tpl_comment($this->cobj));
        }

        $this->tpl->set_var(array(
                'URL_ADD_BASKET'    =>  $this->url->linkToPage('basket'),
                'CONTENT'           =>  $out,
                'DESCRIPTION'       =>  $this->cobj->info->description ? string::format($this->cobj->info->description) : __('No description !'),
                'OBJECT'            =>  $this->cobj->file,
        ));

        // Entire page ?
        if ($this->entire_page) {
            $this->tpl->set_var(array(
                    'STYLESHEET'        =>  get_css(),
                    'STYLESHEET_PLUGIN' =>  get_css_plugin(),
            ));
            $this->tpl->parse('Hdlhtml_header', 'html_header', true);
            $this->tpl->parse('Hdlhtml_footer', 'html_footer', true);
        }

        $this->tpl->parse('Hdlmain', 'main', true);
        $this->tpl->parse('Hdlmain_end', 'main_end', true);

        $out = $this->tpl->parse('OutPut', 'obj_n');
        return $this->tpl->finish($out);
    }

    /**
     *  Set action
     *  @param  array   $actions    Actions
     */
    function setAction($actions) {
        $this->actions = $actions;
    }

    /**
     *  Parse actions
     */
    function parseAction() {

        if (count($this->actions) >= 1) {
            $this->tpl->parse('Hdlaction_choice', 'action_choice', true);

            foreach ($this->actions as $value => $name) {
                $this->tpl->set_var(  array(
                    'ACTION_NAME'   =>  $name,
                    'ACTION_VALUE'  =>  $value,
                    )
                );
                $this->tpl->parse('Hdlaction', 'action', true);
            }
            $this->tpl->parse('Hdlactions', 'actions', true);
        }
    }

    function parseSort() {

        $param = @$_REQUEST['param'];

        $sort = (isset($_SESSION['sess_sort'])) ? $_SESSION['sess_sort'] : $this->conf->get('sort_config');
        $grp = (isset($_SESSION['sess_grp'])) ? $_SESSION['sess_grp'] : $this->conf->get('group_by_sort');
        $ffirst = (isset($_SESSION['sess_ffirst'])) ? $_SESSION['sess_ffirst'] : null;

        if (isset($param)) {
            $tab = array(
                    '-1'=> -1,
                    '0' => SORT_DEFAULT,
                    '1' => SORT_NAME_ALPHA,
                    '2' => SORT_NAME_ALPHA_R,
                    '3' => SORT_EXT_ALPHA,
                    '4' => SORT_EXT_ALPHA_R,
                    '5' => SORT_CAT_ALPHA,
                    '6' => SORT_CAT_ALPHA_R,
                    '7' => SORT_SIZE,
                    '8' => SORT_SIZE_R,
                    );
            $grp = $ffirst = $sort = 0;
            foreach ($param as $occ) {
                list($act, $value) = explode(':', $occ);
                if ($act == 'sort') {
                    if ($value == -1)
                        $sort = $this->conf->get('sort_config');
                    if ($value > 0)
                        $sort = (isset($tab[$value]) ? $tab[$value] : $sort);
                    continue;
                }

                if ($act == 'grp' && $value == 'ok') {
                    $grp = 1;
                    continue;
                }

                if ($act == 'ffirst' && $value == 'ok') {
                    $ffirst = 1;
                    continue;
                }
            }

            if ($ffirst)
                $sort |= SORT_FOLDER_FIRST;
        }

        $_SESSION['sess_sort'] = $sort;
        $_SESSION['sess_grp'] = $grp;
        $_SESSION['sess_ffirst'] = $ffirst;

        switch ($sort) {
            case SORT_NAME_ALPHA:
            case SORT_NAME_ALPHA | SORT_FOLDER_FIRST:
                $this->tpl->set_var('SELECT_SORT_1', 'selected="selected"');
                break;
            case SORT_NAME_ALPHA_R:
            case SORT_NAME_ALPHA_R | SORT_FOLDER_FIRST:
                $this->tpl->set_var('SELECT_SORT_2', 'selected="selected"');
                break;
            case SORT_EXT_ALPHA:
            case SORT_EXT_ALPHA | SORT_FOLDER_FIRST:
                $this->tpl->set_var('SELECT_SORT_3', 'selected="selected"');
                break;
            case SORT_EXT_ALPHA_R:
            case SORT_EXT_ALPHA_R | SORT_FOLDER_FIRST:
                $this->tpl->set_var('SELECT_SORT_4', 'selected="selected"');
                break;
            case SORT_CAT_ALPHA:
            case SORT_CAT_ALPHA | SORT_FOLDER_FIRST:
                $this->tpl->set_var('SELECT_SORT_5', 'selected="selected"');
                break;
            case SORT_CAT_ALPHA_R:
            case SORT_CAT_ALPHA_R | SORT_FOLDER_FIRST:
                $this->tpl->set_var('SELECT_SORT_6', 'selected="selected"');
                break;
            case SORT_SIZE:
            case SORT_SIZE | SORT_FOLDER_FIRST:
                $this->tpl->set_var('SELECT_SORT_7', 'selected="selected"');
                break;
            case SORT_SIZE_R:
            case SORT_SIZE_R | SORT_FOLDER_FIRST:
                $this->tpl->set_var('SELECT_SORT_8', 'selected="selected"');
                break;
            case SORT_DEFAULT:
            case SORT_DEFAULT | SORT_FOLDER_FIRST:
                $this->tpl->set_var('SELECT_SORT_0', 'selected="selected"');
                break;
            default:
                $this->tpl->set_var('SELECT_SORT', 'selected="selected"');
                break;
        }

        if ($grp == 1) {
            $this->tpl->set_var('GRP_CHECKED', ' checked="checked"');
        }

        if ($sort & SORT_FOLDER_FIRST) {
            $this->tpl->set_var('FFIRST_CHECKED', ' checked="checked"');
        }

        return $this->tpl->parse('Hdlsort', 'sort');
    }

    /**
     *  Get pagination for dir
     */
    function parseDirPagination() {

        global $obj, $url;

        $start = $url->getParam('arg', 'start');

        $nbr_obj = $obj->getNbrObject();

        if ($this->conf->get('nbr_obj') > 0 && $nbr_obj > $this->conf->get('nbr_obj')) {

            if ($start) {
                $start = ($start >= $nbr_obj) ? (($nbr_obj - $this->conf->get('nbr_obj') < 0) ? 0 : $nbr_obj - $this->conf->get('nbr_obj')) : $start;
            }

            // Page précédente
            if ($start > 0) {
                $this->tpl->parse('Hdldir_previous_page', 'dir_previous_page', true);
                $page = ($start <= $this->conf->get('nbr_obj')) ? 0 : $start - $this->conf->get('nbr_obj');
                $this->tpl->set_var('PREV_PATH', $url->linkToObj($this->cobj->path, null, null, array('start' => $page)));
            }

            // Liste des pages
            $nbr_page = ($nbr_obj / $this->conf->get('nbr_obj'));
            if ($nbr_page) {
                for ($i = 0; $i < $nbr_page; $i++) {
                    $page = $i * $this->conf->get('nbr_obj');
                    $this->tpl->set_var(array(
                            'Hdldir_page_num_cur'   => null,
                            'PAGE_NUM'  =>  $i + 1,
                            'PAGE_URL'  =>  $url->linkToObj($this->cobj->path, null, null, array('start' => $page)),
                            ));

                    if ($start == $page) {
                        $this->tpl->parse('Hdldir_page_num_cur', 'dir_page_num_cur', true);
                    }

                    $this->tpl->parse('Hdldir_page_num', 'dir_page_num', true);
                }
                $this->tpl->parse('Hdldir_page', 'dir_page', true);
            }

            // Page suivante
            if ($start < ($nbr_obj - $this->conf->get('nbr_obj'))) {
                $this->tpl->parse('Hdldir_next_page', 'dir_next_page', true);
                $this->tpl->set_var('NEXT_PATH' , $url->linkToObj($this->cobj->path, null, null, array('start' => $start + $this->conf->get('nbr_obj'))));
            }

            $this->tpl->parse('Hdldir_pagination', 'dir_pagination');
        }
    }

    /**
     *  Get file pagination
     */
    function parseFilePagination() {

        global $obj, $url, $start;

        if ($this->cobj->prev) {
            $this->tpl->parse('Hdlfile_previous_page', 'file_previous_page', true);
            $this->tpl->set_var(array(
                    'OBJ_PREV'          =>  view_obj(($this->cobj->type == TYPE_ARCHIVED) ? $this->cobj->prev->target : $this->cobj->prev->name),
                    'PREV_PATH'         =>  $url->linkToObj(($this->cobj->type == TYPE_ARCHIVED) ? array($this->cobj->prev->file, $this->cobj->prev->target) : $this->cobj->prev->file),
                    'PREV_FILE_ICON'    =>  $this->cobj->prev->icon,
            ));

        }

        if ($this->cobj->next) {
            $this->tpl->parse('Hdlfile_next_page', 'file_next_page', true);
            $this->tpl->set_var(array(
                    'OBJ_NEXT'          =>  view_obj(($this->cobj->type == TYPE_ARCHIVED) ? $this->cobj->next->target : $this->cobj->next->name),
                    'NEXT_PATH'         =>  $url->linkToObj(($this->cobj->type == TYPE_ARCHIVED) ? array($this->cobj->next->file, $this->cobj->next->target) : $this->cobj->next->file),
                    'NEXT_FILE_ICON'    =>  $this->cobj->next->icon
            ));
        }

        $this->tpl->parse('Hdlfile_pagination', 'file_pagination', true);
    }
}

?>
