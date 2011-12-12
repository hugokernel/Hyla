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
		$tpl = new Template(DIR_TEMPLATE);
		$tpl->set_file('misc', 'misc.tpl');
		$tpl->set_block('misc', array(
				'error'			=>	'Hdlerror',
				'status'		=>	'Hdlstatus',
				'toolbar'		=>	'Hdltoolbar'
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
		$tpl = new Template(DIR_TEMPLATE);
		$tpl->set_file('misc', 'misc.tpl');
		$tpl->set_block('misc', array(
				'error'			=>	'Hdlerror',
				'status'		=>	'Hdlstatus',
				'toolbar'		=>	'Hdltoolbar'
				));
		$tpl->set_var('STATUS', $msg);
		$tpl->parse('Hdlstatus', 'status', true);
		return $tpl->parse('Output', 'misc');
	}
}


/*	Redirection d'une page à une autre
	@param	string	$title Titre fenêtre
	@param	string	$page Page de redirection
	@param	string	$msg Message
	@param	int		$attente Temps d'attente (secondes)
	@param	bool	$rnow Afficher le lien pour rediriger tout de suite ou non !
 */
function redirect($title, $page, $msg, $attente = 5, $rnow = true) {
	global $conf;
	// Le template...
//	include_once('src/lib/template.class.php');
	$tpl = new Template(DIR_TEMPLATE);
	$tpl->set_file('redirect', 'redirect.tpl');
	$tpl->set_block('redirect', 'AffichRedirectNow', 'HdlAffichRedirectNow');

	$tpl->set_var(array(
			'TITLE'			=>	$title.' '.$conf['title'],
			'ATTENTE'		=>	$attente,
			'PAGE'			=>	$page,
			'MESSAGE'		=>	$msg));

	if ($rnow)
		$tpl->parse('HdlAffichRedirectNow', 'AffichRedirectNow', true);
	
	$tpl->parse('HandleAffichRedirect', 'AffichRedirect', true);
	$tpl->pparse('OutPut', 'redirect');
	unset($tpl);
}

/*	Test de 'empty' multiple renvoyant le message associé si un champs est vide...
	@param	array	$tab_empty	Tableau contenant les string et leurs message à renvoyé si vide
	@param	&		$msg		Référence de la chaine qui doit contenir le message de retour
	@return	Booléen à false si au moins un champs était vide
 */
function verif_value($tab_empty, &$msg) {
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

/*	Charge le tableau $conf à partir du fichier de configuration
 */
function load_config() {

	global $conf;
	$conf = array();

	$tab = (function_exists('parse_ini_file')) ? parse_ini_file(FILE_INI) : iniFile::read(FILE_INI, true);

	$conf['webmaster_mail']	= $tab['webmaster_mail'];
	$conf['name_template']		= $tab['template'] ? $tab['template'] : 'default';
	$conf['lng']				= $tab['lng'] ? $tab['lng'] : 'fr_FR';
	$conf['title']				= $tab['title'];

	$conf['file_chmod']		= octdec($tab['file_chmod']);
	$conf['dir_chmod']			= octdec($tab['dir_chmod']);
	$conf['anonymous_add_file'] = $tab['anonymous_add_file'];
	$conf['send_mail']			= $tab['send_mail'];


	$conf['group_by_sort']		= $tab['group_by_sort'];
	$conf['nbr_obj']			= $tab['nbr_obj'];


	$conf['view_hidden_file']	= $tab['view_hidden_file'];
	$conf['download_counter']	= $tab['download_counter'];

	$conf['dir_default_plugin']	= $tab['default_plugin'] ? $tab['default_plugin'] : 'Dir';

	$conf['view_toolbar']		= $tab['view_toolbar'];

	$conf['url_scan']			= $tab['url_scan'];

	$sort_tab = array(0	=> SORT_DEFAULT, 1 => SORT_ALPHA, 2 => SORT_ALPHA_R, 3 => SORT_ALPHA_EXT, 4 => SORT_ALPHA_EXT_R);
	$conf['sort_config'] = $sort_tab[$tab['sort']];

	if ($tab['folder_first'])
		$conf['sort_config'] |= SORT_FOLDER_FIRST;

	unset($tab, $sort_tab);
}

/*	Ouvre le fichier de liaison extensions / icones et charge en mémoire les relations
 */
function load_icon_info() {
	global $tab_icon;
	$tab = (function_exists('parse_ini_file')) ? parse_ini_file(FILE_ICON, true) : iniFile::read(FILE_ICON, true);
	foreach ($tab as $key => $value) {
		$keys = explode(',', $key);
		foreach ($keys as $ext) {
			$tab_icon[trim($ext)] = $value;
		}
	}
}

/*	Renvoie l'icone selon l'extension
 */
function get_icon($ext) {
	global $tab_icon;
	$ret = (!array_key_exists($ext, $tab_icon)) ? $tab_icon['?']['icon'] : $tab_icon[$ext]['icon'];
	$dir = (!array_key_exists('dir', $tab_icon)) ? $tab_icon['?']['dir'] : $tab_icon[$ext]['dir'];
	return $dir.$ret;
}

/*	Retourne la taille d'un fichier de manière compréhensible (original trouvée sur www.php.net)
	@param int $size Taille en octets
	@param int $rnd La précision de l'arrondi !
 */
function get_human_size_reading($size, $rnd = 2){
	$iec = array('o', 'Ko', 'Mo', 'Go', 'To', 'Po', 'Eo', 'Zo', 'Yo');
	for ($i = 0; ($size / 1024) > 1; $i++) {
		$size = $size / 1024;
	}
	return round($size, $rnd).' '.$iec[$i];
}

/*	Renvoie le texte associé
	@param	string	$str	Chaine voulue
	@param	...
 */
function __($str)
{
	global $l10n;
	$ret = (array_key_exists($str, $l10n)) ? $l10n[$str] : $str;
	if (func_num_args() > 1) {
		$tab = func_get_args();
		$tab[0] = $ret;
		$ret = call_user_func_array('sprintf', $tab);
	}
	return $ret;
}

function format($_url, $current = true) {

//	if (!$current) {
		global $cobj;
		$obj = &$cobj;
//	}

	$tab = explode('/', $_url);

	$ret = null;

	if ($current) {
		$ret = '<a href="'.url::getObj('/').'" title="Revenir à la racine"><img src="'.DIR_TEMPLATE.'/img/home.png" width="32" height="32" border="0" align="middle" alt="Revenir à la racine" /></a>';
	}

	if ($current && $obj->file != null && $obj->file != '/')
		$ret .= ' <img src="'.$obj->icon.'" width="32" height="32" border="0" align="middle" alt="Icone" />';

	$_url = null;
	$size = sizeof($tab);
	for ($i = 0; $i < $size; $i++) {
		if ($tab[$i]) {
			$_url .= '/'.$tab[$i];
			// On met un '/' uniquement si il s'agit d'un dossier
			$value = ($current) ? (($obj->type == TYPE_DIR) ? 1 : 0) : (is_dir(FOLDER_ROOT.$_url));
			$ret .= ' / <a href="'.url::getObj($_url).(($i < $size - 1 || $value) ? '/' : '').'" class="object">'.$tab[$i].'</a>';
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
					if ($c == '[')
						$op++;
					else if ($c == ']') {
						if ($op == 0)
							return false;
						$op--;
					}
					break;
			}
		}

		if ($op != 0)
			return false;

		return preg_match('/' . $npattern . '/i', $string);
	}
}

?>
