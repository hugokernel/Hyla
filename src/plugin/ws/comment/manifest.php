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
    M_NAME          =>  'Comment',
    M_DESCRIPTION   =>  'Action on comments',
    M_AUTHOR        =>  'Hugo',
    M_VERSION       =>  '1',

    M_METHODS       =>  array(
        'add'    =>  array(
                        M_METHOD    =>  'add',
                        M_PARAMS    =>  array(
                                            array('file',       true,   'file|dir', 'File'),
                                            array('author',     true,   'string',   'Author'),
                                            array('email',      true,   'string',   'Email'),
                                            array('content',    true,   'string',   'Content'),
                                        ),
                        M_RIGHTS    =>  array(
                                        ),
                    ),
        'del'    => array(
                        M_METHOD    =>  'del',
                        M_PARAMS    =>  array(
                                            array('id', true,   'int|array',    'Id'),
                                        ),
                        M_RIGHTS    =>  array(
                                        ),
                    ),
        'getlast'   => array(
                        M_METHOD    =>  'getLast',
                        M_PARAMS    =>  array(
                                        ),
                    ),
    )
);

?>
