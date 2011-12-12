<?php
/*
	This file is part of iFile
	Copyright (c) 2004-2006 Charles Rincheval.
	All rights reserved

	iFile is free software; you can redistribute it and/or modify it
	under the terms of the GNU General Public License as published
	by the Free Software Foundation; either version 2 of the License,
	or (at your option) any later version.

	iFile is distributed in the hope that it will be useful, but
	WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with iFile; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

if (!defined('PAGE_HOME'))
	header('location: ../index.php');


$tpl->set_file(array(
		'search'	 	=>	'search.tpl'));
$tpl->set_block('search', array(
		'line'		=>	'Hdlline',
		'result'	=>	'Hdlresult'));

$tpl->set_file('search', 'search.tpl');

/*
if (preg_match ("/web/i", "PHP est le meilleur langage de script du webkk.")) {
    echo "Un mot a été trouvé.";
} else {
    echo "Un mot n'a pas été trouvé.";
}
exit;
*/


if (isset($_POST['word']) && !empty($_POST['word'])) {

	$scandir = (isset($_POST['scandir'])) ? true : false;
	$recurs = (isset($_POST['recurs'])) ? true : false;

	$tab = file::searchFile($cobj->path, $_POST['word'], $recurs, FOLDER_ROOT, $scandir);
	
	if ($tab) {
	
		$tab = $obj->getDirContent(null, null, 0, 0, $tab);
	
		$size = sizeof($tab);
		for($i = 0; $i < $size; $i++) {
//			$tpl->set_var('ACTION',	(is_file(FOLDER_ROOT.'/'.$tab[$i]['path'])) ? 'dl' : (($tab[$i]['type'] == 'gallery') ? 'gallery' : 'list'));
			$tpl->set_var(array(
					'FILE_ICON'			=>	$tab[$i]->icon,
					'FILE_NAME'			=>	$tab[$i]->name,
					'FILE_SIZE'			=>	($tab[$i]->type == TYPE_FILE) ? get_intelli_size($tab[$i]->size) : '&nbsp;',
					'PATH'				=>	$tab[$i]->path,
					'PATH_FORMAT'		=>	format($tab[$i]->file, false),
					'FILE_DESCRIPTION'	=>	string::cut(eregi_replace("<br />", " ", $tab[$i]->info->description), 90)));
			$tpl->parse('Hdlline', 'line', true);
		}
		
		$tpl->parse('Hdlresult', 'result', true);
		
	} else
		$tpl->set_var('ERROR', view_error(traduct('noresult')));
	
	$tpl->set_var(array(
			'SCANDIR_CHECKED'	=>	@$_POST['scandir'] ? 'checked="checked"' : null,
			'RECURS_CHECKED'	=>	@$_POST['recurs'] ? 'checked="checked"' : null,
			'WORD'				=>	$_POST['word']));
}

$tpl->set_var('OBJECT', $cobj->file);

$var_tpl = $tpl->parse('OutPut', 'search');

?>
