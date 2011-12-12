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

$tpl->set_file('login', 'login.tpl');

if (isset($_POST['lg_name'])) {
	
	if (!empty($_POST['lg_name']) && !empty($_POST['lg_password'])) {

		if ($_POST['lg_name'] == LOGIN && $_POST['lg_password'] == PASSWORD) {
			$_SESSION['sess_name'] = $_POST['lg_name'];
			$_SESSION['sess_niv'] = NIV_5;
			redirect('', ($_SESSION['sess_page'] ? $_SESSION['sess_page'] : obj::getCurrentUrl()), traduct('authok'));
			$_SESSION['sess_page'] = null;
			exit;
		}
		$msg_error = traduct('autherror');
	} else
		$msg_error = traduct('emptyinput');
		
}

$tpl->set_var(array(
		'OBJECT'	=>	$cobj->file,
		'ERROR'		=>	view_error($msg_error),
		'NAME'		=>	@$_POST['name']
		));

$var_tpl = $tpl->parse('OutPut', 'login');

?>
