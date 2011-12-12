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
//	if (is_numeric($date)) {
		$date = system::date('Y-m-d H:i:s', $date);
//	}
	
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
	global $tpl;
	if ($msg) {
		if (!$tpl) {
			$tpl = new Template(DIR_TEMPLATE);
			$tpl->set_file('misc', 'misc.tpl');
			$tpl->set_block('misc', array(
					'error'			=>	'Hdlerror',
					'status'		=>	'Hdlstatus',
					'suggestion'	=>	'Hdlsuggestion',
					'sort'			=>	'Hdlsort',
					'toolbar'		=>	'Hdltoolbar'
					));
		}

		$tpl->set_var('VIEW_ERROR', $msg);
		$out = $tpl->parse('Hdlerror', 'error', true);
		$tpl->set_var('Hdlerror');
		return $out;
//		return $tpl->parse('Output', 'misc');
	}
}

/*	Créér un message de status
	@param	string	$msg	Le message de status
 */
function view_status($msg) {
	global $tpl;
	if ($msg) {
		if (!$tpl) {
			$tpl = new Template(DIR_TEMPLATE);
			$tpl->set_file('misc', 'misc.tpl');
			$tpl->set_block('misc', array(
					'error'			=>	'Hdlerror',
					'status'		=>	'Hdlstatus',
					'suggestion'	=>	'Hdlsuggestion',
					'sort'			=>	'Hdlsort',
					'toolbar'		=>	'Hdltoolbar'
					));
		}

		$tpl->set_var('VIEW_STATUS', $msg);
		$out = $tpl->parse('Hdlstatus', 'status', true);
		$tpl->set_var('Hdlstatus');
		return $out;
	}
}

/*	Créér un message de suggestion
	@param	string	$msg	Le message de la suggestion
 */
function view_suggestion($msg) {
	global $tpl;
	if ($msg) {
		if (!$tpl) {
			$tpl = new Template(DIR_TEMPLATE);
			$tpl->set_file('misc', 'misc.tpl');
			$tpl->set_block('misc', array(
					'error'			=>	'Hdlerror',
					'status'		=>	'Hdlstatus',
					'suggestion'	=>	'Hdlsuggestion',
					'sort'			=>	'Hdlsort',
					'toolbar'		=>	'Hdltoolbar'
					));
		}

		$tpl->set_var('SUGGESTION', $msg);
		$out = $tpl->parse('Hdlsuggestion', 'suggestion', true);
		$tpl->set_var('Hdlsuggestion');
		return $out;
	}
}

/*	Redirection d'une page à une autre
	@param	string	$title		Titre fenêtre
	@param	string	$page		Page de redirection
	@param	string	$msg		Message
	@param	int		$attente 	Temps d'attente (secondes)
	@param	bool	$rnow		Afficher le lien pour rediriger tout de suite ou non !
 */
function redirect($title, $page, $msg, $attente = 0, $rnow = true) {
	global $conf, $tpl;

	if (!$attente) {
		$attente = $conf['time_of_redirection'];
	}

	// Le template...
	$tpl = new Template(DIR_TEMPLATE);
	$tpl->set_file('redirect', 'redirect.tpl');
	$tpl->set_block('redirect', 'AffichRedirectNow', 'HdlAffichRedirectNow');

	include('l10n/'.$conf['lng'].'/suggestions.php');

	$tpl->set_var(array(
			'STYLESHEET'	=>	get_css(),
			'TITLE'			=>	$title.' '.$conf['title'],
			'ATTENTE'		=>	$attente,
			'PAGE'			=>	$page,
			'MESSAGE'		=>	$msg,
			'SUGGESTION'	=>	get_suggest($suggest['redirect']),
			));

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

/*	Ajout d'une feuille de style
	@param	string	$href	le lien vers la feuille de style en partant de la racine de Hyla
	@param	string	$title	Le titre
	@param	string	$type	Le type (ex: text/css)
	@param	string	$media	Le media (ex: screen/projection)
 */
function add_stylesheet($href, $title, $type = 'text/css', $media = 'screen/projection') {
	global $styles;
	$styles[] = array(	'href'	=>	$href,
						'title'	=>	$title,
						'type'	=>	$type,
						'media'	=>	$media,
						);
}

/*	Ajout d'une feuille de style d'un plugin
	@param	string	$href	le lien vers la feuille de style en partant de la racine de Hyla
	@param	string	$title	Le titre
	@param	string	$type	Le type (ex: text/css)
	@param	string	$media	Le media (ex: screen/projection)
 */
function add_stylesheet_plugin($href, $title, $type = 'text/css', $media = 'screen/projection') {
	global $styles_plugin;
	$styles_plugin[] = array(	'href'	=>	$href,
								'title'	=>	$title,
								'type'	=>	$type,
								'media'	=>	$media,
						);
}

/*	Charge le tableau $conf à partir du fichier de configuration
 */
function load_config() {

	global $conf;
	$conf = array();

	$tab = (function_exists('parse_ini_file')) ? parse_ini_file(FILE_INI) : iniFile::read(FILE_INI, true);

	$conf['webmaster_mail']		= $tab['webmaster_mail'];
	$conf['name_template']		= $tab['template'] ? $tab['template'] : 'default';

	$conf['style']				= $tab['style'] ? $tab['style'] : 'default';

	$conf['lng']				= $tab['lng'] ? $tab['lng'] : DEFAULT_LNG;
	$conf['title']				= $tab['title'];

	$conf['file_chmod']			= octdec($tab['file_chmod']);
	$conf['dir_chmod']			= octdec($tab['dir_chmod']);
	$conf['send_mail']			= $tab['send_mail'];


	$conf['group_by_sort']		= $tab['group_by_sort'];
	$conf['nbr_obj']			= $tab['nbr_obj'];


	$conf['view_hidden_file']	= $tab['view_hidden_file'];
	$conf['download_counter']	= $tab['download_counter'];

	$conf['dir_default_plugin']	= $tab['dir_default_plugin'] ? strtolower($tab['dir_default_plugin']) : 'dir';

	$conf['view_toolbar']		= $tab['view_toolbar'];

	$conf['view_tree']			= $tab['view_tree'];

	$conf['url_scan']			= $tab['url_scan'];

	$conf['time_of_redirection']		= $tab['time_of_redirection'];
	if ($conf['time_of_redirection'] < 1)
		$conf['time_of_redirection'] = 1;

	$conf['download_dir']		= $tab['download_dir'];

	$conf['url_encode']			= $tab['url_encode'];

	$conf['auth_method']		= $tab['auth_method'];

	$conf['rss_nbr_obj']		= $tab['rss_nbr_obj'];
	$conf['rss_nbr_comment']	= $tab['rss_nbr_comment'];

	$conf['fs_charset_is_utf8']	= $tab['fs_charset_is_utf8'];

	$sort_tab = array(
			0 => SORT_DEFAULT,
			1 => SORT_NAME_ALPHA,
			2 => SORT_NAME_ALPHA_R,
			3 => SORT_EXT_ALPHA,
			4 => SORT_EXT_ALPHA_R,
			5 => SORT_CAT_ALPHA,
			6 => SORT_CAT_ALPHA_R,
			7 => SORT_SIZE,
			8 => SORT_SIZE_R,
			);
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
	$ret = (!array_key_exists($ext, $tab_icon)) ? $tab_icon['?']['icon'] : (isset($tab_icon[$ext]['icon']) ? $tab_icon[$ext]['icon'] : $tab_icon['?']['icon']);
	$dir = (!array_key_exists('dir', $tab_icon)) ? $tab_icon['?']['dir'] : $tab_icon[$ext]['dir'];
	return $dir.$ret;
}

/*	Renvoie la catégorie du fichier selon l'extension
 */
function get_cat($ext) {
	global $tab_icon;
	$cat = (!array_key_exists($ext, $tab_icon)) ? __($tab_icon['?']['cat']) : (array_key_exists('cat', $tab_icon[$ext]) ? __($tab_icon[$ext]['cat']) : __($tab_icon['?']['cat']));
	return $cat;
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


function get_suggest($tab) {

	$str = null;

	$tab[] = null;
	shuffle($tab);
//	$val = rand(0, sizeof($tab[$context]) - 1);
	$str = $tab[0];

	return view_suggestion($str);
}

/*	Renvoie l'élément encodé en UTF8, les éléments venant du système de fichiers doivent
	obligatoirement passer par cette fonction avant affichage
	@param	string	$str	La chaine à encoder
 */
function get_utf8($str) {
	global $conf;
	return ($conf['fs_charset_is_utf8'] ? $str : utf8_encode($str));
}

/*	Renvoie l'élément en ISO
	@param	string	$str	La chaine
 */
function get_iso($str) {
	global $conf;
	return ($conf['fs_charset_is_utf8'] ? utf8_decode($str) : $str);
}

/*	Renvoie l'élément encodés de la même manière que le système de fichiers
	@param	string	$str	La chaine
 */
function get_2_fs_charset($str) {
	global $conf;
	return ($conf['fs_charset_is_utf8'] ? $str : utf8_decode($str));
}

/*	Renvoie l'objet correctement formaté...
	@param	string	$str	La chaine
 */
function view_obj($str) {
	$str = get_iso($str);
	$str = htmlentities($str);

	// Le moteur de template peut squizzer les { et }...
	$str = str_replace(array('{', '}'), array('&#123;', '&#125;'), $str);
	$str = get_utf8($str);

	return $str;
}

/*	Test les droits
	@param	...	ACL_xxx, ACL_xxx...
 */
function acl_test() {
	global $cobj, $cuser, $url;
	$ret = false;

	$args = func_get_args();
	$r = call_user_func_array(array('acl', 'ok'), $args);
	if (!$r) {
		if ($cuser->id == ANONYMOUS_ID) {
			$_SESSION['sess_url'] = $_SERVER['REQUEST_URI'];
			redirect(__('Error'), url::getPage('login'), __('Thank you for authenticate you'));
			system::end();
			break;
		} else {
			redirect(__('Error'), url::getCurrentObj(), __('You cannot use this functionality !'));
			system::end();
			break;
		}
	} else
		$ret = true;

	return $ret;
}


/*	Renvoie le chemin réel vers le fichier (si c'est une archive, renvoie le chemin vers le cache)
 */
function get_real_directory() {
	global $cobj;
	cache::getFilePath($cobj->file, $file);
	return ($cobj->type == TYPE_ARCHIVED) ? DIR_ROOT.$file.'/'.$cobj->target : $cobj->realpath;
}


/*	Les fonctions qui peuvent ne pas exécuter selon l'OS ou la version de PHP
 */

if (!function_exists('fnmatch')) {
	function fnmatch($pattern, $string) {
		return preg_match('/^'.strtr(addcslashes($pattern, '/\\.+^$(){}=!<>|'), array('*' => '.*', '?' => '.?')).'$/i', $string);
	}
}


if (!function_exists('array_walk_recursive')) {


	function array_walk_recursive(&$input, $funcname, $userdata = null) {

		if (!is_callable($funcname))
			return false;

		if (!is_array($input))
			return false;

		foreach ($input as $key => $value) {
			if (is_array($input[$key])) {
				array_walk_recursive($input[$key], $funcname, $userdata);
			} else {
				if (!empty($userdata)) {
					$input[$key] = $funcname($value, $userdata);
				} else {
					$input[$key] = $funcname($value);
				}
			}
		}
		return true;
	}
}

?>
