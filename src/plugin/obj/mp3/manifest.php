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
    M_NAME          =>  'Mp3',
    M_DESCRIPTION   =>  'Play mp3 file with flash player.',
    M_AUTHOR        =>  'Hugo',
    M_VERSION       =>  '1',
    M_MINVERSION    =>  '0.8.1',
    M_TARGET        =>  'file',
    M_EXTENSION     =>  'mp3',

    M_CONF          =>  array(
        'auto_play'  =>  array(
            M_DESCRIPTION   =>  'Auto run music', 
            M_DEFAULT       =>  true
        ),
        'loop'  =>  array(
            M_DESCRIPTION   =>  'Loop music', 
            M_DEFAULT       =>  false
        ),
    )
);

?>
