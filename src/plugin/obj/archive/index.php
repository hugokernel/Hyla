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

class plugin_obj_archive extends plugin_obj {

    var $zip;

    var $_act;
    var $_act_result_ok;
    var $_act_result_error;

    function plugin_obj_archive($cobj) {
        parent::plugin_obj($cobj);

        $this->_act = null;
        $this->_act_result_ok = null;
        $this->_act_result_error = null;
    }

    function act($act = null) {

        // Extrait dans le dossier parent
        if ($act == 'extract') {

            acl_test(ADMINISTRATOR_ONLY);

            $out = archive::extract($this->cobj->realpath, file::format($this->obj->getRoot().$this->cobj->path));

            $this->_act = $act;
            $this->_act_result = 0;

            foreach ($out as $occ) {
                if ($occ['status'] == 'ok') {
                    $this->_act_result_ok++;
                    @chmod($occ['filename'], $this->conf->get('file_chmod'));
                } else {
                    $this->_act_result_error++;
                }
            }
        }
    }

    function aff($paff) {

        global $cuser;

        $this->tpl->set_file('archive', 'archive.tpl');
        $this->tpl->set_block('archive', array(
            'zipfile'       =>  'Hdlzipfile',
            'act_extract'   =>  'Hdlact_extract',
        ));

        $this->addStyleSheet('default.css');

        $list = archive::listContent($this->cobj->realpath);
        if (!$list) {
            system::out(__('Plugin::Archive : Error'));
        }
        
        for ($size = 0, $i = 0; $i < sizeof($list); $i++) {
            $size += $list[$i]['size'];
            if ($list[$i]['folder']) {
                continue;
            }
                    
            $this->tpl->set_var(array(
                'FILE_ICON'     =>  get_icon(file::getExtension(basename($list[$i]['filename']))),
                'FILE_NAME'     =>  $list[$i]['filename'],
                'FILE_SIZE'     =>  get_human_size_reading($list[$i]['size']),
                'URL_FILE'      =>  $this->url->linkToObj(array($this->cobj->file, $list[$i]['filename'])),
                'URL_DOWNLOAD'  =>  $this->url->linkToObj(array($this->cobj->file, $list[$i]['filename']), 'download'),
                ));
            $this->tpl->parse('Hdlzipfile', 'zipfile', true);
        }

        $this->tpl->set_var(array(
            'URL_EXTRACT'       =>  $this->url->linkToCurrentObj(null, null, 'extract'),
            'RAPPORT'           =>  (($this->_act_result_ok) ? view_status(__('%s extracted files', $this->_act_result_ok)) : null).(($this->_act_result_error) ? view_error(__('%s error during extraction', $this->_act_result_error)) : null),
            'NBR_FILE'          =>  count($list),
            'COMPRESSED_SIZE'   =>  get_human_size_reading(filesize($this->cobj->realpath)),
            'REAL_SIZE'         =>  get_human_size_reading($size),
            'OBJECT'            =>  $this->url->linkToCurrentObj()
            ));

        // Affichage du lien pour l'extraction
        if (acl::ok(ADMINISTRATOR_ONLY)) {
            $this->tpl->parse('Hdlact_extract', 'act_extract', true);
        }
            
        return $this->tpl->parse('OutPut', 'archive');
    }
}

?>
