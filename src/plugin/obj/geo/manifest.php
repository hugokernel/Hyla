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
    M_NAME          =>  'Geo',
    M_DESCRIPTION   =>  'View poi on Google Maps',
    M_AUTHOR        =>  'Hugo',
    M_VERSION       =>  '1',
    M_MINVERSION    =>  '0.8.1',
    M_TARGET        =>  'file',
    M_EXTENSION     =>  'kml,ov2,asc,csv',

    M_CONF          =>  array(
        'max_poi'  =>  array(
            M_DESCRIPTION   =>  'Max numer of Poi to view on map', 
            M_DEFAULT       =>  50
        ),
        'google_key'  =>  array(
            M_DESCRIPTION   =>  'Google key to view map', 
            M_DEFAULT       =>  'ABQIAAAArke8Ra8V30WnuiYl2wLnhBT2yXp_ZAY8_ufC3CFXhHIE1NvwkxQ07958Edq9Z5EFy_t3w-DVWpNaTQ'
        ),
    )
);

?>
