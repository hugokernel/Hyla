<?php
/*
	This file is part of Hyla
	Copyright (c) 2004-2006 Charles Rincheval.
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

$tpl->set_file('login', 'login.tpl');

if (isset($_POST['lg_name'])) {
	if (!empty($_POST['lg_name']) && !empty($_POST['lg_password'])) {
		$usr = new users();
		$res = $usr->auth($_POST['lg_name'], $_POST['lg_password']);
		if ($res) {
			redirect('', (isset($_SESSION['sess_url']) ? $_SESSION['sess_url'] : url::getObj($cobj->file)), __('You are now authenticated !'));
			$_SESSION['sess_url'] = null;
			$_SESSION['sess_cuser'] = serialize($res);
			system::end();
		}
		unset($usr, $res);
		header('HTTP/1.x 401 Authorization Required');
		$msg_error = __('Error during authentification');
	} else
		$msg_error = __('All the fields must be filled');
}

$tpl->set_var(array(
		'PAGE_LOGIN'	=>	url::getCurrentObj('login'),
		'NAME'			=>	(isset($_POST['lg_name']) ? stripslashes(htmlentities($_POST['lg_name'])) : null),
		'OBJECT'		=>	$cobj->file,
		'ERROR'			=>	view_error($msg_error),
		));

$msg_error = null;

$var_tpl = $tpl->parse('OutPut', 'login');

?>
