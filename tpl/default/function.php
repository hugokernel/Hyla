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

/*  Ici, se trouve le code qui ne peut être généré directement par le moteur de template
    pour cause de vitesse de traitement, il en effet beaucoup plus rapide de faire ainsi.
 */


/*  Créé un chemin cliquable
 */
function format_title($_url, $current = true) {

    global $url, $obj;

    $cobj = $obj->getCurrentObj();

    $tab = explode('/', $_url);

    $ret = null;

    if ($current) {
        $ret .= '<a href="'.$url->linkToObj('/').'" title="Revenir à la racine"><img src="'.DIR_IMAGE.'/home.png" class="icon" alt="Revenir à la racine" /></a>';
    }

    $ret .= '<span class="dir" id="'.get_object_id($_url).'">';

    if ($current && $cobj->file != null && $cobj->file != '/') {
        $ret .= ' <img src="'.$cobj->icon.'" class="icon" alt="Icone" />';
    }

    $_url = null;
    $size = sizeof($tab);
    for ($i = 0; $i < $size; $i++) {
        if ($tab[$i]) {
            $_url .= '/'.$tab[$i];
            // On met un '/' uniquement si il s'agit d'un dossier
            $value = ($current) ? (($cobj->type == TYPE_DIR) ? 1 : 0) : (is_dir($obj->getRoot().$_url));
            if ($i + ($cobj->type == TYPE_DIR ? 2 : 1) == $size) {
                $ret .= ' / <span id="'.get_object_id($_url).'" class="edit-rename">'.view_obj($tab[$i]).'</span>';
            } else {
                $ret .= ' / <a href="'.$url->linkToObj($_url).(($i < $size - 1 || $value) ? '/' : '').'">'.view_obj($tab[$i]).'</a>';
            }
        }
    }

    if ($current && isset($cobj->target))
        $ret .= ' ! '.view_obj($cobj->target);

    return $ret.'</span>';
}

/**
 *  Return an id in according to w3c standard
 */
function get_object_id($obj) {

    static $count = 0;

    /*  See those url for explanation about this :
     *  - http://www.w3.org/TR/html4/types.html#h-6.2
     *  - http://www.w3.org/TR/REC-CSS2/grammar.html
     */

    // Prefix
    $ret = 'hyla-obj-';

    // Obj
    $ret .= str_replace(array('-',  '/',    '_',    '\'',   '"',    '.'),
                        array('--', '-a',   '-b',   '-c',   '-d',   '-e'), $obj);

    // Suffix
    $ret .= '-x'.$count++;

    return $ret;
}

/**
 *  Create url from a complete path
 *  @param  string  $file       File
 *  @param  bool    $last_only  Return only last name
 */
function create_link($file, $last_only = true) {

    $url = plugins::get(PLUGIN_TYPE_URL);

    $out = null;
    $current_path = '/';

    $path = ($last_only) ? file::getLastDir($file) : $file;

    $tab = explode('/', $path);
    $size = count($tab);

    $i = 1;
    foreach ($tab as $name) {
        $slash = ($i == $size) ? null: '/';
        $current_path .= $name.$slash;
        $out .= '<a href="'.$url->linkToObj($file).'">'.view_obj($name).'</a> '.$slash.' ';
        $i++;
    }

    return $out;
}

/*  Génération de l'arborescence
 */
function get_tree() {

	global $conf, $obj, $url;

    $cobj = $obj->getCurrentObj();

	$tab = $obj->scanDir($obj->getRoot(), '/', true, true, $conf->get('view_hidden_file'));
    $tab = $obj->getDirContent('/', SORT_NAME_ALPHA, 0, -1, $tab, array('=', 'type', TYPE_DIR));
  
	$p_nombre = -1;

	$p_current= 0;
	$p_maxup = 0;
	
	$current_active = false;
	
    $var = " <ul id=\"arbre\" class=\"treeview-famfamfam\">\n\t\t";
	
    if ($tab) {

        foreach ($tab as $item) {

            // compte le nombre de passage.
            $p_nombre++;

            $tst = explode('/', $item->path);
            $num = count($tst) - 1;
            // Gestion des dossiers Ouvert class='open'
            $class_active = ($cobj->path != '/' && file::isInPath($cobj->path, $item->path)) ? ' open ' : null;
            
            // Gestion des dossiers Actif  <strong>
            $current_active = ($class_active == ' open ') ? true : false;
            
            // compte le nombre de slash dans le chemin
            $p_current = substr_count($item->file, '/');

            // Variable contenant le Lien vers l'objet
            $variable_lien="\n".'<span class="dir drop" id="'.get_object_id($item->file).'">'.
                '<img src="'.$item->icon.'" class="icon" alt="" /> <a href="'.$url->linkToObj($item->file).'">'.($current_active ? '<strong>' : null).view_obj($tst[$num - 1]).($current_active ? '</strong>' : null)."</a>"."\n".
                '</span>'."\n";

            $p_next= 0;
            if (array_key_exists($p_nombre + 1, $tab)) {
                $p_next = substr_count($tab[($p_nombre + 1)]->file, '/');
            } 

            if ($p_current < $p_next ) {
                $var .= "<li class=\"obj_container ".$class_active." \"> ".$variable_lien."<ul> \n"; 
                $p_maxup++;
            }

            if ($p_current == $p_next) {
                $var .= "<li class=\"obj_container ".$class_active." \"> ".$variable_lien."</li> \n";
            }

            if ($p_current > $p_next ) {
                // calcul du nombre de fermeture.
                if ($p_next == 2) {
                    $var .= "<li class=\"obj_container ".$class_active." \"> ".$variable_lien."</li>\n";
                    $count_temp = ($p_current - $p_next) + ($p_maxup - ($p_current - $p_next));
                    for ($j = 1; $j <= $count_temp; $j++) {
                        $var .= "</ul></li>  \n";
                    } 
                    $p_maxup=0;
                }

                if ($p_next > 2) {
                    $var.="<li class=\"obj_container ".$class_active." \"> ".$variable_lien."</li>\n";
                    // calcul du nombre de fermeture.
                    $nb_fermeture = $p_current - $p_next;
                    for ($j = 1; $j <= $nb_fermeture; $j++) {
                        $var .= "</ul></li>  \n";
                        $p_maxup--;
                    } 
                }
                
                if ($p_next == 0) {
                    $var.="<li class=\"obj_container ".$class_active." \"> ".$variable_lien."</li>\n"; 
                    $nb_fermeture = $p_current -1 ;
                    for ($j = 1; $j < $nb_fermeture; $j++) {
                        $var .= "</ul></li> \n";
                    }                 
                }
            }
           
        }
    }

    $var .= "</ul>";

	return $var;
}

/*  Génération des liens vers les feuilles de style
 */
function get_css() {
    global $page_styles;
    $conf = conf::getInstance();
    $out = null;
    $size = strlen($conf->get('style'));
    foreach($page_styles as $item) {
        if ($item['force_inc']) {
            $out .= '<style type="'.$item['type'].'" media="'.$item['media'].'">'."\n";
            $out .= '@import "'.system::getHost().HYLA_ROOT_URL.$item['href'].'";'."\n";
            $out .= "</style>\n";
        } else {
            $shref = strlen($item['href']);
            $out .= '<link rel="'.($conf->get('style') == substr($item['href'], $shref - $size, $shref) ? 'stylesheet' : 'alternate stylesheet').
                    '" type="'.$item['type'].'" media="'.$item['media'].'" title="'.$item['title'].
                    '" href="'.$item['href'].'" />'."\n";
        }
    }
    return $out;
}

/*  Renvoie le css
 */
function get_css_plugin() {
    global $page_styles_plugin;
    $out = null;

    if ($page_styles_plugin) {
        foreach($page_styles_plugin as $item) {
            $out .= '<style type="'.$item['type'].'" media="'.$item['media'].'">'."\n";
            // media="'.$item['media'].'">';
//            $out .= '@import "'.system::getHost().HYLA_ROOT_URL.$item['href'].'";'."\n";
            $out .= file::getContent(HYLA_ROOT_PATH.'/'.$item['href']);
            $out .= "</style>\n";
        }
/*
            $out .= '<link rel="stylesheet" type="'.$item['type'].'" media="screen,projection" title="Standard" href="'.system::getHost().HYLA_ROOT_URL.$item['href'].'" />';
            $out .= file::getContent(file::dirName($_SERVER['SCRIPT_FILENAME']).'/'.$item['href']);
*/
    }
    $page_styles_plugin = null;
    return $out;
}

/*  Get page headers
 */
function get_page_headers() {
    global $page_headers;
    $out = null;
    foreach($page_headers as $header) {
        $out .= '<'.$header['markup'].' ';
        foreach ($header['attribut'] as $attr => $value) {
            $out .= $attr.'="'.$value.'"';
        }
        if ($header['content']) {
            $out .= ">\n".$header['content']."\n</".$header['markup'].">";
        } else {
            $out .= ' />';
        }
    }
    return $out;
}

?>
