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


/**
 *  Debug function
 *  @param  mixed   $var    Variable
 */
function dbug($var) {
    log::add(L_DEBUG, $var);
}

/**
 *  Log function
 *  @param  mixed   $msg    Message
 */
function dlog($msg, $type = L_INFO) {
    log::add($type, $msg);
}

/**
 *  Abstraction layer for echo
 *  @param  string  $msg    Message to send
 *  @param  int     $status Status code (0 for good)
 */
function out($msg, $status = 0) {
    global $context;
    switch ($context) {
        case 'json':
            $arr = array('status' => $status, 'content' => $msg);
//            $msg = print_r($arr, true);
            $msg = json_encode($arr);
            break;
        case 'default':
        default:
            $msg .= "\n";
            break;
    }

    echo $msg;
}

/*  On formate la date correctement !
    @param string $date Date à formater
    @param int $type Type de formatage -> 0: que la date; 1: date et heure; 2: que l'heure; 3: affiche la date si $date2 > $date; 4: affiche la date textuellement
 */
function format_date($date, $type = 0, $date2 = null)
{
    // Si c'est un time stamp, on converti !
    // Compatiblité ancienne version...à terme, tout en timestamp !
//  if (is_numeric($date)) {
        $date = system::date('Y-m-d H:i:s', $date);
//  }

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
        /*
        case 4:
            $ret = system::date('w j m');
            list ($day, $num, $month) = explode(' ', $ret);
            $ret = traduct('datetime', 'day', $day).' '.$num.' '.traduct('datetime', 'month', $month);
            break;
        */
    }

    return  $ret;
}

/*  Créér un message d'erreur
    @param  string  $msg    Le message d'erreur
 */
function view_error($msg) {
    global $tpl;
    if ($msg) {
        if (!$tpl) {
//            $tpl = new Template(DIR_TEMPLATE, HYLA_ROOT_PATH, DIR_TPL);
            $tpl = new Template(DIR_TPL, array( HYLA_RUN_PATH,
                                                HYLA_ROOT_PATH), $conf->get('template_name'));
            $tpl->set_file('misc', 'misc.tpl');
            $tpl->set_block('misc', array(
                    'error'         =>  'Hdlerror',
                    'status'        =>  'Hdlstatus',
                    'suggestion'    =>  'Hdlsuggestion',
                    'sort'          =>  'Hdlsort',
                    'toolbar'       =>  'Hdltoolbar'
                    ));
        }

        $tpl->set_var('VIEW_ERROR', $msg);
        $out = $tpl->parse('Hdlerror', 'error', true);
        $tpl->set_var('Hdlerror');
        return $out;
//      return $tpl->parse('Output', 'misc');
    }
}

/*  Créér un message de status
    @param  string  $msg    Le message de status
 */
function view_status($msg) {
    global $tpl;
    if ($msg) {
        if (!$tpl) {
//            $tpl = new Template(DIR_TEMPLATE, HYLA_ROOT_PATH, DIR_TPL);
            $tpl = new Template(DIR_TPL, array( HYLA_RUN_PATH,
                                                HYLA_ROOT_PATH), $conf->get('template_name'));
            $tpl->set_file('misc', 'misc.tpl');
            $tpl->set_block('misc', array(
                    'error'         =>  'Hdlerror',
                    'status'        =>  'Hdlstatus',
                    'suggestion'    =>  'Hdlsuggestion',
                    'sort'          =>  'Hdlsort',
                    'toolbar'       =>  'Hdltoolbar'
                    ));
        }

        $tpl->set_var('VIEW_STATUS', $msg);
        $out = $tpl->parse('Hdlstatus', 'status', true);
        $tpl->set_var('Hdlstatus');
        return $out;
    }
}

/*  Créér un message de suggestion
    @param  string  $msg    Le message de la suggestion
 */
function view_suggestion($msg) {
    global $tpl;
    if ($msg) {
        if (!$tpl) {
//            $tpl = new Template(DIR_TEMPLATE, HYLA_ROOT_PATH, DIR_TPL);
            $tpl = new Template(DIR_TPL, array( HYLA_RUN_PATH,
                                                HYLA_ROOT_PATH), $conf->get('template_name'));
            $tpl->set_file('misc', 'misc.tpl');
            $tpl->set_block('misc', array(
                    'error'         =>  'Hdlerror',
                    'status'        =>  'Hdlstatus',
                    'suggestion'    =>  'Hdlsuggestion',
                    'sort'          =>  'Hdlsort',
                    'toolbar'       =>  'Hdltoolbar'
                    ));
        }

        $tpl->set_var('SUGGESTION', $msg);
        $out = $tpl->parse('Hdlsuggestion', 'suggestion', true);
        $tpl->set_var('Hdlsuggestion');
        return $out;
    }
}

/*
function new_redirect($pages) {
    global $tpl;

    $conf = conf::getInstance();

    if (!$attente) {
        $attente = $conf->get('time_of_redirection');
    }

    // Le template...
//    $tpl = new Template(DIR_TEMPLATE);
    $tpl = new Template(array(  HYLA_RUN_PATH.DIR_TPL,
                                HYLA_ROOT_PATH.DIR_TPL), $conf->get('template_name'));
    $tpl->set_file('redirect', 'redirect.tpl');
    $tpl->set_block('redirect', array(
            'destination' =>  'Hdldestination',
            'AffichRedirectNow' =>  'HdlAffichRedirectNow',
    ));

    include(HYLA_ROOT_PATH.'l10n/'.$conf->get('lng').'/suggestions.php');

    $tpl->set_var(array(
            'STYLESHEET'    =>  get_css(),
            'TITLE'         =>  $title.' '.$conf->get('title'),
            'ATTENTE'       =>  $attente,
            ));

    foreach ($pages as $url => $msg) {
        $tpl->set_var(array(
            'REDIRECT_URL'  =>  $url,
            'REDIRECT_MSG'  =>  $msg,
        ));
    }

    if ($rnow)
        $tpl->parse('HdlAffichRedirectNow', 'AffichRedirectNow', true);

    $tpl->parse('HandleAffichRedirect', 'AffichRedirect', true);
    $tpl->pparse('OutPut', 'redirect');
    unset($tpl);
}
*/

/*  Redirection d'une page à une autre
    @param  string  $title      Titre fenêtre
    @param  string  $page       Page de redirection
    @param  mixed   $msg        Message
    @param  int     $attente    Temps d'attente (secondes)
    @param  bool    $rnow       Afficher le lien pour rediriger tout de suite ou non !
 */
function redirect($title, $page, $msg, $attente = 0, $rnow = true) {
    global $tpl;

    $conf = conf::getInstance();

    if (!$attente) {
        $attente = $conf->get('time_of_redirection');
    }

    // Le template...
//    $tpl = new Template(DIR_TEMPLATE, HYLA_ROOT_PATH, DIR_TPL);
    $tpl = new Template(DIR_TPL, array( HYLA_RUN_PATH,
                                        HYLA_ROOT_PATH), $conf->get('template_name'));
    $tpl->set_file('redirect', 'redirect.tpl');
    $tpl->set_block('redirect', array(
            'destination'       =>  'Hdldestination',
            'message'           =>  'Hdlmessage',
            'AffichRedirectNow' =>  'HdlAffichRedirectNow',
    ));

    include(HYLA_ROOT_PATH.'l10n/'.$conf->get('lng').'/suggestions.php');

    if (!is_array($msg)) {
        $msg = array($msg);
    }

    foreach ($msg as $message) {
        $tpl->set_var('MESSAGE', $message);
        $tpl->parse('Hdlmessage', 'message', true);
    }

    $tpl->set_var(array(
            'STYLESHEET'    =>  get_css(),
            'TITLE'         =>  $title.' '.$conf->get('title'),
            'ATTENTE'       =>  $attente,
            'PAGE'          =>  $page,
            'SUGGESTION'    =>  get_suggest($suggest['redirect']),
            ));

    if ($rnow) {
        $tpl->parse('HdlAffichRedirectNow', 'AffichRedirectNow', true);
    }

//    $tpl->parse('HdlAffichRedirect', 'AffichRedirect', true);
    $tpl->pparse('OutPut', 'redirect');
    unset($tpl);
}

/**
 *  Generate toolbar !
 */
function get_toolbar() {

    global $tpl, $url;

    $obj = obj::getInstance();
    $cobj = $obj->getCurrentObj();

    if (!$tpl) {
//        $tpl = new Template(DIR_TEMPLATE, HYLA_ROOT_PATH, DIR_TPL);
        $tpl = new Template(DIR_TPL, array( HYLA_RUN_PATH,
                                            HYLA_ROOT_PATH), $conf->get('template_name'));
        $tpl->set_file('misc', 'misc.tpl');
        $tpl->set_block('misc', array(
                'error'         =>  'Hdlerror',
                'status'        =>  'Hdlstatus',
                'suggestion'    =>  'Hdlsuggestion',
                'sort'          =>  'Hdlsort',
                'toolbar'       =>  'Hdltoolbar'
                ));
    }

    // Load plugin
    $dir = plugins::getDirFromType(PLUGIN_TYPE_GUI);
    $plugins = plugin_gui::getPlugin('page');
    foreach ($plugins as $name => $manifest) {
        $tpl->set_var(  array(
                            'URL_PLUGIN'            =>  $url->linkToPage($name),
                            'PLUGIN_NAME'           =>  __($manifest->name),
                            'PLUGIN_DESCRIPTION'    =>  __($manifest->description),
                            'PLUGIN_ICON'           =>  HYLA_ROOT_URL.$dir.$name.'/icon.png',
                        ));
        $tpl->parse('Hdltoolbar_plugin_page', 'toolbar_plugin_page', true);
    }

    // Load plugin
    $plugins = plugin_gui::getPlugin('action');
    foreach ($plugins as $name => $manifest) {

//echo (int)$manifest->obj_type.' & '.$cobj->type.' = '.($manifest->obj_type & $cobj->type).'<br>';

        if (!($manifest->obj_type & $cobj->type)) {
            continue;
        }

        $tpl->set_var(  array(
                            'URL_PLUGIN'            =>  $url->linkToCurrentObj($name),
                            'PLUGIN_NAME'           =>  __($manifest->name),
                            'PLUGIN_DESCRIPTION'    =>  __($manifest->description),
                            'PLUGIN_ICON'           =>  HYLA_ROOT_URL.$dir.$name.'/icon.png',
                        ));
        $tpl->parse('Hdltoolbar_plugin_action', 'toolbar_plugin_action', true);
    }

    return $tpl->parse('Hdltoolbar', 'toolbar', true);
}

/*  Test de 'empty' multiple renvoyant le message associé si un champs est vide...
    @param  array   $tab_empty  Tableau contenant les string et leurs message à renvoyé si vide
    @param  &       $msg        Référence de la chaine qui doit contenir le message de retour
    @return Booléen à false si au moins un champs était vide
 */
/*
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
*/

function run_tpl() {
    global $page_headers;
    global $page_styles;

    $page_headers = array();
    $page_styles = array();

    $conf = conf::getInstance();

    $current_tpl = (file_exists(DIR_TPL.$conf->get('template_name')) ? $conf->get('template_name') : 'default');
    define('DIR_TEMPLATE',  DIR_TPL.$current_tpl);



    // Entête envoyé en premier
    if (!file_exists(HYLA_ROOT_PATH.DIR_TEMPLATE.'/manifest.php')) {
        system::end(__('Unable to load template manifest file !'));
    }

    include (HYLA_ROOT_PATH.DIR_TEMPLATE.'/manifest.php');

    // Send header
    header($manifest['header']);

    // Get img dir
    $dir_img = (array_key_exists('img-src', $manifest) && $manifest['img-src']) ? str_replace('%s', $current_tpl, $manifest['img-src']) : './tpl/default/img';

    // Define DIR_IMAGE constant
    $dir = HYLA_ROOT_URL;
    if ($dir{strlen($dir) - 1} == '/' && substr($dir_img, 0, 1) == '/') {
        define('DIR_IMAGE', substr($dir, 0, strlen($dir) - 1).$dir_img);
    } else {
        define('DIR_IMAGE', $dir.$dir_img);
    }

    // Include
    $file_func = (array_key_exists('php-function', $manifest) && $manifest['php-function']) ? str_replace('%s', $current_tpl, $manifest['php-function']) : './tpl/default/function.php';
    require_once HYLA_ROOT_PATH.$file_func;

    foreach ($manifest['stylesheets'] as $elem) {
        $css_href = $elem['href'];

        // If stylesheet is in another dir, no include root dir
        if ($css_href{0} != '/' && substr($css_href, 0, 7) != 'http://') {
            $css_href = HYLA_ROOT_URL.DIR_TEMPLATE.'/'.$css_href;
        }

        add_stylesheet($css_href, $elem['title'], $elem['type'], $elem['media']);
    }

    unset($dir_img);
}

/*  Ajout d'une feuille de style
    @param  string  $href   le lien vers la feuille de style en partant de la racine de Hyla
    @param  string  $title  Le titre
    @param  string  $type   Le type (ex: text/css)
    @param  string  $media  Le media (ex: screen/projection)
 */
function add_stylesheet($href, $title, $type = 'text/css', $media = 'screen/projection', $force_inc = false) {
    global $page_styles;
    $page_styles[] = array( 'href'      =>  $href,
                            'title'     =>  $title,
                            'type'      =>  $type,
                            'media'     =>  $media,
                            'force_inc' =>  $force_inc,
                          );
}

/*  Ajout d'une feuille de style d'un plugin
    @param  string  $href   le lien vers la feuille de style en partant de la racine de Hyla
    @param  string  $title  Le titre
    @param  string  $type   Le type (ex: text/css)
    @param  string  $media  Le media (ex: screen/projection)
 */
function add_stylesheet_plugin($href, $title, $type = 'text/css', $media = 'screen/projection') {
    global $page_styles_plugin;
    $page_styles_plugin[] = array(  'href'      =>  $href,
                                    'title'     =>  $title,
                                    'type'      =>  $type,
                                    'media'     =>  $media,
                        );
}

/*  Add page header
    @param  string  $markup     Markup
    @param  array   $attr       Attributs
    @param  string  $content    Content
 */
function add_page_header($markup, $attr, $content = null) {
    global $page_headers;
    $page_headers[] = array('markup' =>  $markup, 'attribut' => $attr, 'content' => $content);
}

/*  Ouvre le fichier de liaison extensions / icones et charge en mémoire les relations
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

/*  Renvoie l'icone selon l'extension
 */
function get_icon($ext) {
    global $tab_icon;
    $ret = (!array_key_exists($ext, $tab_icon)) ? $tab_icon['?']['icon'] : (isset($tab_icon[$ext]['icon']) ? $tab_icon[$ext]['icon'] : $tab_icon['?']['icon']);
    $dir = (!array_key_exists('dir', $tab_icon)) ? $tab_icon['?']['dir'] : $tab_icon[$ext]['dir'];
    return HYLA_ROOT_URL.$dir.$ret;
}

/*  Renvoie la catégorie du fichier selon l'extension
 */
function get_cat($ext) {
    global $tab_icon;
    $cat = (!array_key_exists($ext, $tab_icon)) ? __($tab_icon['?']['cat']) : (array_key_exists('cat', $tab_icon[$ext]) ? __($tab_icon[$ext]['cat']) : __($tab_icon['?']['cat']));
    return $cat;
}

/*  Retourne la taille d'un fichier de manière compréhensible (original trouvée sur www.php.net)
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
//  $val = rand(0, sizeof($tab[$context]) - 1);
    $str = $tab[0];

    return view_suggestion($str);
}

/*  Renvoie l'élément encodé en UTF8, les éléments venant du système de fichiers doivent
    obligatoirement passer par cette fonction avant affichage
    @param  string  $str    La chaine à encoder
 */
function get_utf8($str) {
    $conf = conf::getInstance();
    return ($conf->get('fs_charset_is_utf8') ? $str : utf8_encode($str));
}

/*  Renvoie l'élément en ISO
    @param  string  $str    La chaine
 */
function get_iso($str) {
    $conf = conf::getInstance();
    return ($conf->get('fs_charset_is_utf8') ? utf8_decode($str) : $str);
}

/*  Renvoie l'élément encodés de la même manière que le système de fichiers
    @param  string  $str    La chaine
 */
function get_2_fs_charset($str) {
    $conf = conf::getInstance();
    return ($conf->get('fs_charset_is_utf8') ? $str : utf8_decode($str));
}

/*  Renvoie l'objet correctement formaté...
    @param  string  $str    La chaine
 */
function view_obj($str) {
    $str = get_iso($str);
    $str = htmlentities($str);

    // Le moteur de template peut squizzer les { et }...
    $str = str_replace(array('{', '}'), array('&#123;', '&#125;'), $str);
    $str = get_utf8($str);

    return $str;
}

/*  Test les droits
    @param  ... ACL_xxx, ACL_xxx...
 */
function acl_test() {
    global $obj, $cuser, $url;
    $ret = false;
    
    $args = func_get_args();
    $r = call_user_func_array(array('acl', 'ok'), $args);
    if (!$r) {
        if ($cuser->id == ANONYMOUS_ID) {
            $_SESSION['sess_url'] = $_SERVER['REQUEST_URI'];
            redirect(__('Error'), $url->linkToPage('login'), __('Thank you for authenticate you'));
            system::end();
            break;
        } else {
            redirect(__('Error'), $url->linkToCurrentObj(), __('You cannot use this functionality !'));
            system::end();
            break;
        }
    } else
        $ret = true;

    return $ret;
}

/*  Renvoie le chemin réel vers le fichier (si c'est une archive, renvoie le chemin vers le cache)
 */
function get_real_directory() {
    global $obj;
    $ret = null;
    $cobj = $obj->getCurrentObj();
    if ($cobj->type == TYPE_ARCHIVED) {
        require_once HYLA_ROOT_PATH.'src/inc/cache.class.php';
        cache::getFilePath($cobj->file, $file);
        $ret = HYLA_ROOT_PATH.$file.'/'.$cobj->target;
    } else {
        $ret = $cobj->realpath;
    }
    return $ret;
}


/*  Les fonctions qui peuvent ne pas exécuter selon l'OS ou la version de PHP
 */

if (!function_exists('fnmatch')) {
    function fnmatch($pattern, $string) {
        return preg_match('/^'.strtr(addcslashes($pattern, '/\\.+^$(){}=!<>|'), array('*' => '.*', '?' => '.?')).'$/i', $string);
    }
}

if (!function_exists('array_map')) {
    function array_map($funcname, $arr) {
        $ret = null;
        foreach ($arr as $a) {
            $ret[] = $funcname($a);
        }
        return $ret;
    }
}

//if (!function_exists('array_walk_recursive')) {
    function _array_walk_recursive(&$input, $funcname, $userdata = null) {

        if (!is_callable($funcname))
            return false;

        if (!is_array($input))
            return false;

        foreach ($input as $key => $value) {
            if (is_array($input[$key])) {
                _array_walk_recursive($input[$key], $funcname, $userdata);
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
//}

?>
