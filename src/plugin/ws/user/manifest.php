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

$manifest = array(
    M_NAME          =>  'User',
    M_DESCRIPTION   =>  'User action',
    M_AUTHOR        =>  'Hugo',
    M_VERSION       =>  '1',

    M_METHODS       =>  array(

        'setPassword' =>  array(
                        M_METHOD    =>  'setPassword',
                        M_PARAMS    =>  array(
                                            array('password',   true,   'string',   'Password'),
                                            array('user_id',    false,  'int',      'User id'),
                                            array('token',      false,  'string',   'Token (lost password)'),
                                        ),
                        M_RIGHTS    =>  array(
                                            'user'  =>  AUTHENTICATED_ID,
                                        ),
                     ),
        'setPasswordWithToken' =>  array(
                        M_METHOD    =>  'setPasswordWithToken',
                        M_PARAMS    =>  array(
                                            array('username',   true,   'string',   'User id'),
                                            array('token',      true,   'string',   'Token (lost password)'),
                                            array('password',   true,   'string',   'Password'),
                                        ),
                        M_RIGHTS    =>  array(
                                            'user'  =>  ANONYMOUS_ID,
                                        ),
                     ),
        'setEmail' =>  array(
                        M_METHOD    =>  'setEmail',
                        M_PARAMS    =>  array(
                                            array('email',   true,   'string',   'New email'),
                                            array('user_id', false,  'int',      'User id'),
                                        ),
                        M_RIGHTS    =>  array(
                                            'user'  =>  AUTHENTICATED_ID,
                                        ),
                     ),
        'setType' =>  array(
                        M_METHOD    =>  'setType',
                        M_PARAMS    =>  array(
                                            array('user_id',    false,  'int',      'User id'),
                                            array('type',       true,   'int',      'Type'),
                                        ),
                        M_RIGHTS    =>  array(
                                            'user'  =>  AUTHENTICATED_ID,
                                        ),
                     ),                                     
        'auth' => array(
                        M_METHOD    =>  'auth',
                        M_PARAMS    =>  array(
                                            array('username',   true,   'string',   'Username'),
                                            array('password',   true,   'string',   'Password'),
                                        ),
                        M_RIGHTS    =>  array(
                                            'user'  =>  ANY_ID,
                                        ),
                     ),
        'logout' => array(
                        M_METHOD    =>  'logOut',
                        M_PARAMS    =>  array(
                                        ),
                        M_RIGHTS    =>  array(
                                            'user'  =>  AUTHENTICATED_ID,
                                        ),
                     ),
        'add'   => array(
                        M_METHOD    =>  'add',
                        M_PARAMS    =>  array(
                                            array('username',   true,   'string',   'Username'),
                                            array('email',      true,   'string',   'Email'),
                                            array('password',   false,  'string',   'Password'),
                                        ),
                        M_RIGHTS    =>  array(
                                            'user'  =>  ANY_ID,
                                        ),
                     ),
        'createLostPasswordToken'   => array(
                        M_METHOD    =>  'sendLostPassRequest',
                        M_PARAMS    =>  array(
                                            array('username',   true,   'string',   'Username'),
                                            array('email',      true,   'string',   'Email'),
                                        ),
                        M_RIGHTS    =>  array(
                                            'user'  =>  ANONYMOUS_ID,
                                        ),
                     ),
        'testLostPasswordToken'   => array(
                        M_METHOD    =>  'sendLostPassRequest',
                        M_PARAMS    =>  array(
                                            array('username',   true,   'string',   'Username'),
                                            array('token',      true,   'string',   'Token'),
                                        ),
                        M_RIGHTS    =>  array(
                                            'user'  =>  ANONYMOUS_ID,
                                        ),
                     ),
    )
);

?>
