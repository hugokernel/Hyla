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

class system
{
    /*  Abstraction timestamp
     */
    function time() {
        $time = time() + (TIME_OFFSET * 60);
        return $time;
    }

    /*  Abstraction pour l'heure
        @param  string  $format Format de l'heure
        @param  int     $time   Le timestamp
     */
    function date($format, $time = 0) {
        if ($time == 0)
            $time = system::time();
        $date = date($format, $time);
        return $date;
    }

    /*  Pour le chronométrage...
     */
    function chrono() {
        $mtime = microtime();
        $mtime = explode(' ',$mtime);
        $mtime = $mtime[1] + $mtime[0];
        return $mtime;
    }

    /*  Couche pour l'envoie de mail
        @param  string  $to         L'adresse
        @param  string  $subject    Le sujet
        @param  string  $text       Le texte du mail
        @param  string  $from       L'expéditeur
     */
    function mail($to, $subject, $text, $from) {
        $header = "From: $from\r\n".
                  "X-Mailer: Hyla\r\n".
                  "MIME-Version: 1.0\r\n".
                  "Content-Type: text/plain; charset=utf-8\r\n".
                  "Content-Transfer-Encoding: 8bit\r\n";
        $ret = mail($to, $subject, $text, $header);
        return $ret;
    }

    /*  Génération d'identifiant unique
        @param  int $size   Nombre de caractère de l'ID
     */
    function getUniqueID($size = 8) {
        $ret = null;
        $tab = array(
                'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm',
                'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
                '1', '2', '3', '4', '5', '6', '7', '8', '9', '0');
        $nbr_elem = count($tab) - 1;
        srand((double)microtime() * 1000000);
        for ($i = 0; $i < $size; $i++)
            $ret .= $tab[rand(0, $nbr_elem)];
        return $ret;
    }

    /*  Renvoie l'adresse complète (basé sur le code de DotClear d'Olivier Meunier)
        @access static
     */
    function getHost() {
        $server_name = $_SERVER['SERVER_NAME'];
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $scheme = 'https';
            $port = ($_SERVER['SERVER_PORT'] == '443') ? null : ':'.$_SERVER['SERVER_PORT'];
        } else {
            $scheme = 'http';
            $port = ($_SERVER['SERVER_PORT'] == '80') ? null : ':'.$_SERVER['SERVER_PORT'];
        }
        return $scheme.'://'.htmlspecialchars($server_name).$port;
    }

    /*  Retourne une chaine indiquant le système
     */
    function getOS() {
        return strtolower(PHP_OS);
    }

    /*  Renvoie si on est sur Windows...
     */
    function osIsWin() {
        $ret = false;
        if (substr(system::getOS(), 0, 3) == 'win')
            $ret = true;
        return $ret;
    }

    /*  Fonction affichant une erreur en cas de time out
     */
    function timeOut() {
        if (!defined('EXIT')) {
            exit('Fatal error : Time out !');
        }
    }

    function end($msg = null) {
        define('EXIT', 'ok');
        exit($msg);
    }
}

?>
