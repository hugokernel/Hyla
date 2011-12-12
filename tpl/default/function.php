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

/*	Ici, se trouve le code qui ne peut être généré directement par le moteur de template
	pour cause de vitesse de traitement, il en effet beaucoup plus rapide de faire ainsi.
 */


/*	Créé un chemin cliquable
 */
function format($_url, $current = true) {

	global $cobj, $url;
	$obj = &$cobj;

	$tab = explode('/', $_url);

	$ret = null;

	if ($current) {
		$ret = '<a href="'.url::getObj('/').'" title="Revenir à la racine"><img src="'.REAL_ROOT_URL.DIR_IMAGE.'/home.png" class="icon" alt="Revenir à la racine" /></a>';
	}

	if ($current && $obj->file != null && $obj->file != '/')
		$ret .= ' <img src="'.$obj->icon.'" class="icon" alt="Icone" />';

	$_url = null;
	$size = sizeof($tab);
	for ($i = 0; $i < $size; $i++) {
		if ($tab[$i]) {
			$_url .= '/'.$tab[$i];
			// On met un '/' uniquement si il s'agit d'un dossier
			$value = ($current) ? (($obj->type == TYPE_DIR) ? 1 : 0) : (is_dir(FOLDER_ROOT.$_url));
			$ret .= ' / <a href="'.url::getObj($_url).(($i < $size - 1 || $value) ? '/' : '').'">'.view_obj($tab[$i]).'</a>';
		}		
	}

	if ($current && isset($obj->target))
		$ret .= ' ! '.view_obj($obj->target);

	return $ret;
}

/*	Génération de l'arborescence
 */
function get_tree() {

	global $conf, $obj, $url, $cobj;

	$tab = $obj->scanDir(FOLDER_ROOT, $conf['view_hidden_file']);
	$tab = $obj->getDirContent('/', SORT_NAME_ALPHA, 0, -1, $tab);

	$var = "<ul id=\"arbre\">\n\t\t<li>\n\t\t\t <span>&nbsp;</span> <a href=\"".url::getObj('/').'" title="Revenir à la racine"><img src="'.DIR_TEMPLATE.'/img/home.png" width="32" height="32" border="0" align="middle" alt="Revenir à la racine" /></a>'."</li>\n";

	$curr = explode('/', $cobj->path);

	$niv = 0;

	$class = null;

	if ($tab) {

		$last = 0;

		foreach ($tab as $occ) {

			$current = ($occ->path == $cobj->path) ? true : false;

			$tst = explode('/', $occ->path);
			$num = sizeof($tst) - 1;

			$class = ($cobj->path != '/' && file::isInPath($cobj->path, $occ->path)) ? ' class="tree_current"' : $class;
			if ($num > $last) {
				$var .= "<li>\n\t<ul$class>\n\t";
			}

			if ($num < $last) {
				for ($i = 0; $i < ($last - $num); $i++) {
					$var .= "</ul></li>\n";
				}
			}
			$var .= "\t<li>";

			$var .= "\t\t\t <span>&nbsp;</span>".($current ? '<strong>' : null).
					"<a href=\"".url::getObj($occ->file).'"><img src="'.$occ->icon.'" class="icon" alt="" /> '.view_obj($tst[$num - 1])."</a>".
					($current ? '</strong>' : null).
					"\n";

			$var .= "</li>\n";
			$last = $num;

			$class = file::isInPath($cobj->path, $occ->path) ? $class : null;
		}
	}

	$last -= 2;
	for ($i = 0; $i < $last; $i++) {
		$var .= "</ul></li>\n";
	}

	if ($tab) {
		$var .= "</ul></li>\n\t";
	}

	$var .= "\n</ul>\n";

	return $var;
}

/*	Génération des liens vers les feuilles de style
 */
function get_css() {
	global $styles, $conf;
	$out = null;
	foreach($styles as $occ) {
		$out .= '<link rel="'.($conf['style'] == basename($occ['href']) ? 'stylesheet' : 'alternate stylesheet').
				'" type="'.$occ['type'].'" media="'.$occ['media'].'" title="'.$occ['title'].
				'" href="'.$occ['href'].'" />'."\n";
	}
	return $out;
}

/*	Renvoie le css
 */
function get_css_plugin() {
	global $styles_plugin, $conf;
	$out = null;

	if ($styles_plugin) {
		foreach($styles_plugin as $occ) {
			$out .= '<style type="'.$occ['type'].'">'."\n";
			// media="'.$occ['media'].'">';
			$out .= '@import "'.url::getHost().REAL_ROOT_URL.'/'.$occ['href'].'";'."\n";
			$out .= "</style>\n";
		}

/*
		foreach($styles_plugin as $occ) {
			$out .= '<style type="'.$occ['type'].'">';
			// media="'.$occ['media'].'">';
			$out .= file::getContent(file::dirName($_SERVER['SCRIPT_FILENAME']).'/'.$occ['href']);
			$out .= "</style>";
		}
*/
	}
	$styles_plugin = null;
	return $out;
}

?>
