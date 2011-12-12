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

$tpl->set_file('admin', 'admin.tpl');

$tpl->set_block('admin', array(
		'chk-add-comment'	=>	'Hdlchk-add-comment',
		'chk-edit-file'		=>	'Hdlchk-edit-file',
		'chk-add-file'		=>	'Hdlchk-add-file',
		'chk-del-file'		=>	'Hdlchk-del-file',
		'user'				=>	'Hdluser'
		));

//$xml = simplexml_load_file(FILE_XML_USER);
$xml = new XPath(FILE_XML_USER);

switch (@$_GET['action']) {
	case 'adduser':
		
		$val = verif_value(array(
				$_POST['password']	=>	traduct('emptypassword'),
				$_POST['name']		=>	traduct('emptyname')), $msg_error);
		
		if ($_POST['name'] == ROOT || !$val)
			break;
		
		//$res = $xml->xpath('user[@name=\''.$_POST['name'].'\']');
		$res = $xml->match('/list/user[@name=\''.$_POST['name'].'\']');
		
		$niv = 0;
		if (@$_POST['add_comment'])
			$niv += 1;
		if (@$_POST['edit_file'])
			$niv += 2;
		if (@$_POST['add_file'])
			$niv += 4;
		if (@$_POST['del_file'])
			$niv += 8;
		
		if (!$res) {

			$xml->insertChild('/list/*[last()]', '		
			<user name="'.$_POST['name'].'">
				<password>'.crypt($_POST['password'], CRYPT_SALT).'</password>
				<niv>'.$niv.'</niv>
			</user>', false);
			$xml->exportToFile(FILE_XML_USER);
			
		} else
			$msg_error = traduct('useralreadyexist');
			
		break;
	
	case 'deluser':
		$xml->removeChild('/list/user[@name=\''.$_GET['name'].'\']');
		$xml->exportToFile(FILE_XML_USER);
		break;
	
	case 'edituser':
	/*
		$tab = array();
		
		print_r($_POST);
		
		foreach ($_POST as $k => $p) {
			@list($name, $cmd) = @explode(':', $k);
			$tab[$name] = 0;
			if ($name && $cmd) {
				
				echo $name,' - ',$cmd,'<br>';
				
				$tab[$name] += $cmd;
			}
		}
		echo '<hr>';
		print_r($tab);
		
		foreach ($tab as $k => $p) {
			$res = $xml->xpath('user[@name=\''.$k.'\']');
			$res[0]->niv = $p;
			file_put_contents(FILE_XML_USER, $xml->asXML());
		}
	*/
		break;
	
	// Une p'tite synchro
	case 'synch':
		
		$xmlf = new XPath(FILE_XML);
		$res = $xmlf->match('/list/*');
		
		$rapport = null;
		$tab_del = array();
		foreach ($res as $occ) {
			$name = $xmlf->getAttributes($occ, 'name');
			if ($name) {				
				if (!file_exists(FOLDER_ROOT.$name)) {
					// Obligé de faire comme ça, à priori, bug dans la classe XPath
					$tab_del[] = '/list/object[@name=\''.$name.'\']';
					$rapport .= $name.' ';
				}
			}
		}
		
		if ($tab_del) {
			foreach ($tab_del as $occ) {
				$xmlf->removeChild($occ, true);
			}
			$xmlf->exportToFile(FILE_XML);
			$rapport = sprintf(traduct('synchresult'), $rapport);
		} else {
			$rapport = traduct('synchnoresult');	
		}
		
		$tpl->set_var('RAPPORT', $rapport);
		unset($xmlf);
		
		break;
}


// Affichage des user
$res = $xml->match('/list/*');

foreach ($res as $occ) {
	$name = $xml->getAttributes($occ, 'name');
	$niv = $xml->getData('/list/user[@name=\''.$name.'\']/niv');

	if ($name == ROOT)
		continue;
	
	$tpl->set_var('Hdlchk-add-comment');
	$tpl->set_var('Hdlchk-edit-file');
	$tpl->set_var('Hdlchk-add-file');
	$tpl->set_var('Hdlchk-del-file');


	if ($niv & ADD_COMMENT)
		$tpl->parse('Hdlchk-add-comment', 'chk-add-comment', true);
	if ($niv & EDIT_FILE)
		$tpl->parse('Hdlchk-edit-file', 'chk-edit-file', true);
	if ($niv & ADD_FILE)
		$tpl->parse('Hdlchk-add-file', 'chk-add-file', true);
	if ($niv & DEL_FILE)
		$tpl->parse('Hdlchk-del-file', 'chk-del-file', true);

	$tpl->set_var(array(
			'USER_NAME'	=>	$name,
			'USER_NIV'	=>	$niv
			));

	$tpl->parse('Hdluser', 'user', true);
}

$tpl->set_var(array(
		'NAME'		=>	@$_POST['name'],
		'ERROR'		=>	$msg_error));

$var_tpl = $tpl->parse('OutPut', 'admin');

?>
