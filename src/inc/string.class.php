<?php
/*
    This file is part of Hyla
    Copyright (c) 2004-2012 Charles Rincheval.
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


class string
{
    /*  Coupe un mot ou une phrase et rajoute des '...'
        @param  string  $str        Chaine à couper
        @param  int     $size       Taille de la coupe
        @param  string  $end        Tronquer avec cette chaine
     */
    public static function cut($str, $size = 25, $end = '...') {

        if (strlen($str) > $size) {
			$str = substr($str, 0, $size - strlen($end)) . $end;
		}

		return $str;
    }

    /*  Recherche une chaine de caractère dans un ensemble de caractères (strpbrk en php5)
        @param  string  $str    La chaine à vérifier
        @param  string  $list   Liste de caractère
        @return Retourne true si la chaine contient un des caractère passé en second paramètre
     */
    public static function test($str, $list) {
        $ret = false;
        $size = strlen($list);
        for ($i = 0; $i < $size; $i++) {
            if ($str{0} == $list{$i} || strpos($str, $list{$i})) {
                $ret = true;
                break;
            }
        }
        return $ret;
    }

    /*  Format une chaine comme il faut !
        @param  string  $string La chaine à formater
        @param  bool    $n      Accepter les retour chariot ou non
        @param  bool    $url    "Déformater" les urls ou non
     */
    public static function format($string, $n = true, $url = false) {

        $string = strip_tags( $string);

        // Le nl2br rajoute un retour chariot après le <br /> donc :
        $string = ($n) ? preg_replace( "/\r\n|\n/i", '<br/>', $string) : preg_replace( "/\r\n|\n/i", ' ', $string);

        if ($url) {
            $_format = create_function( '$tab', 'return \'<a href="\'.$tab[0].\'">\'.string::cut($tab[0], 60).\'</a>\';');
            $string = preg_replace_callback( '#([a-zA-Z]+://[/]*)([a-zA-Z0-9\-_\.]+[:0-9]*(/[a-zA-Z0-9\-/\._\?=\&\,\;\#\!\%\:\@\/]+)?[/]*)#', $_format, $string);
            $string = preg_replace( '/([a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4})/', '<a href="mailto:$0">$1</a>', $string);
        }

        return $string;
    }

    /*  Opération inverse de formatString
        @param  string  $string La chaine à "déformater"
        @param  bool    $n      Convertir les <br> en saut
        @param  bool    $url    "Déformater" les urls ou non
     */
    public static function unFormat($string) {
        $string = preg_replace('/<br />|<br>/i', "\n", $string);
        // ToDo: passer le code ci-dessous dans une seule exp. reg.
        $string = preg_replace('/<.*href="mailto:?(.*:\/\/)?([^ \/]*)([^ >"]*)"?[^>]*>(.*)(<\/a>)/', '$4', $string);
        $string = preg_replace('/<.*href="?(.*:\/\/)?([^ \/]*)([^ >"]*)"?[^>]*>(.*)(<\/a>)/', '$1$2$3', $string);
        return $string;
    }
}
