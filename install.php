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
    WITHOUT ANY WARRANTY; without even the implied warcranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Hyla; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

header('Content-Type: text/html; charset=UTF-8');

define('CONF_FILE', 'conf/config.inc.php');
define('LOCK_FILE', 'conf/lock');
if (file_exists(LOCK_FILE)) {
    echo 'Impossible de lancer l\'installation de Hyla si le fichier <strong>'.LOCK_FILE.'</strong> est présent, supprimez-le !';
    exit();
}

define('PAGE_HOME', true);

session_name('Hyla');
session_start();
session_destroy();

if (isset($_REQUEST['step']) && $_REQUEST['step'] != 10)
    require 'src/init.php';

error_reporting(E_ERROR);

$array_step = array(
        null    =>  '1',    // Bienvenue dans l'installeur d'Hyla !
        '2'     =>  '2',    // Vérification de la configuration du serveur
        '3'     =>  '3',    // Type d'installation
        '4'     =>  '4',   // Saisi des informations
        '5'     =>  '5',   // Création du fichier de configuration
        '6'     =>  '6',   // Création des tables et insertions des données
        '7'     =>  '7',   // Création de l'utilisateur principal
        );

$url = $_SERVER['SERVER_NAME'];

$sql_user = null;
$sql_server = null;
$sql_base = null;
$host_server = null;
$sql_port = null;

if (preg_match('#([a-z]+)\.free\.fr#i', $url, $arr)) {
    $sql_user = $arr[1];
    $sql_server = 'sql.free.fr';
    $sql_base = $arr[1];
    $host_server = 'free';
} else if (preg_match('#localhost#i', $url, $arr)) {
    $sql_user = 'root';
    $sql_server = 'localhost';
    $host_server = 'localhost';
}


$error = 0;
$notice = 0;

if (defined('PREFIX_TABLE')) {
    $prefix = PREFIX_TABLE;
}


if (!defined('HYLA_VERSION')) {
    define('HYLA_VERSION', '0.8.2');
}

/*  Renvoie le contenu du fichier de configuration à générer
 */
function get_config_content($folder_root, $sql_host, $sql_base, $sql_user, $sql_pass, $root_url = null, $sql_port = null) {

    $sql_host = ($sql_port) ? $sql_host.':'.$sql_port : $sql_host;

    $var_file =
"<?php
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


/*  +----------------------------------------------------+
    | Répertoire contenant vos fichiers à lister         |
    | /!\ ATTENTION, pas de slash ou anti slash de fin ! |
    +----------------------------------------------------+
 */
define('FOLDER_ROOT', '$folder_root');


/*  L'emplacement de Hyla après le nom de domaine (sans slash de fin !)
    Ex: http://ifile.free.fr/               -> mettez ''
    Ex: http://ifile.free.fr/hyla/          -> mettez '/hyla'
    Ex: http://ifile.free.fr/data/hyla      -> mettez '/data/hyla'

    Si ce champs est vide, la valeur de \$_SERVER['PHP_SELF'] sera utilisée
 */";

    if ($root_url) {
        $var_file .= "
define('ROOT_URL', '$root_url');
";
    } else {
        $var_file .= "
//define('ROOT_URL', '');
";
    }

    $var_file .= "


/*  +---------------------------------+
    | Connection à la base de données |
    +---------------------------------+
    Dans SQL_HOST, il est possible de spécifier un port différent
    de la manière suivante : 'server:3300'
 */
define('SQL_HOST',  '$sql_host');
define('SQL_BASE',  '$sql_base');
define('SQL_USER',  '$sql_user');
define('SQL_PASS',  '$sql_pass');

?>";

    return $var_file;
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<title>Hyla installeur</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<link rel="stylesheet" type="text/css" media="screen,projection" title="Défault" href="tpl/default/css/default.css" />

<script language="javascript" type="text/javascript" src="tpl/default/js/jquery.js"></script>
<script language="javascript" type="text/javascript" src="tpl/default/js/lib.js"></script>

<script type="text/javascript">

/*  Renvoie une chaine
 */
function test_sql() {
    var ret = '?step=sqltest';
    ret += '&sql_host=' + document.forms['form_conf'].elements['sql_host'].value;
    ret += '&sql_user=' + document.forms['form_conf'].elements['sql_user'].value;
    ret += '&sql_base=' + document.forms['form_conf'].elements['sql_base'].value;
    ret += '&sql_pass=' + document.forms['form_conf'].elements['sql_pass'].value;
    ret += '&sql_port=' + document.forms['form_conf'].elements['sql_port'].value;
    return ret;
}

</script>

    <style type="text/css">

body {
    font-family: Bitstream Vera Sans, sans-serif;
    background: white;

    font-size: 90%;

    margin-left: 5%;
    margin-right: 5%;
}

h1 {
    color: #00C;
}

h2 {
    color: #00A;
}

.remarque {
    margin-left: 10px;
    padding: 5px;
    background: #EE0;
    -moz-border-radius: 5px;
}

table {
    margin-left: 5%;
    margin-right: 5%;
    width: 90%;
    text-align: left;
    border-collapse: collapse;
}

th {
    background: #EEE;
}

td, th {
    padding: 3px;
    border: 1px solid #CCC;
}

    </style>

</head>

<body>

<a href="http://www.hyla-project.org/"><img src="img/hyla.png" width="100" height="100" alt="Logo Hyla : La rainette verte" /></a>

<h1>Installation de &laquo; Hyla <?php echo HYLA_VERSION; ?> &raquo;

<?php

$step = @$_REQUEST['step'];
if (isset($step)) {
    if (array_key_exists($step, $array_step))
        echo ' - Étape '.$array_step[$step];
}

?>

</h1>

<hr />
<?php

function get_html_result($var, $fatal = false) {
    global $error, $notice;
    if ($var) {
        $ret = '<font color="green">Ok</font>';
    } else {
        if ($fatal) {
            $error++;
            $ret = '<font color="red">Non (Indispensable !)</font>';
        } else {
            $notice++;
            $ret = '<font color="red">Non</font>';
        }
    }
    echo $ret;
}

function test_version_php($ver_min) {
    $ret = false;
    $ver = substr(phpversion(), 0, 5);
    $ver = '4.1.0';
    echo $ver.' (version minimale : '.$ver_min.') : ';
    if (strcmp($ver_min, $ver) < 0) {
        $ret = true;
    }
    return $ret;
}

function test_config() {
    $ret = true;
    if (!extension_loaded('mysql')
        || !extension_loaded('session')
        || !is_writable(DIR_ROOT.DIR_CONF)
        || !is_writable(DIR_ROOT.FILE_INI)
        || !is_writable(DIR_ROOT.DIR_CACHE)
        || !is_writable(DIR_ROOT.DIR_ANON))
        $ret = false;
    return $ret;
}

define('VERSION_PHP_MIN',   '4.0.4');
define('VERSION_PHP_FULL',  '4.2.0');

$step = @$_REQUEST['step'];
switch ($step) {

    #   Test de la connection Sql
    case 'sqltest':

        $sql_host = $_REQUEST['sql_host'];
        $sql_port = $_REQUEST['sql_port'];
        $sql_user = $_REQUEST['sql_user'];
        $sql_base = $_REQUEST['sql_base'];
        $sql_pass = $_REQUEST['sql_pass'];

        $sql_host = ($sql_port) ? $sql_host.':'.$sql_port : $sql_host;

?>
    <h3>Test de connection au serveur Sql &laquo; <?php echo htmlentities($sql_host); ?> &raquo;  </h3>

    <p>
        Tentative de connection au serveur <b><?php echo $sql_host; ?></b> avec l'utilisateur <b><?php echo $sql_user; ?></b>
        sur la base de données <b><?php echo $sql_base; ?></b>.
    </p>


<?php
        $error = 0;

        $bdd =& new db();
        echo 'Création de l\'objet pour la connection : ';
        echo get_html_result($bdd, true).'<br />';
        if ($bdd) {
            $cnt = $bdd->connect($sql_host, $sql_user, $sql_pass);
            echo 'Connection au serveur Sql : ';
            echo get_html_result($cnt, true).'<br />';

            if ($cnt) {
                $slt = $bdd->select($sql_base);
                echo 'Sélection de la base de données Sql : ';
                echo get_html_result($slt, true).'<br />';
            }
        }

        if ($error) {
?>
    <br />
    <blockquote class="error">
        L'installation ne peut pas continuer à cause d'erreur(s), reportez vous au rapport
        ci-dessus pour solutionner le problème.
    </blockquote>
<?php
        }

?>
    <hr />

    <a href="#" onclick="window.close();">Fermer</a>
<?php

        break;

    #   Test du chemin
    case 'dirtest':
        $dirtest = $_REQUEST['dir'];
?>

    <h3>Test du chemin &laquo; <?php echo htmlentities($dirtest); ?> &raquo; </h3>

<?php
        if (file_exists($dirtest)) {

            if (file::dirName($_SERVER['SCRIPT_FILENAME']) == $dirtest) {
?>
    <p class="remarque">
        <strong>
            Vous avez laissé le dossier d'installation de Hyla, prenez garde à ceci car
            un visiteur mal intentionné pourrait explorer l'arborescence de Hyla et découvrir
            les informations contenues dans le fichier « conf/config.inc.php »
        </strong>
    </p>
<?php
            }
?>
    <p>
        Lecture : <?php get_html_result(is_readable($dirtest), true) ?>
    </p>

    <p>
        Écriture : <?php get_html_result(is_writable($dirtest)) ?>
    </p>

<?php
        } else {
?>
    <p>
        <font color="red">Le dossier que vous avez saisi n'existe pas !</font>
    </p>
<?php
        }
?>
    <hr />

    <a href="#" onclick="window.close();">Fermer</a>
<?php
        break;

    #   Vérification de la configuration du serveur
    case '2':
?>
    <h2>Vérification de la configuration du serveur</h2>

    <h3>Version :</h5>

    <p class="remarque">
        Hyla utilise des fonctions indispensables à son bon fonctionnement, malheureusement, ces fonctions ne sont pas toujours disponibles selon les versions de Php,
        voici pourquoi, il existe une version de Php minimale de <?php echo VERSION_PHP_MIN; ?> et une version Php recommandée de <?php echo VERSION_PHP_FULL; ?>.
    </p>

    <ul>
        <li>Php : <?php echo get_html_result(test_version_php(VERSION_PHP_MIN), true); ?></li>
    </ul>

    <h3>Configuration Php :</h5>
    <ul>
        <li>Téléchargement de fichiers distants ( <em>allow_url_fopen</em> ) : <strong><?php get_html_result(ini_get('allow_url_fopen')); ?></strong></li>
        <li>Autorise ou non le téléchargement de fichier sur le serveur ( <em>file_uploads</em> ) : <strong><?php get_html_result(ini_get('file_uploads')); ?></strong></li>
        <li>Taille maximale acceptée d'un fichier envoyé sur le serveur ( <em>upload_max_filesize</em> ) : <strong><?php echo ini_get('upload_max_filesize'); ?></strong></li>
    </ul>

    <h3>Extensions :</h5>
    <ul>
        <li>Bibliothèque Mysql (Gestionnaire de base de données) : <strong><?php get_html_result(extension_loaded('mysql'), true); ?></strong>
        <li>Bibliothèque SESSION (gestion des sessions) : <strong><?php get_html_result(extension_loaded('session'), true); ?></strong>
<?php
if ($host_server == 'free')
    echo '<blockquote class="info"><strong>Attention</strong>, pour les utilisateurs de free.fr, n\'oubliez pas de créer à la racine de votre site un dossier nommé "sessions"</blockquote>';
?>
        </li>
        <li>Bibliothèque GD (pour manipuler les images) : <strong><?php get_html_result(extension_loaded('gd')); ?></strong></li>
        <li>Bibliothèques EXIF (pour lire les données EXIF contenu dans certaine image) : <strong><?php get_html_result(extension_loaded('exif')); ?></strong></li>
    </ul>

    <h3>Droits en écriture :</h5>

    <p class="remarque">
        Les dossiers et fichiers suivants doivent être accessibles en écriture par le serveur web afin que Hyla puisse écrire dans ces derniers.
    </p>

    <ul>
        <li>Dossier de configuration ( &laquo; <?php echo DIR_CONF; ?> &raquo; ) : <strong><?php get_html_result(is_writable(DIR_ROOT.DIR_CONF), true); ?></strong></li>
        <li>Fichier conf ( &laquo; <?php echo FILE_INI; ?> &raquo; ) : <strong><?php get_html_result(is_writable(DIR_ROOT.FILE_INI), true); ?></strong></li>
        <li>Cache ( &laquo; <?php echo DIR_CACHE; ?> &raquo; ) : <strong><?php get_html_result(is_writable(DIR_ROOT.DIR_CACHE), true); ?></strong></li>
        <li>Fichiers anonymes ( &laquo; <?php echo DIR_ANON; ?> &raquo; ) : <strong><?php get_html_result(is_writable(DIR_ROOT.DIR_ANON), true); ?></strong></li>
    </ul>

<?php
if ($host_server == 'free')
    echo '<blockquote class="info"><strong>Attention</strong>, pour les utilisateurs de free.fr, il vous sera impossible de supprimer des dossiers car free empêche l\'exécution de la fonction rmdir, vous devrez pas conséquent le faire par vos propres moyens, par exemple, grâce à un programme ftp.</blockquote>';
?>

<?php

if ($notice) {
?>
    <blockquote class="info">
        Bien qu'Hyla puisse fonctionner comme même, il semble que votre configuration n'est pas adéquate,
        pour profitez pleinement des fonctionnalités de Hyla, veuillez si possible,
        mettre à jour votre configuration ou contactez votre administrateur !
    </blockquote>
<?php
}

if ($error) {
?>
    <blockquote class="error">
        Un ou des élément(s) indispensable(s) au bon fonctionnement d'Hyla ou un problème de droits empêchent son installation !
        <br />
        Veuillez vous référer aux messages ci-dessus pour résoudre le problème !
    </blockquote>
<?php
}

if (file_exists(CONF_FILE))
    echo '<blockquote class="info"><strong>Attention</strong>, Le fichier de configuration ('.CONF_FILE.' ) est présent, il sera supprimé plus tard.</blockquote>';

?>
    <hr />

    <p>
<?php

if (!test_config())
    echo '<a href="?step=2">Réessayer</a>';
else
    echo '<a href="?step=3">Poursuivre l\'installation</a>';
?>
    </p>
<?php
        break;

    #   Type d'installation
    case '3':

        if (file_exists(CONF_FILE)) {
            if (!unlink(CONF_FILE)) {
?>
    <blockquote class="error">
        Impossible de supprimer le fichier de configuration ( <?php echo CONF_FILE; ?> ) !
    </blockquote>

    <hr />

    <a href="?step=3">Réessayer</a>
<?php
                break;
            }
        }
?>
    <h2>Type d'installation</h2>

    <p>
        Deux types d'installation vous sont proposées :
    </p>

    <p>
        <a href="?step=4">Une installation standard</a> : Hyla va s'installer normalement après avoir spécifié les informations nécessaires.
    </p>

    <p>
        <p class="remarque">
            <u>Remarque :</u>
            Hyla <?php echo HYLA_VERSION; ?>  ne propose pas d'assitant de migration depuis une version 0.8.0 car
            la structure de la base de données n'a pas évoluée.
        </p>
    </p>

    <hr />

<?php
        break;

    #   Installation standard
    case '4':

        if (test_config()) {
?>
    <h2>Saisi des informations</h2>

    <form method="post" name="form_conf" action="?step=5">

        <h3>Dossier contenant les fichiers à partager :</h3>
        <p>
            <input name="folder_root" id="folder_root" size="100" maxlength="255" value="<?php echo file::dirName($_SERVER['SCRIPT_FILENAME']); ?>" type="text" />
        </p>
        <a href="#" onclick="this.href='?step=dirtest&amp;dir=' + document.forms['form_conf'].elements['folder_root'].value; popup(this.href); return false;" title="Vous permet de vous assurer que le chemin est bien accessible par Hyla">Tester le dossier de partage</a>
        <p class="help">
            Il s'agit du chemin complet depuis la racine sans slash ( / ) de fin (Ex: /var/www/data)
        </p>

        <p class="remarque">
            <strong>
                Il est vivement recommandé de <span style="color: red">ne pas laisser le dossier de Hyla en partage</span> car un visiteur mal intentionné
                pourrait explorer l'arborescence de Hyla et découvrir les informations contenues dans le fichier &laquo; conf/config.inc.php &raquo;
            </strong>
        </p>

        <p class="remarque">
            Si vous décidez par la suite de changer de dossier, éditez le fichier &laquo; conf/config.inc.php &raquo; et modifiez la constante FOLDER_ROOT.
        </p>


        <h3>Connection à la base de données :</h3>
        <p>
            <label for="sql_host">Serveur :</label>
            <input name="sql_host" id="sql_host" size="20" maxlength="255" value="<?php echo $sql_server; ?>" type="text" />

            <label for="sql_port">Port (par défaut, laissez vide) :</label>
            <input name="sql_port" id="sql_port" size="5" maxlength="8" value="<?php echo $sql_port; ?>" type="text" />
        </p>
        <p>
            <label for="sql_user">Utilisateur :</label>
            <input name="sql_user" id="sql_user" size="20" maxlength="255" value="<?php echo $sql_user; ?>" type="text" />
        </p>
        <p>
            <label for="sql_base">Base de données :</label>
            <input name="sql_base" id="sql_base" size="20" maxlength="255" value="<?php echo $sql_base; ?>" type="text" />
        </p>
        <p>
            <label for="sql_pass">Mot de passe :</label>
            <input name="sql_pass" id="sql_pass" size="20" maxlength="255" value="" type="password" />
        </p>
        <p>
            <label for="sql_pass_bis">Répétez le mot de passe :</label>
            <input name="sql_pass_bis" id="sql_pass_bis" size="20" maxlength="255" value="" type="password" />
        </p>

        <p>
            <a href="#" onclick="this.href=test_sql(); popup(this.href); return false;" title="Vous permet de tester la connection avec le serveur Sql">Tester la connection au serveur Sql</a>
        </p>


        <p class="remarque">
            Avant de cliquer sur le bouton ci-dessous, veuillez vérifier si les champs saisis sont valides en cliquant sur les liens de tests appropriés.
        </p>
        <input type="submit" name="Submit" value="Valider les informations saisies" />
    </form>

    <hr />

<?php
        }
        break;

    #   Création du fichier de configuration
    case '5':

        if (test_config()) {

            $folder_root = $_POST['folder_root'];

            $sql_host = $_POST['sql_host'];
            $sql_port = $_POST['sql_port'];
            $sql_user = $_POST['sql_user'];
            $sql_pass = $_POST['sql_pass'];
            $sql_pass_bis = $_POST['sql_pass_bis'];
            $sql_base = $_POST['sql_base'];

            // Surtout pas de / en fin de chaine !
            $size = (strlen($folder_root) - 1);
            if ($folder_root{$size} == '/') {
                $folder_root = substr($folder_root, 0, $size);
            }

            $var_file = get_config_content($folder_root, $sql_host, $sql_base, $sql_user, $sql_pass, null, $sql_port);

?>
    <h2>Test de connection au serveur Sql &laquo; <?php echo htmlentities($sql_host); ?> &raquo;  </h2>

    <p>
        Tentative de connection au serveur <b><?php echo $sql_host; ?></b> avec l'utilisateur <b><?php echo $sql_user; ?></b>
        sur la base de données <b><?php echo $sql_base; ?></b>.
    </p>


<?php
            $error = 0;
            if($sql_pass != $sql_pass_bis) {
                echo view_error(__('Passwords are different'));
                $error = 1;
            } else {
                $bdd =& new db();
                echo 'Création de l\'objet pour la connection : ';
                echo get_html_result($bdd, true).'<br />';
                if ($bdd) {
                    $cnt = $bdd->connect($sql_host, $sql_user, $sql_pass);
                    echo 'Connection au serveur Sql : ';
                    echo get_html_result($cnt, true).'<br />';

                    if ($cnt) {
                        $slt = $bdd->select($sql_base);
                        echo 'Sélection de la base de données Sql : ';
                        echo get_html_result($slt, true).'<br />';
                    }
                }
            }

            if ($error) {
?>
    <br />
    <blockquote class="error">
        L'installation ne peut pas continuer à cause d'erreur(s), reportez vous au rapport
        ci-dessus pour solutionner le problème.
    </blockquote>
    <hr />

    <a href="?step=4">Revenir en arrière.</a>
<?php
            } else {
?>
    <h2>Création du fichier de configuration ( <?php echo CONF_FILE; ?> )</h2>

<?php

                $file_conf = false;

                // Si le fichier de conf existe, on le supprime
                if (file_exists(CONF_FILE))
                    unlink(CONF_FILE);

                if (!file_exists(CONF_FILE) && file::putContent(CONF_FILE, $var_file))
                    $file_conf = true;

                if ($file_conf) {
?>
    <p>
        Ecriture du fichier de configuration (<strong><?php echo CONF_FILE; ?></strong>) <?php get_html_result(true); ?></li>
    </p>
<?php
                } else {
?>
    </ul>
    <blockquote class="error">
        Erreur durant la création du fichier de configuration (<strong><?php echo CONF_FILE; ?></strong>) : le fichier existe peut être déjà !
    </blockquote>

    <hr />

    <a href="?step=4">Revenir en arrière.</a>
<?php
                    break;
                }
?>

    <p>
        <a href="#" onclick="swap_layer('conf_file');">Voir le contenu</a> du fichier de configuration ( <?php echo CONF_FILE; ?> )
    </p>

    <div id="conf_file" class="jhidden">
<?php

highlight_string($var_file);

?>
    </div>

    <hr />

    <p>
        <a href="?step=6">Suite (Création des tables et insertions des données)</a>
    </p>
<?php
            }
        }
        break;

    #   Création des tables et insertion des données
    case '6':
?>
    <h2>Création des tables et insertions des données</h2>
<?php

        include CONF_FILE;
        $bdd =& new db();
        if ($bdd->connect(SQL_HOST, SQL_USER, SQL_PASS) && $bdd->select(SQL_BASE)) {

$var_query[0]['desc'] = 'Création de la table &laquo; '.$prefix.'acontrol &raquo; ';
$var_query[0]['query'] = "
CREATE TABLE `".$prefix."acontrol` (
  `ac_obj_id` int(4) unsigned NOT NULL,
  `ac_usr_id` int(4) unsigned NOT NULL,
  `ac_rights` int(10) unsigned NOT NULL default '0',
  UNIQUE KEY `ac_obj_id` (`ac_obj_id`,`ac_usr_id`)
);";

$var_query[1]['desc'] = 'Création de la table &laquo; '.$prefix.'comment &raquo; ';
$var_query[1]['query'] = "
CREATE TABLE `".$prefix."comment` (
  `comment_id` int(4) unsigned NOT NULL auto_increment,
  `comment_obj_id` int(4) unsigned NOT NULL,
  `comment_author` char(255) NOT NULL default '',
  `comment_mail` char(255) NOT NULL default '',
  `comment_url` char(255) NOT NULL default '',
  `comment_date` int(10) unsigned NOT NULL default '0',
  `comment_content` text,
  PRIMARY KEY  (`comment_id`)
);";

$var_query[2]['desc'] = 'Création de la table &laquo; '.$prefix.'group_user &raquo; ';
$var_query[2]['query'] = "
CREATE TABLE `".$prefix."group_user` (
  `grpu_usr_id` int(4) unsigned NOT NULL,
  `grpu_grp_id` int(4) unsigned NOT NULL,
  UNIQUE KEY `GRP_USR` (`grpu_usr_id`,`grpu_grp_id`)
);";

$var_query[3]['desc'] = 'Création de la table &laquo; '.$prefix.'object &raquo; ';
$var_query[3]['query'] = "
CREATE TABLE `".$prefix."object` (
  `obj_id` int(4) unsigned NOT NULL auto_increment,
  `obj_file` blob,
  `obj_date_last_update` int(10) unsigned NOT NULL default '0',
  `obj_description` text,
  `obj_plugin` char(255) NOT NULL default '',
  `obj_icon` char(255) default NULL,
  `obj_dcount` int(4) unsigned NOT NULL default '0',
  `obj_flag` int(4) unsigned NOT NULL default '0',
  `obj_id_ref` int(4) unsigned NULL default '0',
  PRIMARY KEY  (`obj_id`)
);";

$var_query[4]['desc'] = 'Création de la table &laquo; '.$prefix.'users &raquo; ';
$var_query[4]['query'] = "
CREATE TABLE `".$prefix."users` (
  `usr_id` int(4) unsigned NOT NULL auto_increment,
  `usr_name` char(32) NOT NULL,
  `usr_password_hash` char(255) NOT NULL default '',
  `usr_type` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`usr_id`),
  UNIQUE KEY `usr_name` (`usr_name`)
);";

$var_query[5]['desc'] = 'Insertion dans la table &laquo; '.$prefix.'users &raquo; de l\'utilisateur &laquo; Any &raquo;';
$var_query[5]['query'] = "INSERT INTO `".$prefix."users` VALUES (1, 'Any', '', 0);";

$var_query[6]['desc'] = 'Insertion dans la table &laquo; '.$prefix.'users &raquo; de l\'utilisateur &laquo; Authenticated &raquo;';
$var_query[6]['query'] = "INSERT INTO `".$prefix."users` VALUES (2, 'Authenticated', '', 0);";

$var_query[7]['desc'] = 'Insertion dans la table &laquo; '.$prefix.'users &raquo; de l\'utilisateur &laquo; Anonymous &raquo;';
$var_query[7]['query'] = "INSERT INTO `".$prefix."users` VALUES (3, 'Anonymous', '', 0);";

$var_query[8]['desc'] = 'Insertion dans la table &laquo; '.$prefix.'object &raquo; de la racine &laquo; / &raquo;';
$var_query[8]['query'] = "INSERT INTO `".$prefix."object` (`obj_id`, `obj_file`, `obj_date_last_update`, `obj_description`, `obj_plugin`, `obj_icon`, `obj_dcount`, `obj_flag`, `obj_id_ref`) VALUES (1, '/', 0, NULL, '', NULL, 0, 0, 0);";

$var_query[9]['desc'] = 'Insertion dans la table &laquo; '.$prefix.'acontrol &raquo; du droit de la racine';
$var_query[9]['query'] = "INSERT INTO `".$prefix."acontrol` (`ac_obj_id`, `ac_usr_id`, `ac_rights`) VALUES (1, 1, 0);";


            echo '<ul>';

            // Création des tables...
            $error = 0;
            foreach ($var_query as $q) {
                echo '<li>'.$q['desc'].' ';
                if (!$var = $bdd->execQuery($q['query'])) {
                    $ret = $bdd->getError();
                    $msg = $ret['message'];
                    $error++;
                } else
                    $msg = null;
                    echo ' : ';
                    echo get_html_result($var);

                    if ($msg)
                        echo ' (<i>'.$msg.'</i>)';

                echo '</li>';
            }

            $bdd->close();
            echo '</ul>';

        } else {
?>
    <blockquote class="error">
        Erreur durant la connection au serveur sql !
    </blockquote>
<?php
        }
?>
    <hr />

    <p>
        <a href="?step=7">Suite (Création de l'utilisateur principal)</a>
    </p>
<?php
        break;

    #   Création d'un utilisateur
    case '7':

        $ok = 0;

        $username = null;
        $password = null;
        $password_bis = null;

        if (@$_POST['Submit']) {

            $error = false;

            include CONF_FILE;

            $bdd =& new db();
            if ($bdd->connect(SQL_HOST, SQL_USER, SQL_PASS) && $bdd->select(SQL_BASE)) {
                $username = trim($_POST['usr_login']);
                $password = trim($_POST['usr_password']);
                $password_bis = trim($_POST['usr_password_bis']);

                $usr = new users();

                $msg_error = null;
                $ret = $usr->testLogin($username);
                if ($ret == -1) {
                    $msg_error = view_error(__('The name is invalid !'));
                    $username = null;
                } else if (!$ret) {
                    $msg_error = view_error(__('User already exists !'));
                    $username = null;
                } else if (empty($password))
                    $msg_error = view_error(__('All the fields must be filled'));
                else if (strlen($password) < MIN_PASSWORD_SIZE)
                    $msg_error = view_error(__('Password must have at least %s characters !', MIN_PASSWORD_SIZE));
                else if ($password != $password_bis)
                    $msg_error = view_error(__('Passwords are different'));
                else {
                    $ok = $usr->addUser($username, $password);
                    $usr->setType($ok, USR_TYPE_ADMIN);
                }

            } else {
                $error = true;
            }
        }
?>
    <h2>Création de l'utilisateur principal</h2>

    <p class="remarque">
        L'utilisateur créé ici aura le status d'administrateur, il aura donc les droits d'administrer totalement Hyla.
    </p>

<?php
if ($msg_error) {
    echo $msg_error;
}

if (!$ok) {
?>
    <form method="post" name="form_user" action="?step=7">
        <fieldset>
            <legend>Veuillez remplir les champs suivants :</legend>

            <label for="usr_login">Nom d'utilisateur :</label>
            <input name="usr_login" id="usr_login" size="20" maxlength="32" value="<?php echo $username; ?>" type="text" />
            <p class="help">
                Toutes les lettres de l'alphabet sont acceptées ainsi que les chiffres,
                le trait d'union (-) et le tiret bas (_), attention tout de même,
                le nom doit commencer par une lettre et est limité à 32 caractères.
            </p>
            <p>
                <label for="usr_password">Mot de passe :</label>
                <input name="usr_password" id="usr_password" size="20" maxlength="32" value="<?php echo $password; ?>" type="password" />
            </p>
            <p>
                <label for="usr_password_bis">Répétez le mot de passe :</label>
                <input name="usr_password_bis" id="usr_password_bis" size="20" maxlength="32" value="<?php echo $password_bis; ?>" type="password" />
            </p>
            <input type="submit" name="Submit" value="Créer" />
        </fieldset>
    </form>

    <hr />

<?php
} else {
?>
    <ul>
        <li>Ajout de l'utilisateur &laquo; <?php echo $username; ?> &raquo; : <?php get_html_result(true); ?></li>
    </ul>
    <hr />
    <p>
        <a href="?step=8">Fin de l'installation</a>
    </p>
<?php
}
        break;

    #   Fin de l'installation
    case '8':
?>
    <h3>Fin de l'installation !</h3>
<?php
        /*  On essaie de détecter le type du charset du système de fichiers en se basant sur le fait que sous Win,
            on n'est pas en UTF8, enfin, normalement...
         */
        $val = system::osIsWin() ? 'false' : 'true';
        $ini = new iniFile(FILE_INI);
        $ini->editVar('fs_charset_is_utf8', $val);
        if (!$ini->saveFile()) {
?>
    <blockquote class="error">
        Impossible de modifier le fichier de configuration ( <?php echo FILE_INI; ?> ) !
    </blockquote>
<?php
        }

        // Création du fichier verrou
        if (!file::putContent(LOCK_FILE, 'locked!')) {
?>
    <blockquote class="error">
        Impossible de créer le fichier de verrou ( <?php echo LOCK_FILE; ?> ), Hyla ne pourra pas s'exécuter, essayez de le créer manuellement.
    </blockquote>
<?php
        }

?>

    <p>
        Hyla est maintenant installé !
        <br />
        Vous pouvez vous connecter dans <a href="index.php?p=page-admin">l'administration</a> et finir de paramétrer Hyla.
    </p>

    <p>
        Et n'oubliez pas :
    </p>

    <ul>
        <li><a href="doc/fr-FR/install.htm" onclick="popup(this.href); return false;" title="Documentation">Documentation locale</a></i>
        <li><a href="http://www.hyla-project.org/Hyla_Rapport_Opquast.pdf">Le site officiel de Hyla</a>
        <li><a href="http://www.hyla-project.org/doc">La documentation en ligne</a>
        <li><a href="http://www.hyla-project.org/forums/">Les forums de discussions</a>
    </ul>

    <hr />
<?php
        break;

    #   Présentation
    case '1':
    default:
?>
    <h2>Bienvenue dans l'installeur d'Hyla !</h2>

    <p>
        Merci d'avoir choisi Hyla, un gestionnaire de fichiers, simple, léger, extensible, capable de générer des galeries photos et bien plus encore...
        <br />
        Hyla est notamment respectueux des standards en vigueur sur le web.
    </p>

    <h3>En cas de problème ou pour toute question, voici les liens qui vous seront utile :</h3>

    <ul>
        <li>La <a href="doc/fr-FR/index.htm" onclick="popup(this.href); return false;">documentation locale</a></li>
        <li>La <a href="http://www.hyla-project.org/doc">documentation en ligne</a></li>
        <li>N'hésitez pas à faire part de votre problème quel qu'il soit concernant Hyla sur <a href="http://www.hyla-project.org/forums/">les forums de discussions</a>.</li>
    </ul>

    <h3>Vous souhaitez contribuer ?</h3>

    <p class="remarque">
        Hyla recherche des traducteurs pour sa future version, si vous êtes intéressé, signalez-vous
        sur <a href="http://www.hyla-project.org/forums/">un des forums de discussions.</a>
        <br />
        Merci d'avance !
    </p>

    <p>
        Testeurs, Traducteurs, Développeurs, vous pouvez participer au développement de Hyla de plusieurs manières :
    </p>

    <ul>
        <li>En participant aux <a href="http://www.hyla-project.org/forums/">forums</a></li>
        <li>En intégrant la liste de diffusion de développement (voir forums...)</li>
    </ul>

    <h3>Vous appréciez Hyla ?</h5>

    <p>
        N'hésitez pas à le dire, ça fait plaisir et si Hyla vous plait vraiment beaucoup, vous pouvez toujours consulter <a href="http://www.hyla-project.org/donate">cette page</a>.
    </p>


    <hr />
    <p>
        <a href="?step=2">Commencer l'installation</a>
    </p>
<?php
        break;
}


?>

<p>
    <a href="doc/fr-FR/install.htm" onclick="popup(this.href); return false;" title="Documentation"><img src="tpl/default/img/help.png" alt="Point d'interrogation" /> </a>
</p>



</body>

</html>
<?php

if (class_exists('system')) {
    system::end();
}

?>
