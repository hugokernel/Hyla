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


function DBUG($var) {
	echo '<pre>';
	print_r($var);
	echo '</pre>';
}


/*	On formate la date correctement !
	@param string $date Date à formater
	@param int $type Type de formatage -> 0: que la date; 1: date et heure; 2: que l'heure; 3: affiche la date si $date2 > $date; 4: affiche la date textuellement
 */
function format_date($date, $type = 0, $date2 = null)
{
	// Si c'est un time stamp, on converti !
	// Compatiblité ancienne version...à terme, tout en timestamp !
	if (is_numeric($date))
		$date = system::date('Y-m-d H:i:s', $date);
	
	list($annee, $mois, $jour) = explode('-', substr($date, 0, 10));
	$ret = $jour.'/'.$mois.'/'.$annee;

	switch ($type)
	{
		case 1:
			$ret .= substr($date, 10, strlen($date));
			break;
		case 2:
			$ret = substr($date, 10, strlen($date));
			break;
		case 3:
			if ($date2 == null)
				$date2 = system::date('Y-m-d H:i:s');
			if (substr($date2, 0, 10) > substr($date, 0, 10))
				$ret .= substr($date, 10, strlen($date));
			else
				$ret = substr($date, 10, strlen($date));
			break;
		case 4:
			$ret = system::date('w j m');
			list ($day, $num, $month) = explode(' ', $ret);
			$ret = traduct('datetime', 'day', $day).' '.$num.' '.traduct('datetime', 'month', $month);
			break;
	}

	return 	$ret;
}

/*	Créér un message d'erreur
	@param	string	$msg	Le message d'erreur
 */
function view_error($msg) {
	if ($msg) {
		$tpl = new Template(FOLDER_TEMPLATE);
		$tpl->set_file('misc', 'misc.tpl');
		$tpl->set_block('misc', array(
				'mkdir'			=>	'Hdlmkdir',
				'rename'		=>	'Hdlrename',
//				'pagination'	=>	'Hdlpagination',
				'error'			=>	'Hdlerror',
				'status'		=>	'Hdlstatus'
				));
		$tpl->set_var('ERROR', $msg);
		$tpl->parse('Hdlerror', 'error', true);
		return $tpl->parse('Output', 'misc');
	}
}

/*	Créér un message de status
	@param	string	$msg	Le message de status
 */
function view_status($msg) {
	if ($msg) {
		$tpl = new Template(FOLDER_TEMPLATE);
		$tpl->set_file('misc', 'misc.tpl');
		$tpl->set_block('misc', array(
				'mkdir'			=>	'Hdlmkdir',
				'rename'		=>	'Hdlrename',
//				'pagination'	=>	'Hdlpagination',
				'error'			=>	'Hdlerror',
				'status'		=>	'Hdlstatus'
				));
		$tpl->set_var('STATUS', $msg);
		$tpl->parse('Hdlstatus', 'status', true);
		return $tpl->parse('Output', 'misc');
	}
}


/*	Redirection d'une page à une autre
	@param string $title Titre fenêtre
	@param string $page Page de redirection
	@param string $msg Message
	@param int $attente Temps d'attente (secondes)
	@param bool $rnow Afficher le lien pour rediriger tout de suite ou non !
 */
function redirect($title, $page, $msg, $attente = 5, $rnow = true)
{
	// Le template...
//	include_once('src/lib/template.class.php');
	$tpl = new Template(FOLDER_TEMPLATE);
	$tpl->set_file('redirect', 'redirect.tpl');
	$tpl->set_block('redirect', 'AffichRedirectNow', 'HdlAffichRedirectNow');

	$tpl->set_var(array(
			'NAVIG_TITLE'	=>	$title,
			'ATTENTE'		=>	$attente,
			'PAGE'			=>	$page,
			'MESSAGE'		=>	$msg));

	if ($rnow)
		$tpl->parse('HdlAffichRedirectNow', 'AffichRedirectNow', true);
	
	$tpl->parse('HandleAffichRedirect', 'AffichRedirect', true);
	$tpl->pparse('OutPut', 'redirect');
	unset($tpl);
}

/*	Vérifie si le mail est valide...
	@param string $chaine Mail à vérifier...
 */
function valid_email($chaine)
{
	return ereg('^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+'.'@'.
	'[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.'.'[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$', $chaine);
}

/*	On met un slashes ou non selon la config...
 */
function add_slashes($chaine)
{
	return (get_magic_quotes_gpc()) ? $chaine : addslashes($chaine);
}

/*	Et on enlève les slashes selon la config ! STACH STACH !!
 */
function strip_slashes($chaine)
{
	return (get_magic_quotes_gpc()) ? stripslashes($chaine) : $chaine;
}


/*	Fonction permettant de renvoyer depuis combien de jour, heure, minutes un user ne s'est pas connecté
	@param string $date Date
 */
function get_nbr_day($date)
{
	// On met la date au format qu'il faut...
	list($annee, $mois, $jour) = explode("-", substr($date, 0, 10));
	list($heure, $minute, $seconde) = explode(":", substr($date, 10, 18));

	$date = mktime($heure, $minute, $seconde, $mois, $jour, $annee);
	$date = system::time() - $date;
	$ret['jour'] = round($date / 3600 / 24);

	return (int)$ret['jour'];
}

/*	Validation des noms des users à l'inscription
	@param string $login Login à valider
	@param bool $ret Valide ou non valide...
 */
function valid_login($login)
{
	$ret = true;
	// On interdit les pseudos commencant par un caractère numérique et les espaces dans le pseudo
	if ((int)ereg(" ", $login))
		$ret = false;
	if ((int)ereg("^[0123456789]", $login))
		$ret = false;

	return $ret;
}

/*	Test de 'empty' multiple renvoyant le message associé si un champs est vide...
	@param	array	$tab_empty	Tableau contenant les string et leurs message à renvoyé si vide
	@param	&		$msg		Référence de la chaine qui doit contenir le message de retour
	@return	Booléen à false si au moins un champs était vide
 */
function verif_value($tab_empty, &$msg)
{
	$bool = true;
	foreach ($tab_empty as $key => $val) {
		if (empty($key)) {
			$msg = $val;
			$bool = false;
			break;
		}
	}
	return $bool;
}

/*	Renvoie si l'extension d'un fichier est valide ou non...
	@param string $name_file Le fichier
	@param array $tab_ext Tableau contenant les extensions autorisées !
 */
function verif_extension($name_file, $tab_ext)
{
	$ret = false;
	$pos = strrpos($name_file, '.');
	if ($pos)
	{
		$ext = substr($name_file, $pos + 1, strlen($name_file));
		if (in_array(strtolower($ext), $tab_ext))
			$ret = true;
	}
	return $ret;
}

/*	Renvoie une image selon l'extension du fichier...
	@param string $name_file Le fichier
 */
function get_icone_from_ext($ext)
{
//	$ext = file::getExtension($name_file);
	switch ($ext)
	{
		case 'avi':
		case 'mpg':
		case 'mpeg':
		case 'asf':	$ext_img = 'video.png'; break;
		
		case 'doc':	$ext_img = 'doc.png'; break;

		case 'bmp':
		case 'gif':
		case 'jpg':
		case 'png':	$ext_img = 'image.png'; break;
		
		case 'c':	$ext_img = 'c.png'; break;
		case 'cpp':	$ext_img = 'cpp.png'; break;
		case 'hpp':
		case 'h':	$ext_img = 'c-header.png'; break;
		
		case 'ogg':
		case 'wav':
		case 'mp3':	$ext_img = 'audio.png'; break;
		case 'mid':	$ext_img = 'midi.png'; break;
		
		case 'ram':	$ext_img = 'realmedia.png'; break;
		
		case 'php3':
		case 'php4':
		case 'php5':
		case 'php':	$ext_img = 'php.png'; break;
		
		case 'pdf':	$ext_img = 'pdf.png'; break;
		case 'txt':	$ext_img = 'txt.png'; break;
		
		case 'tar':	$ext_img = 'tar.png'; break;

		case 'deb':	$ext_img = 'deb.png'; break;
		
		case 'hex':
		case 'rom':	$ext_img = 'rom.png'; break;
		
		case 'gz':	$ext_img = 'tgz.png'; break;
		case 'bz2':	$ext_img = 'bz2.png'; break;
		case 'rar':	$ext_img = 'rar.png'; break;
		case 'zip':	$ext_img = 'zip.png'; break;
		default:	$ext_img = 'unknown.png'; break;
	}
	
	return $ext_img;
}

/*	Retourne la taille d'un fichier 'intelligiblement' ?!?
	@param int $size Taille en octets
	@param int $rnd La précision de l'arrondi !
 */
function get_intelli_size($size, $rnd = -1)
{
	$size = ($size == null) ? 0 : $size;
	if ($size >= 1073741824) {
		$rnd = ($rnd == -1) ? 4 : $rnd;
		$size = round($size / 1073741824, $rnd).' Go';
	} else if ($size >= 1048576) {
		$rnd = ($rnd == -1) ? 2 : $rnd;
		$size = round($size / 1048576, $rnd).' Mo';
	} else if ($size >= 1024) {
		$rnd = ($rnd == -1) ? 1 : $rnd;
		$size = round($size / 1024, $rnd).' Ko';
	} else
		$size = $size.' o';
	return $size;
}
/*
Trouvé sur php.net en remplacement de get_intelli_size
function size_hum_read($size){

//Returns a human readable size
  $i=0;
  $iec = array("B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");
  while (($size/1024)>1) {
   $size=$size/1024;
   $i++;
  }
  return substr($size,0,strpos($size,'.')+4).$iec[$i];
}
*/



/*	Renvoie le texte asocié
	@param string $str Chaine voulue
 */
function traduct($str, $str1 = null, $str2 = null)
{
	global $g_tab;
	$ret = null;
	$numarg = func_num_args();
	switch ($numarg) {
	case 3:
		$ret = $g_tab[$str][$str1][$str2];
		break;
	case 2:
		$ret = $g_tab[$str][$str1];
		break;
	case 1:
	default:
		$ret = $g_tab[$str];
	}
	//$ret = htmlentities($ret);
	return $ret;
}

function format($url, $current = true) {

	if ($current) {
		global $cobj;
		$obj = $cobj;
	}

	$tab = explode('/', $url);
	$ret = '<a href="'.obj::getUrl('/').'">.</a>';
	$url = null;
	$size = sizeof($tab);
	for ($i = 0; $i < $size; $i++) {
		if ($tab[$i]) {
			$url .= '/'.$tab[$i];
			// On met un '/' uniquement si il s'agit d'un dossier
			$value = ($current) ? (($obj->type == TYPE_DIR) ? 1 : 0) : (is_dir(FOLDER_ROOT.$url));
			$ret .= ' / <a href="'.obj::getUrl($url).(($i < $size - 1 || $value) ? '/' : '').'" class="object">'.$tab[$i].'</a>';
		}		
	}

	if ($current && isset($obj->target))
		$ret .= ' ! '.$obj->target;

	return $ret;
}

if (!function_exists('fnmatch')) {
	function fnmatch($pattern, $string) {
	   for ($op = 0, $npattern = '', $n = 0, $l = strlen($pattern); $n < $l; $n++) {
		   switch ($c = $pattern[$n]) {
			   case '\\':
				   $npattern .= '\\' . @$pattern[++$n];
			   break;
			   case '.': case '+': case '^': case '$': case '(': case ')': case '{': case '}': case '=': case '!': case '<': case '>': case '|':
				   $npattern .= '\\' . $c;
			   break;
			   case '?': case '*':
				   $npattern .= '.' . $c;
			   break;
			   case '[': case ']': default:
				   $npattern .= $c;
				   if ($c == '[') {
					   $op++;
				   } else if ($c == ']') {
					   if ($op == 0) return false;
					   $op--;
				   }
			   break;
		   }
	   }

	   if ($op != 0) return false;
	
	   return preg_match('/' . $npattern . '/i', $string);
	}
}

?>
