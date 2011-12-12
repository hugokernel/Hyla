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
    M_NAME          =>  'Acl',
    M_DESCRIPTION   =>  'Access list',
    M_AUTHOR        =>  'Hugo',
    M_VERSION       =>  '1',

    M_METHODS       =>  array(
        'add'   =>  array(
                        M_METHOD    =>  'add',
                        M_PARAMS    =>  array(
                                            array('path',       true, 'path',   'Path'),
                                            array('user_id',    true, 'int',    'User id'),
                                            array('right',      true, 'int',    'Right'),
                                        ),
                        M_RIGHTS    =>  array(
                                            'user'  =>  ADMINISTRATOR_ONLY,
                                        ),
                     ),
        'edit'  =>  array(
                        M_METHOD    =>  'edit',
                        M_PARAMS    =>  array(
                                            array('path',       true, 'path',   'Path'),
                                            array('user_id',    true, 'int',    'User id'),
                                            array('right',      true, 'int',    'Right'),
                                        ),
                        M_RIGHTS    =>  array(
                                            'user'  =>  ADMINISTRATOR_ONLY,
                                        ),
                    ),
        'del'   =>  array(
                        M_METHOD    =>  'del',
                        M_PARAMS    =>  array(
                                            array('path',       true, 'path',   'Path'),
                                            array('user_id',    true, 'int',    'User id'),
                                        ),
                        M_RIGHTS    =>  array(
                                            'user'  =>  ADMINISTRATOR_ONLY,
                                        ),
                    ),
    )
);

?>
