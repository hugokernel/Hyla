<?php
/*
	This file is part of iFile
	Copyright (c) 2004-2006 Charles Rincheval.
	All rights reserved

	iFile is free software; you can redistribute it and/or modify it
	under the terms of the GNU General Public License as published
	by the Free Software Foundation; either version 2 of the License,
	or (at your option) any later version.

	iFile is distributed in the hope that it will be useful, but
	WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with iFile; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */


class string
{
	/*	Renvoie le nombre de mots demandé dans une phrase
		@param string $str La phrase
	 	@param int $nbr_word Nombre de mots souhaités
	 */
	function cutWord($str, $nbr_word) {
		$tab_word = array();
		$tab_word = explode(' ', $str);
		for ($i = 0, $word = null; $i < $nbr_word; $i++)
			$word .= $tab_word[$i].' ';
		return $word;
	}

	/*	Coupe un mot ou une phrase et rajoute des '...'
		@param string $str Chaine à couper
	 	@param int $size Taille de la coupe
		@param string $end Tronquer avec ce string
	 */
	function cut($str, $size = 25, $end = '...') {
		if (strlen($str) > $size)
			$str = substr($str, 0, $size - strlen($end)).$end;
	
		return $str;
	}

	/*	On ne tient pas compte des accents !
		@param string $str Chaine de caractères !
		@param bool $_lower Si à true, renvoie la chaine en miniscule
	 */
	function skipAccent($str, $_lower = false) {
		$str = ($_lower) ? strtolower($str) : $str;
		$tofind = "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ";
		$replac = "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn";
		return strtr($str, $tofind, $replac);
	}

	/*	Format une chaine comme il faut !
		@param	string	$string	La chaine à formater
		@param	bool	$n		Accepter les retour chariot ou non
	 */
	function format($string, $n = true) {
		$string = htmlspecialchars($string, ENT_QUOTES);

		// Le nl2br rajoute un retour chariot après le <br /> donc :
		$string = ($n) ? eregi_replace("\r\n|\n", '<br />', $string) : eregi_replace("\r\n|\n", ' ', $string);

		return $string;
	}

	/*	Opération inverse de formatString
		@param	string	$string	La chaine à "déformater"
	 */
	function unFormat($string, $n = true) {
		if ($n)
			$string = eregi_replace("<br />|<br>", "\n", $string);

		return $string;
	}
}

?>
