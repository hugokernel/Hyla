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
    M_NAME          =>  'Fs',
    M_DESCRIPTION   =>  'File system',
    M_AUTHOR        =>  'Hugo',
    M_VERSION       =>  '1',

    M_METHODS       =>  array(
        'rename'   =>  array(
                        M_METHOD    =>  'rename',
                        M_PARAMS    =>  array(
                                            array('path',       true, 'file|dir',   'Path'),
                                            array('new_name',   true, 'string',     'New name'),
                                        ),
                        M_RIGHTS    =>  array(
                                            'acl'   =>  AC_RENAME,
                                        ),
//                                'name'          =>  'Rename',
//                                'description'   =>  'Rename file or dir',
                     ),

        'copy'      =>  array(
                        M_METHOD    =>  'copy',
                        M_PARAMS    =>  array(
                                            array('file',           true, 'file|dir',   'Path'),
                                            array('destination',    true, 'dir',        'Destination path'),
                                        ),
                        M_RIGHTS    =>  array(
                                            'acl'   =>  AC_COPY,
                                        ),
//                                'name'          =>  'Copy',
//                                'description'   =>  'Copy file or dir',
                     ),

        'move'      =>  array(
                        M_METHOD    =>  'move',
                        M_PARAMS    =>  array(
                                            array('file',           true, 'file|dir',   'Path'),
                                            array('destination',    true, 'dir',        'Destination path'),
                                        ),
                        M_RIGHTS    =>  array(
                                            'acl'   =>  AC_MOVE,
                                        ),
                     ),

        'delete'    =>  array(
                        M_METHOD    =>  'delete',
                        M_PARAMS    =>  array(
                                            array('file',           true, 'file|dir',   'Path'),
                                        ),
                        M_RIGHTS    =>  array(
//                                            'acl'   =>  AC_DELETE,
                                        ),
//                                'name'          =>  'Delete',
//                                'description'   =>  'Delete file or dir',
                     ),
    )
);

?>
