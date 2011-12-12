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

if (basename($_SERVER['PHP_SELF']) != 'install.php') {
	exit('Par mesure de sécurité, ce fichier doit être nommé install.php pour pouvoir être exécuté !');
}

define('PAGE_HOME', true);

require 'src/conf.php';
require 'src/db/mysql.class.php';
require 'src/inc/file.class.php';

error_reporting(E_ERROR);

$conf_file = 'conf/config.inc.php';

$prefix = PREFIX_TABLE;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<title>Hyla installeur</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

<link rel="stylesheet" type="text/css" media="screen,projection" title="Défault" href="tpl/default/default.css" />

</head>

<body>

<a href="http://www.digitalspirit.org/hyla/"><img src="img/hyla.png" width="100" height="100" alt="Logo Hyla : La rainette verte" /></a>

<h1>Installation de &laquo; Hyla <?php echo HYLA_VERSION; ?> &raquo; </h1>
<hr />
<?php

$notice = 0;
$error = 0;

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

$etape = @$_REQUEST['etape'];
switch ($etape) {

	#	Vérification de la configuration du serveur
	case '2':
?>
	<h3>Vérification de la configuration du serveur</h3>
	<p>Configuration Php :</p>
	<ul>
		<li>Téléchargement de fichiers distants ( <em>allow_url_fopen</em> ) : <strong><?php get_html_result(ini_get('allow_url_fopen')); ?></strong></li>
		<li>Autorise ou non le téléchargement de fichier sur le serveur ( <em>file_uploads</em> ) : <strong><?php get_html_result(ini_get('file_uploads')); ?></strong></li>
		<li>Taille maximale acceptée d'un fichier envoyé sur le serveur ( <em>upload_max_filesize</em> ) : <strong><?php echo ini_get('upload_max_filesize'); ?></strong></li>
	</ul>

	<p>Extensions :</p>
	<ul>
		<li>Bibliothèque Mysql (Gestionnaire de base de données) : <strong><?php get_html_result(extension_loaded('mysql'), true); ?></strong>
		<li>Bibliothèque SESSION (gestion des sessions) : <strong><?php get_html_result(extension_loaded('session'), true); ?></strong>
<?php
if (preg_match('#\.free\.fr#i', $_SERVER['SERVER_NAME']))
	echo '<blockquote class="info">Attention, pour les utilisateurs de free.fr, n\'oubliez pas de créer à la racine de votre site un répertoire nommé "sessions"</blockquote>';
?>
		</li>
		<li>Bibliothèque GD (pour manipuler les images) : <strong><?php get_html_result(extension_loaded('gd')); ?></strong></li>
		<li>Bibliothèques EXIF (pour lire les données EXIF contenu dans certaine image) : <strong><?php get_html_result(extension_loaded('exif')); ?></strong></li>
	</ul>

	<p>Droits en écriture :</p>
	<ul>
		<li>Répertoire de configuration ( &laquo; <?php echo DIR_CONF; ?> &raquo; ) : <strong><?php get_html_result(is_writable(DIR_ROOT.DIR_CONF), true); ?></strong></li>
		<li>Fichier conf ( &laquo; <?php echo FILE_INI; ?> &raquo; ) : <strong><?php get_html_result(is_writable(DIR_ROOT.FILE_INI), true); ?></strong></li>
		<li>Cache ( &laquo; <?php echo DIR_CACHE; ?> &raquo; ) : <strong><?php get_html_result(is_writable(DIR_ROOT.DIR_CACHE), true); ?></strong></li>
		<li>Fichiers anonymes ( &laquo; <?php echo DIR_ANON; ?> &raquo; ) : <strong><?php get_html_result(is_writable(DIR_ROOT.DIR_ANON), true); ?></strong></li>
	</ul>

<?php
if (preg_match('#\.free\.fr#i', $_SERVER['SERVER_NAME']))
	echo '<blockquote class="info">Attention, pour les utilisateurs de free.fr, il vous sera impossible de supprimer des répertoires car free empêche l\'exécution de la fonction rmdir, vous devrez pas conséquent le faire par vos propres moyens, par exemple, grâce à un programme ftp.</blockquote>';
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

?>
	<hr />
	<p>
<?php

if (!test_config())
	echo '<a href="?etape=2">Réessayer</a>';
else
	echo '<a href="?etape=3">Poursuivre l\'installation</a>';
?>
	</p>
<?php
		break;

	#	Création du fichier de configuration
	case '3':
		session_destroy();
		if (test_config()) {
?>
	<h3>Création du fichier de configuration ( <?php echo $conf_file; ?> )</h3>

	<form method="post" name="form_user" action="?etape=4">
		<fieldset>
			<legend>Répertoire contenant les fichiers à partager :</legend>
			<p>
				<em></em>
			</p>
			<p>
				<input name="folder_root" id="folder_root" size="100" maxlength="255" value="<?php echo dirname($_SERVER['SCRIPT_FILENAME']); ?>" type="text" />
			</p>
			<p class="help">
				Il s'agit du chemin complet depuis la racine sans slash ( / ) de fin
				<br />
				Ex:	/var/www/data
				<p>
					<strong>
						Il est vivement recommandé de <span style="color: red">ne pas laissé le répertoire de Hyla en partage</span> car un visiteur malicieu
						pourrait explorer l'arborescence de Hyla et découvrir les informations contenues dans le fichier &laquo; conf/config.inc.php &raquo;
						Si vous décidez par la suite de changer de répertoire, éditez le fichier &laquo; conf/config.inc.php &raquo; et éditez la clef FOLDER_ROOT.
					</strong>
				</p>
			</p>
		</fieldset>
		<br />
		<fieldset>
			<legend>Connection à la base de données :</legend>
			<p>
				<label for="sql_host">Serveur :</label>
				<input name="sql_host" id="sql_host" size="20" maxlength="255" value="<?php echo $sql_server; ?>" type="text" />
			</p>
			<p>
				<label for="sql_user">Utilisateur :</label>
				<input name="sql_user" id="sql_user" size="20" maxlength="255" value="<?php echo $sql_user; ?>" type="text" />
			</p>
			<p>
				<label for="sql_base">Base de données :</label>
				<input name="sql_base" id="sql_base" size="20" maxlength="255" value="<?php echo $sql_user; ?>" type="text" />
			</p>
			<p>
				<label for="sql_pass">Mot de passe :</label>
				<input name="sql_pass" id="sql_pass" size="20" maxlength="255" value="" type="text" />
			</p>
		</fieldset>
		<br />
		<input type="submit" name="Submit" value="Continuer" />
	</form>
	<hr />
<?php
			break;
		}

	#	Test connection
	case '4':
		if (test_config()) {

			$folder_root = $_POST['folder_root'];

			$sql_host = $_POST['sql_host'];
			$sql_user = $_POST['sql_user'];
			$sql_pass = $_POST['sql_pass'];
			$sql_base = $_POST['sql_base'];

			// Surtout pas de / en fin de chaine !
			$size = (strlen($folder_root) - 1);
			if ($folder_root{$size} == '/') {
				$folder_root = substr($folder_root, 0, $size);
			}

			if ($folder_root == dirname($_SERVER['SCRIPT_FILENAME'])) {
?>
		<p>
			<strong>Avertissement !</strong>
			<br />
			Vous avez laissé comme répertoire à lister la racine de Hyla, un utilisateur malicieu pourrait lire le fichier &laquo; conf/config.inc.php &raquo; et,
			y découvrir les login et mot de passe de connection à la base de données.
			Si vous décidez par la suite de changer de répertoire, éditez le fichier &laquo; conf/config.inc.php &raquo; et éditez la clef FOLDER_ROOT.
		</p>
<?php
			}

?>
	<h3>Test de connection à la base de données</h3>

<?php


			/*	Connection à la base
			 */
			$bdd =& new db();
			if (!$bdd->connect($sql_host, $sql_user, $sql_pass)) {
?>
	<blockquote class="error">
		Impossible de se connecter au serveur SQL &laquo; <?php echo $sql_host; ?> &raquo; !
	</blockquote>
<?php
			} else {

				if (!$bdd->select($sql_base)) {
?>
	<blockquote class="error">
		Impossible de sélectionner la base de données &laquo; <?php echo $sql_base; ?> &raquo; !
	</blockquote>
<?php
				} else {

					$bdd->close();
?>
	<ul>
		<li>Connection au serveur SQL réussi : <?php get_html_result(true); ?></li>
<?php

$var_file =
"<?php
/*
	This file is part of Hyla
	Copyright (c) 2004-2006 Charles Rincheval.
	All rights reservednstall.php?etape=5

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


/*	+----------------------------------------------------+
 	| Répertoire contenant vos fichiers à lister         |
 	| /!\ ATTENTION, pas de slash ou anti slash de fin ! |
 	+----------------------------------------------------+
 */
define('FOLDER_ROOT', '$folder_root');


/*	+---------------------------------+
	| Connection à la base de données |
	+---------------------------------+
 */
define('SQL_HOST',	'$sql_host');
define('SQL_BASE',	'$sql_base');
define('SQL_USER',	'$sql_user');
define('SQL_PASS',	'$sql_pass');

?>";

				$file_conf = false;
				if (!file_exists($conf_file) && file::putContent($conf_file, $var_file))
					$file_conf = true;

				if ($file_conf) {
?>
		<li>Ecriture du fichier de configuration (<strong><?php echo $conf_file; ?></strong>) <?php get_html_result(true); ?></li>
	</ul>
<?php
				} else {
?>
	</ul>
	<blockquote class="error">
		Erreur durant la création du fichier de configuration (<strong><?php echo $conf_file; ?></strong>) : le fichier existe peut être déjà !
	</blockquote>
<?php
				}

			}
		}
?>
	<hr />
<?php

		if (test_config() && $file_conf)
			echo '<a href="?etape=5">Création des tables</a>';
		else
			echo '<a href="?etape=3">Changer les paramètres</a>';

		}
		break;

	#	Création des tables
	case '5':

		if (!test_config())
			break;

?>
	<h3>Création des tables nécessaires à l'application</h3>
<?php
		include $conf_file;
		$bdd =& new db();
		if ($bdd->connect(SQL_HOST, SQL_USER, SQL_PASS) && $bdd->select(SQL_BASE)) {

$var_query[0]['desc'] = 'Création de la table &laquo; '.$prefix.'object &raquo; ';
$var_query[0]['query'] = "
CREATE TABLE `{$prefix}object` (
  `obj_id` int(4) unsigned NOT NULL auto_increment,
  `obj_object` text,
  `obj_description` text,
  `obj_plugin` char(255) NOT NULL default '',
  `obj_dcount` int(4) unsigned NOT NULL default '0',
  PRIMARY KEY  (`obj_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs COMMENT='Table des objets du système de fichiers';
";

$var_query[1]['desc'] = 'Création de la table &laquo; '.$prefix.'users &raquo; ';
$var_query[1]['query'] = "
CREATE TABLE `{$prefix}users` (
  `usr_id` int(4) unsigned NOT NULL auto_increment,
  `usr_name` char(32) NOT NULL,
  `usr_password_hash` char(255) NOT NULL default '',
  `usr_perm` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`usr_id`),
  UNIQUE KEY `usr_name` (`usr_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs COMMENT='Table des utilisateurs';
";

$var_query[2]['desc'] = 'Création de la table &laquo; '.$prefix.'comment &raquo; ';
$var_query[2]['query'] = "
CREATE TABLE `{$prefix}comment` (
  `comment_id` int(4) unsigned NOT NULL auto_increment,
  `comment_object` text,
  `comment_author` char(255) NOT NULL default '',
  `comment_mail` char(255) NOT NULL default '',
  `comment_url` char(255) NOT NULL default '',
  `comment_date` int(10) unsigned NOT NULL default '0',
  `comment_content` text,
  PRIMARY KEY  (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_cs COMMENT='Table des commentaires des objets' ;
";

$var_query[3]['desc'] = 'Insertion de données dans la table &laquo; '.$prefix.'users &raquo; ';
$var_query[3]['query'] = "
INSERT INTO `{$prefix}users` ( `usr_id` , `usr_name` , `usr_password_hash` , `usr_perm` )
VALUES (
NULL , 'Anonymous', '', '1'
);";
			echo '<ul>';

			$error = 0;
			foreach ($var_query as $q) {
				if (!$var = $bdd->execQuery($q['query'])) {
					$ret = $bdd->getError();
					$error++;
?>
	<blockquote class="error">
		<?php echo $ret['message']; ?>
	</blockquote>
<?php
				} else {
?>
		<li><?php	echo $q['desc'].' : ';
					echo get_html_result(true); ?></li>
<?php
				}
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
		<a href="?etape=6">Création de l'utilisateur principal</a>
	</p>
<?php

		break;

	#	Création d'un utilisateur
	case '6':

		$username = null;
		$password = null;

		if (@$_POST['Submit']) {

			$error = false;

			include $conf_file;

			$bdd =& new db();
			if ($bdd->connect(SQL_HOST, SQL_USER, SQL_PASS) && $bdd->select(SQL_BASE)) {
				$username = trim($_POST['usr_login']);
				$password = trim($_POST['usr_password']);
				$password = crypt($password, CRYPT_SALT);
				$perm = ADD_COMMENT | EDIT_FILE | ADD_FILE | DEL_FILE | CREATE_DIR | DEL_DIR | ADMIN;

				if (!empty($username) && !empty($password)) {
					$var = $bdd->execQuery("
INSERT INTO `{$prefix}users` ( `usr_id` , `usr_name` , `usr_password_hash` , `usr_perm` )
VALUES (
NULL , '$username', '$password', '$perm'
);");
					$bdd->close();

?>
	<ul>
		<li>Ajout de l'utilisateur &laquo; <?php echo $username; ?> &raquo; : <?php get_html_result(true); ?></li>
	</ul>
	<hr />
	<p>
		<a href="?etape=7">Fin de l'installation</a>
	</p>
<?php
					break;
				} else {
					$error = true;
				}
			}
		}
?>
	<h3>Création de l'utilisateur principal</h3>
<?php
if ($error) {
?>
	<blockquote class="error">
		Tous les champs doivent être rempli !
	</blockquote>
<?php
}
?>
	<form method="post" name="form_user" action="?etape=6">
		<fieldset>
			<legend>Création d'un utilisateur :</legend>
			<p>
				<label for="usr_login">Nom d'utilisateur :</label>
				<input name="usr_login" id="usr_login" size="20" maxlength="255" value="<?php echo $username; ?>" type="text" />
			</p>
			<p>
				<label for="usr_password">Mot de passe :</label>
				<input name="usr_password" id="usr_password" size="20" maxlength="255" value="<?php echo $password; ?>" type="text" />
			</p>
			<input type="submit" name="Submit" value="Créer" />
		</fieldset>
	</form>

	<hr />

<?php
		break;

	#	Fin de l'installation
	case '7':
?>
	<h3>Fin de l'installation !</h3>

	<p>
		Hyla est maintenant installé !
		<br />
		Vous devez <strong>supprimé le fichier install.php</strong>, une fois ceci fait, vous pourrez vous connecter dans l'administration et finir de paramétrer Hyla :
		<a href="index.php?p=page-admin">Administration</a>
<?php
if (preg_match('#\.free\.fr#i', $_SERVER['SERVER_NAME']))
	echo '<blockquote class="info">Attention, pour les utilisateurs de free.fr, le système de cache utilisé sur les serveurs de free peut garder en mémoire install.php même une fois celui ci supprimé !</blockquote>';
?>
	</p>

	<hr />

	<p>
		Et n'oubliez pas :
	</p>

	<ul>
		<li><a href="http://www.digitalspirit.org/hyla/">Le site officiel de Hyla</a>
		<li><a href="http://www.digitalspirit.org/hyla/?aff=doc">La documentation</a>
		<li><a href="http://www.digitalspirit.org/forums/viewforum.php?id=11">Le forum dédié</a>
		<li><a href="http://www.digitalspirit.org/hyla/?aff=faq">Les questions les plus fréquemment posées</a>
	</ul>

<?php
		break;

	#	Présentation
	case '1':
	default:
?>
	<h3>Bienvenue dans l'installation d'Hyla</h3>

	<p>
		Merci d'avoir choisi Hyla, un gestionnaire de fichiers, simple, léger, extensible, capable de générer des galeries photos et bien plus encore... il est notamment respectueu des standards en vigueur sur le web.
	</p>

	<p>
		Afin de vous aider, vous pouvez consulter la <a href="http://www.digitalspirit.org/hyla/?aff=doc">documentation en ligne</a>.
	</p>
	<p>
		N'hésitez pas à faire part de votre problème quel qu'il soit concernant Hyla sur <a href="http://www.digitalspirit.org/forums/viewforum.php?id=11">le forum dédié</a>.
	</p>

	<hr />
	<p>
		Ce script vous permettra de l'installer sur votre serveur en suivant les étapes indiquées.
		<br />
		<a href="?etape=2">Commencer l'installation</a>
	</p>
<?php
		break;
}

?>

</body>

</html>
<?php

system::end();

?>
