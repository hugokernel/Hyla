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
    WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Hyla; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

if (!defined('PAGE_HOME'))
    header('location: ../index.php');

$tpl->set_file('user', 'user.tpl');

$tpl->l10n->setFile('user.php');

$tpl->set_var(array(
            'MSG'                           =>  isset($msg) ? $msg : null,
            'FORM_USER_CHANGER_PASSWORD'    =>  $url->linkToPage(array('user', 'password'), null, 'changepassword'),
            ));

$var_tpl .= $tpl->parse('OutPut', 'user');

?>
