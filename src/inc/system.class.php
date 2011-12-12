<?php
/*
	This file is part of Hyla
	Copyright (c) 2004-2006 Charles Rincheval.
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
	/*	Abstraction timestamp
	 */
	function time() {
		$time = time() + (TIME_OFFSET * 60);
		return $time;
	}
	
	/*	Abstraction pour l'heure
	 	@param	string	$format	Format de l'heure
	 	@param	int		$time		Le timestamp
	 */
	function date($format, $time = 0) {
		if ($time == 0)
			$time = system::time();
		
		$date = date($format, $time);
		return $date;
	}

	/*	Pour le chronométrage...
	 */
	function chrono() {
		$mtime = microtime();
		$mtime = explode(' ',$mtime);
		$mtime = $mtime[1] + $mtime[0];
		return $mtime;
	}

	/*	Couche pour l'envoie de mail
	 	@param	string	$mail		L'adresse
	 	@param	string	$subject	Le sujet
	 	@param	string	$text		Le texte du mail
	 	@param	string	$from		L'expéditeur
	 */
	function mail($mail, $subject, $text, $from) {
		$ret = mail($mail, $subject, $text, 'From: '.$from);
		return $ret;
	}
	
	/*	Génération d'identifiant unique
		@param	int	$size	Nombre de caractère de l'ID
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
	
	/*	Retourne une chaine indiquant le système
	 */
	function getOS() {
		return PHP_OS;
	}

	/*	Fonction affichant une erreur en cas de time out
	 */
	function timeOut() {
		if (!defined('EXIT'))
			exit(__('Time out !'));
	}

	function end($msg = null) {
		define('EXIT', 'ok');
		exit($msg);
	}
}

?>
