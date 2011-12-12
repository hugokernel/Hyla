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
    M_NAME          =>  'Misc',
    M_DESCRIPTION   =>  'Miscallenous',
    M_AUTHOR        =>  'Hugo',
    M_VERSION       =>  '1',

    M_METHODS       =>  array(
        'getIcon'   =>  array(
                        M_METHOD    =>  'getIcon',
                        M_PARAMS    =>  array(
                                        ),
                        M_RIGHTS    =>  array(
                                            'user'  =>  ANY_ID,
                                        ),
                        ),
        'getGui'   =>  array(
                        M_METHOD    =>  'getGui',
                        M_PARAMS    =>  array(
                                            array('name',   true,   'string',   'Gui name'),
                                        ),
                        M_RIGHTS    =>  array(
                                            'user'  =>  ANY_ID,
                                        ),
                        ),
        'getPluginContent'   =>  array(
                        M_METHOD    =>  'getPluginContent',
                        M_PARAMS    =>  array(
                                            array('file',   true,   'file|dir', 'File'),
                                            array('tree',   false,  'int',      'View tree or not (0 : tree, 1 : no tree, 2 : auto'),
                                        ),
                        M_RIGHTS    =>  array(
                                            'user'  =>  ANY_ID,
                                        ),
                        ),
    )
);

?>
