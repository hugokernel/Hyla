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

require_once HYLA_ROOT_PATH.'src/inc/cache.class.php';

class plugin_gui_download extends plugin_gui {

    function plugin_gui_download() {
        parent::plugin_gui();
    }

    function act() {

        $cobj = $this->obj->getCurrentObj();

        $status = false;

        // Archive object ?
        /*
        $type = 'tar';
        if ($url->getParam('aff', 2) == 'archive') {
            if (archive::isValidType($url->getParam('aff', 3))) {
                $type = $url->getParam('aff', 3);
            }
        }
        */

        switch ($cobj->type) {

           //  On extrait le fichier et on l'envoie
            case TYPE_ARCHIVED:
                $file = null;

                // Extract file if not already extracted
                if (!cache::getFilePath($cobj->file, $file)) {
                    archive::extract($cobj->realpath, $file);
                }
//                echo $file.' - '.get_real_directory();

                // Si le fichier n'est pas trouvé dans l'archive : Erreur !
                if (!file_exists(get_real_directory())) {
                    header('HTTP/1.x 404 Not Found');
                    redirect(__('Error'), $this->url->linkToObj($cobj->file), __('Object not found !'));
                    break;
/*                    $this->events['onerror'] =    array(  __('Object not found !'),
                                                            'redirect'  =>  'last'
                                                    );
                    return false;
                    */
                }

                $file .= '/'.$cobj->target;
                // Break not present intentionally

            //  On envoie simplement le fichier
            case TYPE_FILE:
            /*
                if ($url->getParam('aff', 2) == 'archive') {
                    if ($cobj->type == TYPE_FILE) {
                        if (!cache::getArchivePath($cobj->file, $file, $type)) {
                            archive::createFromFile($file, $cobj->realpath, $type);
                        }
                    } else {
                        // ToDo: put in cache first result of createFromFile
                        archive::createFromFile($file.'.'.$type, $file, $type);
                        $file .= '.'.$type;
                    }
                } else {
                    $file = get_real_directory();
                }
                */

                file::sendFile($file);
                $status = true;
                break;

            //  On archive le dossier et on l'envoie
            case TYPE_DIR:

                redirect(__('Error'), $this->url->linkToObj($cobj->file), 'Pas encore implémenté !');

                // Si la configuration l'autorise
                /*
                if ($conf->get('download_dir')) {
                    $file = null;
                    if (!cache::getArchivePath($cobj->file, $file, $type)) {
                        $file = file::dirName($_SERVER['SCRIPT_FILENAME']).'/'.$file;
                        $out = archive::createFromDir($file, $cobj->realpath, $type);
                        if (!$out) {
                            redirect(__('Error'), file::downPath($url->linkToObj($cobj->path)), __('Dir is probably empty or not readable !'));
                            break;
                        }
                    }

                    file::sendFile($file);
                    if ($conf->get('download_dir_max_filesize') && $conf->get('download_dir_max_filesize') < filesize($file)) {
                        unlink($file);
                    }

                    $status = true;
                } else {
                    redirect(__('Error'), $url->linkToObj(file::downPath($cobj->file)), __('This functionality is disabled !'));
                }
                */
                break;
        }
/*
        if ($conf->get('download_counter') && $status) {
            $obj->addDownload();
        }
*/
        system::end();
    }

    function aff() {
        return null;
    }
}

?>
