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

if (!defined('PAGE_HOME'))
    header('location: ../index.php');

$tpl->set_file('info', 'info.tpl');
$tpl->set_block('info', array(
        'aff_size'      =>  'Hdlaff_size',
        'aff_mime'      =>  'Hdlaff_mime',
        'aff_md5'       =>  'Hdlaff_md5',
        'aff_gen_file'  =>  'Hdlaff_gen_file',
        'aff_file'      =>  'Hdlaff_file',
        'aff_dir'       =>  'Hdlaff_dir',
        'aff_download_type' =>  'Hdlaff_download_type',
        ));

$tpl->l10n->setFile('info.php');

$tab_type = array(
        TYPE_DIR        =>  __('Dir'),
        TYPE_FILE       =>  __('File'),
        TYPE_ARCHIVED   =>  __('Archive'),
        TYPE_UNKNOW     =>  __('Unknow'),
        );

$size = null;

switch ($cobj->type) {

    case TYPE_DIR:
        $name = $cobj->name;

        if (acl::ok(AC_VIEW)) {
            $ret = file::getDirSize($cobj->realpath, false);
            $size = __('%s, %s file', get_human_size_reading($ret['size']), $ret['nbr']);
        }

        $tpl->parse('Hdlaff_dir', 'aff_dir', true);
        break;

    case TYPE_ARCHIVED:
        $name = $cobj->target;
        $size = 0;

        $ok = false;

        if (!cache::getFilePath($cobj->file, $file)) {
            archive::extract($cobj->realpath, $file);
        }

//        $filepath = file::dirName($_SERVER['SCRIPT_FILENAME']).'/'.$file.'/'.$cobj->target;
        $filepath = get_real_directory();
        if (!file_exists($filepath)) {
            header('HTTP/1.x 404 Not Found');
            redirect(__('Error'), $url->linkToObj($cobj->file), __('Object not found !'));
            system::end();
        }

        $size = get_human_size_reading(filesize($filepath));

        // La fonction md5_file existe ?
        if (function_exists('md5_file')) {
            $tpl->set_var(array(
                    'MD5'               =>  ($url->getParam('aff', 2) == 'md5') ? ' : '.md5_file($filepath) : null,
                    'URL_MD5_CALCULATE' =>  $url->linkToCurrentObj(array('info', 'md5')),
                    ));
            $tpl->parse('Hdlaff_md5', 'aff_md5', true);
            $ok = true;
        }

        // La fonction mime_content_type existe ?
        if (function_exists('mime_content_type')) {
            $tpl->set_var('MIME', mime_content_type($filepath));
            $tpl->parse('Hdlaff_mime', 'aff_mime', true);
            $ok = true;
        }

        if ($ok) {
            $tpl->parse('Hdlaff_gen_file', 'aff_gen_file', true);
        }

        $tpl->parse('Hdlaff_file', 'aff_file', true);
        break;

    case TYPE_FILE:

        $size = get_human_size_reading($cobj->size);
        $name = $cobj->name;

        $tpl->parse('Hdlaff_file', 'aff_file', true);

        // La fonction md5_file existe ?
        if (function_exists('md5_file')) {
            $tpl->set_var(array(
                    'MD5'               =>  ($url->getParam('aff', 2) == 'md5') ? ' : '.md5_file($cobj->realpath) : null,
                    'URL_MD5_CALCULATE' =>  $url->linkToCurrentObj(array('info', 'md5')),
                    ));
            $tpl->parse('Hdlaff_md5', 'aff_md5', true);
        }

        // La fonction mime_content_type existe ?
        if (function_exists('mime_content_type')) {
            $tpl->set_var('MIME', mime_content_type($cobj->realpath));
            $tpl->parse('Hdlaff_mime', 'aff_mime', true);
        }

        $tpl->parse('Hdlaff_gen_file', 'aff_gen_file', true);
        break;

    default:
        system::end('Error !');
        break;
}

if ($size) {
    $tpl->set_var('SIZE', $size);
    $tpl->parse('Hdlaff_size', 'aff_size', true);
}

// Format
foreach (archive::getAllType() as $type) {
    $tpl->set_var(array(
                        'URL_DOWNLOAD_TYPE'  =>  $url->linkToCurrentObj(array('download', 'archive', $type)),
                        'DOWNLOAD_TYPE_NAME' =>  ucfirst($type),
                        ));
    $tpl->parse('Hdlaff_download_type', 'aff_download_type', true);
}

$tpl->set_var(array(
            'NAME'                  =>  $name,
            'TYPE'                  =>  $tab_type[$cobj->type],
            'ICON'                  =>  $cobj->icon,
            'OBJECT'                =>  $cobj->file,
            
            'MSG_INFO'              =>  $obj->getUserRights4Path($cobj->file, ANONYMOUS_ID) ? null : view_status(__('Anonymous user do not have the rights to reach this resource !')),

            'URL_DOWNLOAD_DIRECT'   =>  $url->linkToCurrentObj(array('download')),

            'URL_RSS_FILE_COMMENT'  =>  $url->linkToCurrentObj(array('rss', 'comment')),

            'URL_RSS_DIR_OBJECT'    =>  $url->linkToObj($cobj->path, array('rss')),
            'URL_RSS_DIR_COMMENT'   =>  $url->linkToObj($cobj->path, array('rss', 'comment')),

            'URL_OBJ_EXPORT_JS'     =>  $url->linkToCurrentObj(array('export', 'content')),
            'URL_OBJ_EXPORT_IFRAME' =>  $url->linkToCurrentObj(array('export', 'page', 'all')),
            ));

$var_tpl .= $tpl->parse('OutPut', 'info');

?>
