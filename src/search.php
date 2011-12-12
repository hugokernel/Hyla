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

$tpl->set_file('search', 'search.tpl');
$tpl->set_block('search', array(
        'line'      =>  'Hdlline',
        'result'    =>  'Hdlresult',
        ));

$l10n->setFile('search.php');

if (isset($_POST['word']) && !empty($_POST['word'])) {

    $scandir = (isset($_POST['scandir'])) ? true : false;
    $recurs = (isset($_POST['recurs'])) ? true : false;

    $tab = file::searchFile($cobj->path, $_POST['word'], $recurs, FOLDER_ROOT, $scandir, $conf['view_hidden_file']);
    $tab = $obj->getDirContent(null, null, 0, 10000, $tab);

    if ($tab) {
        $size = sizeof($tab);
        for($i = 0; $i < $size; $i++) {
            $tpl->set_var(array(
                    'PATH_INFO'         =>  $url->linkToObj($tab[$i]->file),
                    'FILE_ICON'         =>  $tab[$i]->icon,
                    'FILE_NAME'         =>  $tab[$i]->name,
                    'FILE_SIZE'         =>  ($tab[$i]->type == TYPE_FILE) ? get_human_size_reading($tab[$i]->size) : '&nbsp;',
                    'PATH'              =>  $tab[$i]->path,
                    'PATH_FORMAT'       =>  format($tab[$i]->file, false),
                    'FILE_DESCRIPTION'  =>  string::cut(eregi_replace("<br />", " ", $tab[$i]->info->description), 90)));
            $tpl->parse('Hdlline', 'line', true);
        }

        $tpl->parse('Hdlresult', 'result', true);
    } else
        $tpl->set_var('ERROR', view_error(__('No result')));

    $tpl->set_var(array(
            'SCANDIR_CHECKED'   =>  isset($_POST['scandir']) ? 'checked="checked"' : null,
            'RECURS_CHECKED'    =>  isset($_POST['recurs']) ? 'checked="checked"' : null,
            'WORD'              =>  strip_tags($_POST['word']),
            ));

    $l10n->setStr('search.php', 'Search results for &laquo; %s &raquo; :', strip_tags($_POST['word']));
}

$tpl->set_var('FORM_SEARCH', $url->linkToCurrentObj('search'));

$l10n->setStr('search.php', 'Search in &laquo; %s &raquo;', $cobj->path);

$var_tpl = $tpl->parse('OutPut', 'search');

$var_tpl = $l10n->parse($var_tpl, 'search.php');

?>
