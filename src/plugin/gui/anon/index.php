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

class plugin_gui_anon extends plugin_gui {

    function plugin_gui_anon() {
       parent::plugin_gui();
    }
    
    function act() {
        switch ($this->url->getParam('aff', 3)) {
            #   Téléchargement
            case 'download':
                if (file::getRealFile($this->url->getParam('obj'), DIR_ROOT.DIR_ANON)) {
                    file::sendFile(DIR_ROOT.DIR_ANON.$this->url->getParam('obj'));
                 //   DBUG($this->url->getParam('obj'));
                    system::end();
                }
                break;

            #   Acceptation d'un fichier anonyme
            case 'accept':
                $ret = $lobj->acceptAnonFile($this->url->getParam('obj'), $this->obj->getRoot());
                if ($ret) {
                    $msg = __('File was moved in %s', $ret);
                }
                break;

            #   Suppression d'un fichier
            case 'del':
                $file = $lobj->getInfo($this->url->getParam('obj'), false, false);
                if ($file->type != TYPE_UNKNOW) {
                    $lobj->delete($file);
                }
        }
    }

    function aff() {

        global $url;
      
        $this->tpl->set_file('anon', 'tpl/anon.tpl');      
        $this->tpl->set_block('anon', array(
            'anon_move_dir_occ' =>  'Hdlanon_move_dir_occ',
            'anon_move'         =>  'Hdlanon_move',
            'anon_line'         =>  'Hdlanon_line',
            'anon_list'         =>  'Hdlanon_list',
        ));


//        $url->setContextSaving(false);
        $url->setContext(array('page', 'admin', 'anon'));

        $this->obj->setRoot(DIR_ROOT.DIR_ANON);
//        $cobj = $this->obj->getInfo($url->current->obj, true, true);

/*
        $plugin = plugins::get(PLUGIN_TYPE_OBJ);
        $out = plugin_obj::load($plugin);

        $out .= get_pagination();
*/

/*
$p = new run_obj(true, true, true);
$out = $p->run();
*/
        $p = new run_obj();
        $p->viewHeader(false);
        $p->viewTree(true);
        
//        dbug($obj->datasource);
//        exit;
        
        $p->setAction(array('remove' => __('Remove from basket')));
        $out = $p->run();
//        $url->setContext(null);

        $this->tpl->set_var('CONTENT', $out);

        return $this->tpl->parse('OutPut', 'anon');
/*
        $lobj->view_hidden_file = true;
        $tab = $lobj->getDirContent('/', SORT_DATE, 0, 10000, -1, array(array('=', 'type', TYPE_FILE), array('!', 'name', '.htaccess')));
        $wrap = $lobj->getAnonFile();
        $size = sizeof($tab);
        if ($size) {
            for ($i = 0, $cmpt = 0; $i < $size; $i++) {
                $path = (isset($wrap[$tab[$i]->file])) ? $wrap[$tab[$i]->file] : '/';
                $this->tpl->set_var(array(
                        'FILE_PATH'         =>  $path,
                        'FILE_ICON'         =>  $tab[$i]->icon,
                        'FILE_NAME'         =>  $tab[$i]->name,
                        'FILE_SIZE'         =>  get_human_size_reading($tab[$i]->size),
                        'FILE_DATE'         =>  format_date(filectime($tab[$i]->realpath), 1),
                        'FILE_DESCRIPTION'  =>  ($tab[$i]->info->description) ? $tab[$i]->info->description : __('No description !'),
                        'PATH_DOWNLOAD'     =>  $this->url->linkToPage(array('admin', 'anon', 'download'), $tab[$i]->file),
                        'ADMIN_ANON_DEL'    =>  $this->url->linkToPage(array('admin', 'anon', 'del'), $tab[$i]->file),
                        'ADMIN_ANON_MOVE'   =>  $this->url->linkToPage(array('admin', 'anon', 'accept'), $tab[$i]->file),
                        ));

                $this->tpl->parse('Hdlanon_line', 'anon_line', true);
                $cmpt++;
            }
            $this->tpl->parse('Hdlanon_list', 'anon_list', true);
        }
        
        $this->tpl->set_var('MSG', (!isset($cmpt) && !$msg) ? __('There are no file !') : $msg);
        
        unset($lobj);

        return $this->tpl->parse('OutPut', 'anon');
*/
    }
}

?>
