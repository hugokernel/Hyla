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
    M_NAME          =>  'Basket',
    M_DESCRIPTION   =>  'Action on basket',
    M_AUTHOR        =>  'Hugo',
    M_VERSION       =>  '1',

    M_METHODS       =>  array(
        'add' =>  array(
                        M_METHOD    =>  'add',
                        M_PARAMS    =>  array(
                                            array('obj',    true, 'file|dir',   'Object'),
                                        ),
                     ),
        'remove' =>  array(
                        M_METHOD    =>  'remove',
                        M_PARAMS    =>  array(
                                            array('obj',    true, 'file|dir',   'Object'),
                                        ),
                        M_RIGHTS    =>  array(
                                        ),
                     ),
        'getList' =>  array(
                        M_METHOD    =>  'getList',
                        M_RIGHTS    =>  array(
                                        ),
                     ),
        'clear' =>  array(
                        M_METHOD    =>  'clear',
                        M_RIGHTS    =>  array(
                                        ),
                     ),
        'getCount' =>  array(
                        M_METHOD    =>  'getCount',
                        M_RIGHTS    =>  array(
                                        ),
                     ),
    )
);

?>
