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
    WITHOUT ANY WARRANTY; without even the implied warcranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Hyla; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

if (!defined('PAGE_HOME'))
    header('location: ../index.php');

$tpl->set_file('action', 'action.tpl');

$tpl->set_block('action', array(
        'dir_copy_occ'  =>  'Hdldir_copy_occ',
        'copy'          =>  'Hdlcopy',

        'dir_move_occ'          =>  'Hdldir_move_occ',
        'move'                  =>  'Hdlmove',

        'mkdir_redirect_edit'   =>  'Hdlmkdir_redirect_edit',
        'mkdir'                 =>  'Hdlmkdir',

        'rename'                =>  'Hdlrename',
        ));

switch ($aff) {

    case 'mkdir':

        if (acl::ok(AC_EDIT_DESCRIPTION, AC_EDIT_PLUGIN)) {
            $tpl->parse('Hdlmkdir_redirect_edit', 'mkdir_redirect_edit', true);
        }

        $tpl->set_var(array(
                'PARENT_DIR'    =>  $cobj->path,
                'FORM_MKDIR'    =>  $url->linkToCurrentObj('', 'mkdir'),
                'ERROR'         =>  (isset($msg_error)) ? view_error($msg_error) : null,
                ));

        $tpl->parse('Hdlmkdir', 'mkdir', true);
        break;

    case 'move':

        if ($cobj->type == TYPE_ARCHIVED) {
            redirect(__('Error'), $url->linkToCurrentObj(), __('Not implemented !'));
            system::end();
        }

        if ($cobj->file == '/') {
            redirect(__('Error'), $url->linkToCurrentObj(), __('Impossible to move the root'));
            system::end();
        }

        if (!is_writable($cobj->realpath))
            $msg_error = __('You do not have the rights sufficient to move this object !');

    case 'copy':
        $tab = $obj->scanDir(FOLDER_ROOT, $conf['view_hidden_file']);
        asort($tab);

        $tpl->set_var('FORM_ACTION', $url->linkToCurrentObj('', $aff));

        // La racine
        if ($cobj->type == TYPE_ARCHIVED || ($cobj->path != '/' && file::downPath($cobj->file) != '/')) {
            // On vérifie les droits sur la racine
            $right = $obj->getCUserRights4Path('/');
            if (($aff == 'move' && ($right & AC_MOVE)) || ($aff == 'copy' && ($right & AC_COPY))) {
                $tpl->set_var('DIR_NAME', '/');
                $tpl->parse('Hdldir_'.$aff.'_occ', 'dir_'.$aff.'_occ', true);
            }
        }

        // Les autres répertoires
        foreach ($tab as $occ) {

            // Si l'utilisateur n'a pas le droit
            $right = $obj->getCUserRights4Path($occ);
            if (($aff == 'move' && !($right & AC_MOVE)) || ($aff == 'copy' && !($right & AC_COPY)))
                continue;

            // Si le dossier commence par un .
            if ($occ{1} == '.' && !$conf['view_hidden_file'])
                continue;

            // On ne peut pas déplacer un dossier dans son parent
            if ($aff == 'move' && $cobj->type == TYPE_DIR && file::isInPath($occ, $cobj->path))
                continue;

            // On ne peut pas copier ou déplacer sur soi même
            if ($cobj->type != TYPE_ARCHIVED && $occ == $cobj->path)
                continue;

            // On ne peut pas déplacer ou copier un dossier dans son parent
            if ($cobj->type == TYPE_DIR && $occ == file::downPath($cobj->path))
                continue;

            $tpl->set_var('DIR_NAME', view_obj($occ));
            $tpl->parse('Hdldir_'.$aff.'_occ', 'dir_'.$aff.'_occ', true);
        }
        unset($tab);

        $tpl->set_var('ERROR', (isset($msg_error)) ? view_error($msg_error) : null);
        $tpl->parse('Hdl'.$aff, $aff, true);
        break;

    case 'rename':

        // Le Renommage d'un fichier archivé n'est pas possible
        if ($cobj->type == TYPE_ARCHIVED) {
            redirect($cobj->file, $url->linkToCurrentObj(), __('Not implemented !'));
            system::end();
        }

        // On ne peut pas renommer la racine
        if ($cobj->file == '/') {
            redirect(__('Error'), $url->linkToCurrentObj(), __('Impossible to rename the root'));
            system::end();
        }

        $tpl->set_var(array(
                'CURRENT_NAME'  =>  view_obj($cobj->name),
                'FORM_RENAME'   =>  $url->linkToCurrentObj('', 'rename'),
                'ERROR'         =>  (isset($msg_error)) ? view_error($msg_error) : null,
                ));
        $tpl->parse('Hdlrename', 'rename', true);
        break;
}

$tpl->set_var('OBJECT', $cobj->file);

$msg_error = null;

$var_tpl = $tpl->parse('OutPut', 'action');

?>
