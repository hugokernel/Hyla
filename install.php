<?php

if (basename($_SERVER['PHP_SELF']) != 'install.php') {
	exit('Par mesure de sécurité, ce fichier doit être nommé install.php pour pouvoir être exécuté !');
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<title>iFile installeur</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<style>

body {
	font-family: Verdana, Arial, Helvetica, sans-serif;
}

/*	Les formulaires
 */
textarea{
	width: 100%;
	border: 1px solid #CCC;
/*	padding: 0px;*/
	background: #FFE;
}

textarea:focus {
	background: #FFF;
}

input {
	border: 1px solid #CCC;
	padding: 3px;
	background: #FFE;
}

input:focus {
	background: #FFF;
}

input[type=submit] {
	border: 1px solid #999;
	padding: 1px;
	background: #CCC;
}

input[type=submit]:hover {
	border: 1px solid #999;
	padding: 1px;
	background: #DDD;
}

</style>

</head>

<body>
<h1>Installation de "iFile"</h1>
<hr />
<?php

error_reporting(E_ERROR);

define('ERROR_REPORT', 2);

require 'src/inc/define.php';

require_once 'src/inc/file.class.php';

$conf_file = 'conf/config.inc.php';
$dir_conf = 'conf/';


$msg_error = null;

$etape = (int)@$_REQUEST['etape'];

if (!$etape || $etape == 1) {
?>

<h2>1. Bienvenue !</h2>

<p>
	Ce script vous permet une installation simplifiée de <a href="http://www.digitalspirit.org/wiki/projets/ifile">iFile</a>...
</p>
<p>
	Au lieu d'exécuter ce script, vous pouvez tout aussi bien éditez vous même le fichier de configuration par défaut (<strong> conf/config.inc.default.php </strong>) et le renommez ensuite config.inc.php, dans ce cas, n'oubliez pas de créer les tables de la base de données grâce au script sql (<strong> src/db/mysql.sql </strong>)
</p>

<?php
if (preg_match('#\.free\.fr#i', $_SERVER['SERVER_NAME'])) {
	echo '<h4>Attention, pour les utilisateurs de free.fr, n\'oubliez pas de créer à la racine de votre site un répertoire nommé "sessions"</h4>';
}
?>

<hr />
<?php

if (file_exists($conf_file)) {
	echo "<p>Impossible d'aller plus loin, merci de supprimer le fichier de configuration (<strong> $conf_file </strong>)</p>";
	echo '</body></html>';
} else {
	echo '<a href="?etape=2">Etape suivante</a> (2. Vérification de la configuration du serveur)';
}

?>

<?php
} else if ($etape == 2) {

?>
<h2>2. Vérification de la configuration du serveur</h2>

<h3>Lecture de la configuration du serveur :</h3>
<?php

$auploadfile = (ini_get('file_uploads')) ? '<span style="color: green">OK</span> ( '.ini_get('upload_max_filesize').' maxi par fichier )' : '<span style="color: red">NON</span> (Mettez le paramètre "file_uploads" du fichier php.ini à On)';
$aurlfopen = (ini_get('allow_url_fopen')) ? '<span style="color: green">OK</span>' : '<span style="color: red">NON</span> (Mettez le paramètre "allow_url_fopen" du fichier php.ini à <i>On</i> si vous voulez pouvoir télécharger des fichiers distants)';

echo 'Téléchargement de fichiers : '.$auploadfile.'<br>';
echo 'Téléchargement de fichiers distants : '.$aurlfopen.'<br>';

?>

<h3>Vérification des droits d'accès :</h3>
<?php

$dir_cache = (is_writable(FOLDER_CACHE)) ? '<span style="color: green">OK</span>' : '<span style="color: red">NON</span> (Le répertoire <strong>'.FOLDER_CACHE.'</strong>  doit être accessible en écriture par le serveur web)';
//$file_config_inc_php = (!file_exists($conf_file)) ? '<span style="color: green">OK</span>' : '<span style="color: red">NON</span> (Le fichier <strong>'.$conf_file.'</strong> doit être supprimé pour pouvoir continuer !)';
$dir_conf_status = (is_writable($dir_conf)) ? '<span style="color: green">OK</span>' : '<span style="color: red">NON</span> (Le répertoire <strong>'.$dir_conf.'</strong>  doit être accessible en écriture par le serveur web)';

echo '<p>Ecriture dans le répertoire de cache (<strong>cache/</strong>) : '.$dir_cache.'</p>';
//echo '<p>Ecriture dans le fichier de configuration (<strong>'.$conf_file.'</strong>) : '.$file_config_inc_php.'</p>';
echo '<p>Ecriture dans le répertoire de configuration (<strong>'.$dir_conf.'</strong>) : '.$dir_conf_status.'</p>';

if (!is_writable(FOLDER_CACHE) || file_exists($conf_file) || !is_writable($dir_conf)) {
	echo 'Il est impossible de passer à l\'étape suivante : vérifiez les droits en écriture du répertoire <strong>'.FOLDER_CACHE.'</strong>, de répertoire <strong>'.$dir_conf.'</strong> et qu\'il n\'existe pas de fichier <strong>'.$conf_file.'</strong> !';
} else {
?>
<hr />
<a href="?">Retour en arrière</a> (1. Bienvenue) | <a href="?etape=3">Etape suivante</a> (3. Saisi de la configuration)
<?php
}

} else if ($etape == 3) {
/*
echo '<pre>';
print_r($_SERVER);
echo '</pre>';
*/

	if (!is_writable(FOLDER_CACHE) || file_exists($conf_file) || !is_writable($dir_conf)) {
?>
<a href="?etape=2">Impossible d'aller plus loin, retournez à l'étape précédente</a> (2. Vérification de la configuration du serveur)
<?php
	} else {

?>

<h2>3. Saisi de la configuration</h2>

<p>
	Une fois l'installation effectuée, vous pourrez toujours modifier la configuration en éditant le fichier <strong>conf/config.inc.php</strong>
</p>

<form method="post" name="form_user" action="?etape=4">
<fieldset>
	<legend>Répertoire contenant les fichiers à partager :</legend>
	<p>
		<em>Il s'agit du chemin complet depuis la racine sans slash ( / ) de fin</em>
	</p>
	<p>
		<input name="folder_root" id="folder_root" size="100" maxlength="255" value="<?php echo dirname($_SERVER['SCRIPT_FILENAME']); ?>" type="text" />
	</p>
	<p>
		Ex:	/var/www/file
	</p>
</fieldset>
<br />
<fieldset>
	<legend>L'emplacement de ifile après le nom de domaine :</legend>

	<p>
		<input name="root_url" id="root_url" size="100" maxlength="255" value="
<?php

$root_url = dirname($_SERVER['PHP_SELF']);
$size = (strlen($root_url) - 1);
if ($root_url{$size} == '/') {
	$root_url = substr($root_url, 0, $size);
}

?>" type="text" />
	</p>
	<p>	Ex: http://ifile.free.fr/				-> ne mettez rien <br />
		Ex: http://ifile.free.fr/ifile/			-> mettez /ifile<br />
		Ex: http://ifile.free.fr/data/ifile		-> mettez /data/ifile
	</p>
</fieldset>
<br />
<fieldset>
	<legend>Connection à la base de données :</legend>
	<p>
		<label for="sql_host">Serveur :</label>
		<input name="sql_host" id="sql_host" size="20" maxlength="255" value="" type="text" />
	</p>
	<p>
		<label for="sql_user">Utilisateur :</label>
		<input name="sql_user" id="sql_user" size="20" maxlength="255" value="" type="text" />
	</p>
	<p>
		<label for="sql_pass">Mot de passe :</label>
		<input name="sql_pass" id="sql_password" size="20" maxlength="255" value="" type="text" />
	</p>
	<p>
		<label for="sql_base">Base de données :</label>
		<input name="sql_base" id="sql_base" size="20" maxlength="255" value="" type="text" />
	</p>
</fieldset>
<br />
<fieldset>
	<legend>Login et mot de passe :</legend>
	<p>
		<label for="login">Login :</label>
		<input name="login" id="login" size="20" maxlength="255" value="" type="text" />
	</p>
	<p>
		<label for="password">Mot de passe :</label>
		<input name="password" id="login_password" size="20" maxlength="255" value="" type="text" />
	</p>
</fieldset>
<br />
<input type="submit" name="Submit" value="Envoyer" />

</form>

<hr />
<a href="?etape=2">Retour en arrière</a> (2. Vérification de la configuration du serveur)

<?php
	}

} else if ($etape == 4) {

	$folder_root = $_POST['folder_root'];
	$root_url = $_POST['root_url'];

	$sql_host = $_POST['sql_host'];
	$sql_user = $_POST['sql_user'];
	$sql_pass = $_POST['sql_pass'];
	$sql_base = $_POST['sql_base'];
	
	$login = $_POST['login'];
	$password = $_POST['password'];

	// Surtout pas de / en fin de chaine !
	$size = (strlen($folder_root) - 1);
	if ($folder_root{$size} == '/') {
		$folder_root = substr($folder_root, 0, $size);
	}
	$size = (strlen($root_url) - 1);
	if ($root_url{$size} == '/') {
		$root_url = substr($root_url, 0, $size);
	}

$var_file = 
"<?php
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


/*	+----------------------------------------------------+
 	| Répertoire contenant vos fichiers à lister         |
 	| /!\ ATTENTION, pas de slash ou anti slash de fin ! |
 	+----------------------------------------------------+
 */
define('FOLDER_ROOT', '$folder_root');


/*	L'emplacement de ifile après le nom de domaine (sans slash de fin !)
	Ex: http://ifile.free.fr/				-> mettez ''
	Ex: http://ifile.free.fr/ifile/			-> mettez '/ifile'
	Ex: http://ifile.free.fr/data/ifile		-> mettez '/data/ifile'
 */
define('ROOT_URL', '$root_url');


/*	+---------------------------------+
	| Connection à la base de données |
	+---------------------------------+
 */
define('SQL_HOST',	'$sql_host');
define('SQL_USER',	'$sql_user');
define('SQL_PASS',	'$sql_pass');
define('SQL_BASE',	'$sql_base');


/*	+----------------------------------------------------+
	| Pour pouvoir ajouter des fichiers, éditer...etc... |
	+----------------------------------------------------+
 */
define('LOGIN', '$login');
define('PASSWORD', '$password');

?>";
?>

<p>
<?php

if (!file_exists($conf_file) && file::putContent($conf_file, $var_file)) {
?>
	<p>
		Le contenu de votre fichier de configuration (<strong><?php echo $conf_file; ?></strong>) à correctement été écrit !
	</p>
<?php
} else {
?>
	<p>
		Erreur durant la création du fichier de configuration (<strong><?php echo $conf_file; ?></strong>) : le fichier existe peut être déjà !
	</p>
<?php
}

?>
</p>

<hr />
<a href="?etape=5">Etape suivante</a> (Création des tables)

<pre>
<?php highlight_string($var_file); ?>
</pre>

<?php		

} else if ($etape == 5) {

?>
	<h3>Création des tables</h3>

<?php

	require 'conf/config.inc.php';

	require 'src/inc/function.inc.php';
	require 'src/inc/system.class.php';

	require 'src/db/mysql.class.php';

	// Connection à la base
	$bdd =& new db();
	$id_bdd = $bdd->connect(SQL_HOST, SQL_BASE, SQL_USER, SQL_PASS);

$var_sql_list_object = "
CREATE TABLE list_object (
	obj_id				int(4) unsigned NOT NULL auto_increment,
	obj_object			text,
	obj_description		text,
	obj_plugin			char(255) NOT NULL default '',
	obj_dcount			int(4) unsigned NOT NULL default 0,
	PRIMARY KEY  (obj_id)
) TYPE=MyISAM COMMENT='Table des objets du système de fichiers';";

$var_sql_list_comment = "
CREATE TABLE list_comment (
	comment_id				int(4) unsigned NOT NULL auto_increment,
	comment_object			text,

	comment_author			char(255) NOT NULL default '',
	comment_mail			char(255) NOT NULL default '',
	comment_url				char(255) NOT NULL default '',
	comment_date			int(10) unsigned NOT NULL default '0',
	comment_content			text,

	PRIMARY KEY  (comment_id)
) TYPE=MyISAM COMMENT='Table des commentaires des objets';";

	if (!$var = $bdd->execQuery($var_sql_list_object)) {
		$ret = $bdd->getError();
		echo 'Erreur :';
		echo '<pre>'.$ret['message'].'</pre>';
	} else {

		if (!$var = $bdd->execQuery($var_sql_list_comment)) {
			$ret = $bdd->getError();
			echo 'Erreur :';
			echo '<pre>'.$ret['message'].'</pre>';
		} else {
?>
<p>
	Création des tables effectuée !
</p>

<p>
	Fin de l'installation !
	<br />
	<b>ATTENTION</b>, Vous devez impérativement supprimé le fichier <strong><?php echo $_SERVER['PHP_SELF'] ?></strong> sinon, le script ne s'exécutera pas !
</p>

<?php
		}

	}
}
?>
</body>

</html>
