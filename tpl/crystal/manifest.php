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
    'name'          =>  'Crystal',
    'description'   =>  'Thème en XHtml',
    'author'        =>  'Hugo',
    'version'       =>  '1',
    'header'        =>  'Content-Type: text/html; charset=UTF-8',
    'img-src'       =>  '/tpl/%s/img',
    'php-function'  =>  null,
    'minversion'    =>  '0.8.1',
    'stylesheets'   =>  array(
            array(
                'type'  =>  'text/css',
                'media' =>  'screen/projection',
                'title' =>  'Standard',
                'href'  =>  'css/default.css'
            ),
            array(
                'type'  =>  'text/css',
                'media' =>  'screen/projection',
                'title' =>  'Cacher l\'arborescence',
                'href'  =>  'css/no-tree.css'
            ),
            array(
                'type'  =>  'text/css',
                'media' =>  'screen/projection',
                'title' =>  'Spécial (Cache uniquement la barre d\'action)',
                'href'  =>  'css/special.css'
            ),
            array(
                'type'  =>  'text/css',
                'media' =>  'screen/projection',
                'title' =>  'Spécial sans arborescence',
                'href'  =>  'css/special-no-tree.css'
            ),
            array(
                'type'  =>  'text/css',
                'media' =>  'screen/projection',
                'title' =>  'FlatDark',
                'href'  =>  'css/flatdark.css'
            ),
            array(
                'type'  =>  'text/css',
                'media' =>  'screen/projection',
                'title' =>  'FlatDark sans arborescence',
                'href'  =>  'css/flatdark-no-tree.css'
            ),
    )
);

?>
