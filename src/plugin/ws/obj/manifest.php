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
    M_NAME          =>  'Obj',
    M_DESCRIPTION   =>  'Action on object',
    M_AUTHOR        =>  'Hugo',
    M_VERSION       =>  '1',

    M_METHODS       =>  array(
        'getDescription' => array(
                            M_METHOD    =>  'getDescription',
                            M_PARAMS     =>  array(
                                                array('file',           true,   'file|dir', 'File'),
                                                array('formated',       false,  'boolean',  'Return data formated or not'),
                                            ),
                            M_RIGHTS    =>  array(
                                            ),
                        ),
        'setDescription' => array(
                            M_METHOD    =>  'setDescription',
                            M_PARAMS    =>  array(
                                                array('file',           true,   'file|dir', 'File'),
                                                array('description',    true,   'string',   'Description'),
                                            ),
                            M_RIGHTS    =>  array(
                                                'acl'   =>  AC_EDIT_DESCRIPTION,
                                            ),
                        ),
        'setIcon' =>    array(
                            M_METHOD    =>  'setIcon',
                            M_PARAMS    =>  array(
                                                array('file',   true,   'file|dir', 'File'),
                                                array('icon',   true,   'string',   'Icon'),
                                            ),
                            M_RIGHTS     =>  array(
                                                'acl'   =>  AC_EDIT_ICON,
                                           ),
                        ),
        'setPlugin' =>  array(
                            M_METHOD    =>  'setPlugin',
                            M_PARAMS    =>  array(
                                                array('file',   true,   'file|dir', 'File'),
                                                array('plugin', true,   'string',   'Plugin'),
                                            ),
                            M_RIGHTS    =>  array(
                                                'acl'   =>  AC_EDIT_PLUGIN,
                                            ),
                        ),
    )
);

?>
