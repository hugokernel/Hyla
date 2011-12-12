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

define('PAGE_HOME', true);

require 'src/init.php';

/*	Connection à la base
 */
$bdd =& new db();
if (!$bdd->connect(SQL_HOST, SQL_USER, SQL_PASS))
	system::end(__('Couldn\'t connect to sql server !'));

if (!$bdd->select(SQL_BASE))
	system::end(__('Unable to use database &laquo; %s &raquo;', SQL_BASE));

require 'src/inc/cache.class.php';
require 'src/inc/plugins.class.php';
require 'src/inc/plugin_obj.class.php';
require 'src/inc/image.class.php';
require 'src/inc/plugin_auth.class.php';

header('Content-Type: text/html; charset=UTF-8');

error_reporting(E_ERROR);

$tpl = new Template(DIR_TPL.'rss');

/*	Chargement des infos de l'utilisateur courant
 */
$auth = new plugin_auth();
$auth->load();
$cuser = $auth->getUser();

// Instanciation et récupération des droits
$obj = new obj(FOLDER_ROOT);
$obj->loadRights();


// Création de l'objet url avec génération d'url absolue
$url = new url(true);
$url->scan();

$type = (isset($_GET['type']) && $_GET['type'] == 'comment') ? 'comment' : 'info';

// Récupération des infos de l'objet et vérif des droits
$cobj = (url::getQueryAff(0) != 'page') ? $obj->getInfo(url::getQueryObj(), true, true) : new tFile;
if (($type == 'info' && $cobj->type != TYPE_DIR) || !($obj->getCUserRights4Path($cobj->path) & AC_VIEW)) {
	if (!$cobj || $cobj->file == '/') {
		if ($cuser->id != ANONYMOUS_ID && ($obj->getCUserRights4Path('/') & AC_VIEW)) {
			header('HTTP/1.x 404 Not Found');
			system::end(__('Object not found !'));
		} else {
			header('HTTP/1.x 401 Authorization Required');
			system::end(__('You do not have the rights sufficient to reach the resource !'));
		}
	} else {
		header('HTTP/1.x 404 Not Found');
		system::end(__('Object not found !'));
	}
	system::end();
}


$tpl->set_var(array(
		'TITLE'			=>	$cobj->file.' '.$conf['title'],
		'DESCRIPTION'	=>	$cobj->info->description,
		'URL'			=>	url::getObj($cobj->file),
		'LANG'			=>	$conf['lng'],
		'COPYRIGHT'		=>	$conf['webmaster_mail'],
		'DATE'			=>	format_date(system::time(), 1),
		'AUTHOR'		=>	'hugo',
		));


// Listage des commentaires
if ($type == 'comment') {

	$tpl->set_var('TITLE', $cobj->file.' - '.__('Last comment').' '.$conf['title']);

	$tpl->set_file('comment', 'comment.xml');
	$tpl->set_block('comment', 'item', 'Hdlitem');

	$tab = $obj->getCommentDir($cobj->file, $conf['rss_nbr_comment']);

	foreach ($tab as $cmt) {
		$tz = date('O', $cmt->date);
		$tz = date('O', time());
		$tz = substr($tz, 0, -2).':'.substr($tz, -2);
		$date = date('Y-m-d\\TH:i:s', $cmt->date).$tz;

		$tpl->set_var(array(
				'OBJ_TITLE'			=>	htmlentities(get_iso($cmt->object)),
				'OBJ_DESCRIPTION'	=>	htmlentities($cmt->content, ENT_QUOTES),
				'OBJ_LINK'			=>	url::getObj($cmt->object),
				'OBJ_ID'			=>	$cmt->id,
				'OBJ_AUTHOR'		=>	$cmt->author,
				'OBJ_DATE'			=>	$date,
				'OBJ_CONTENT'		=>	'<![CDATA['.$cmt->content.']]>',
				));

		$tpl->parse('Hdlitem', 'item', true);
	}

	$tpl->pparse('OutPut', 'comment');

} else {

	$tpl->set_file('obj', 'obj.xml');
	$tpl->set_block('obj', array(
			'media_img'	=>	'Hdlmedia_img',
			'item'		=>	'Hdlitem',
			));

	// Listage du dossier
	$tab = $obj->getDirContent($cobj->file, SORT_DATE, 0, $conf['rss_nbr_obj'], -1, array('=', 'type', TYPE_FILE));

	$data = true;

	foreach ($tab as $cobj) {

		$tpl->set_var('Hdlmedia_img');

		if ($data) {
			$plugins = new plugins($cobj);
			$plugins->search();
			$var_tpl = $plugins->load();

			$tpl->set_var(array(
					'STYLESHEET_PLUGIN'	=>	get_css_plugin(),
					'OBJ_CONTENT'		=>	($cobj->info->description ? $cobj->info->description.'<hr />' : null).$var_tpl,
					));
		}

		// Si le dernier accès enregistré en base n'est pas dispo, on lit l'accès physique !
		$t = ($cobj->info->date_last_update) ? $cobj->info->date_last_update : filectime($cobj->realpath);

		$tz = date('O', time());
		$tz = substr($tz, 0, -2).':'.substr($tz, -2);
		$date = date('Y-m-d\\TH:i:s', $t).$tz;

		$tpl->set_var(array(
				'OBJ_TITLE'			=>	view_obj($cobj->name),
				'OBJ_DESCRIPTION'	=>	htmlentities($cobj->info->description, ENT_QUOTES),
				'OBJ_URL'			=>	null,
				'OBJ_AUTHOR'		=>	null,
				'OBJ_DATE'			=>	$date,
				'OBJ_COPYRIGHT'		=>	null,

				'OBJ_SIZE'			=>	$cobj->size,
				'OBJ_MIME'			=>	function_exists('mime_content_type') ? mime_content_type($cobj->realpath) : null,
				'OBJ_URL'			=>	url::getObj($cobj->file, 'download'),
				));

		$tpl->parse('Hdlitem', 'item', true);
	}

	$tpl->pparse('OutPut', 'obj');
}

system::end();

?>
