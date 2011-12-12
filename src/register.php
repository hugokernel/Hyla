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

$tpl->set_file('register', 'register.tpl');

$tpl->l10n->setFile('register.php');

// Le formulaire à été posté ?
if (isset($_POST['reg_name'])) {

    $usr = new users();
    $ret = $usr->testLogin($_POST['reg_name']);
    if ($ret == -1) {
        $msg_error = __('The name is invalid !');
        $_POST['reg_name'] = null;
    } else if (!$ret) {
        $msg_error =__('An user or a group of this name already exists !');
        $_POST['reg_name'] = null;
    } else if (empty($_POST['reg_password']) || empty($_POST['reg_password_confirm'])) {
        $msg_error = __('All the fields must be filled');
    } else if (strlen($_POST['reg_password']) < MIN_PASSWORD_SIZE) {
        $msg_error = __('Password must have at least %s characters !', MIN_PASSWORD_SIZE);
    } else if ($_POST['reg_password'] != $_POST['reg_password_confirm']) {
        $msg_error = __('Passwords are different');
    } else {
        $id = $usr->addUser($_POST['reg_name'], $_POST['reg_password']);

        if ($id) {
            // Envoi de courriel ?
            if ($conf['send_mail'] && $conf['webmaster_mail']) {
                system::mail($conf['webmaster_mail'], __('Hyla - A new user has been registered !'), __('register_mail_content', $_POST['reg_name']), $conf['webmaster_mail']);
            }

            redirect(__('Error'), $url->linkToPage('login'), __('Account correctly created !'));
            system::end();
            exit;
        }
    }
}

$tpl->set_var(array(
        'STYLESHEET'    =>  get_css(),
        'PAGE_REGISTER' =>  $url->linkToPage('register'),
        'NAME'          =>  (isset($_POST['reg_name']) ? stripslashes(htmlentities($_POST['reg_name'])) : null),
        'OBJECT'        =>  $cobj->file,
        'ERROR'         =>  view_error($msg_error),
        ));

$msg_error = null;

$var_tpl = $tpl->parse('OutPut', 'register');

print($var_tpl);
system::end();

?>
