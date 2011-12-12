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


class tComment {
	var $author;
	var $mail;
	var $url;
	var $content;
	var $date;
}

/*	Information venant de la base de données
 */
class tFileInfo {
	var $description;	// Description de l'objet
	var $nbr_comment;	// Nombre de commentaires
	var $comment;		// Objet commentaire
	var	$plugin;		// Le plugin par défaut (pour les répertoires)
	var $dcount;		// Nombre de téléchargement

	function tFileInfo() {
		$this->description = null;
		$this->nbr_comment = 0;
		$this->comment = array();
		$this->plugin = null;
	}
}

/*	Information sur le nom l'adresse et le type du fichier
 */
class tFile {
	var $type;			// Fichier, Répertoire, Archive
	var $path;			// Le chemin depuis la racine FOLDER_ROOT
	var $name;			// Le nom du fichier, Vide si répertoire
	var $file;			// path + name	(Très utile pour éviter d'avoir à faire des $path.$name par la suite)
	var $target;		// La cible lorsque l'on pointe à l'intérieur d'un fichier (fichier contenu dans une archive)
	var $extension;		// L'extension du fichier
	var $realpath;		// Le chemin depuis la racine du système /!\ ATTENTION /!\ Pour des raisons de sécurité, ce chemin ne doit jamais être montré au connecté

	var $size;			// La taille du fichier !
	var $icon;			// L'icone correspondante au type du fichier

	var $prev;			// Nom du fichier précédent
	var $next;			// Nom du fichier suivant

	var $info;

	// Utile pour la pagination
/*	var	$prev;			// tFile précédent
	var	$next;			// tFile suivant
*/
	function tFile() {

		$this->type = TYPE_UNKNOW;
		$this->path = null;
		$this->name = null;
		$this->file = null;
		$this->target = null;
		$this->extension = null;
		$this->realpath = null;

		$this->size = null;
		$this->icon = null;

		$this->prev = null;
		$this->next = null;

		$this->info = new tFileInfo;
	}
}


class obj {

	var		$_bdd;

	var		$_folder_root;

	var		$_object_table;
	var		$_comment_table;

	var		$_tri;				// Le tri à appliquer

	function obj($_folder_root) {

		global 	$bdd;

		$this->_bdd = &$bdd;

		$this->_folder_root = $_folder_root;

		$this->_object_table = TABLE_LIST;
		$this->_comment_table = TABLE_COMMENT;

		$this->_tri = SORT_CONFIG;

	}

	/*	Scan l'url et affecte les variables $aff, $act et renvoie le tableau d'infos de l'objet
	 */
	function scanUrl() {

		global $act, $aff, $param, $pact, $paff;
		$object = null;

		if (URL_SCAN == 'QUERY_STRING') {
			@list($act, $actobject) = explode(',', @$_REQUEST['act']);
			@list($aff, $object) = explode(',', @$_REQUEST['aff']);

			$object = ($object) ? $object : $actobject;

			$paff = @$_REQUEST['paff'];
			$pact = @$_REQUEST['pact'];

		} else if (URL_SCAN == 'PATH_INFO') {
			$exp = preg_quote('#'.ROOT_URL, '/').'\/index.php\/(.*)#se';	//(.*?)\/([^\/]+)#se';
			preg_match($exp, $_SERVER['REQUEST_URI'], $match);

			$slpos = strpos(@$match[1], '/');
			if ($slpos) {
				$aff = substr(@$match[1], 0, $slpos);
				$object = substr(@$match[1], $slpos);
			} else
				$aff = @$match[1];

			if (!$object)
				$object = '/';
		}

//		@list($aff, $act) = explode('_', $aff);
		@list($aff, $param) = explode('-', $aff);
		return obj::getInfo($object, FOLDER_ROOT);
	}

	/*	Renvoie l'url qui va bien pour envoyer des paramètres au module courant
		@param	string	$mact	Paramètres action pour le module
		@param	string	$maff	Paramètres affichage pour le module
	 */
/*
	function getPluginUrl($mact = null, $maff) {

	}
*/

	/*	Renvoie l'url de l'objet courant
	 */
	function getCurrentUrl($aff = AFF_INFO, $paff = null, $act = null, $pact = null) {
		global $cobj;
		$s = obj::getUrl($cobj->file, $aff, $paff, $act, $pact);
		$s .= ((@$cobj->target) ? '!'.$cobj->target : null);
		return $s;
	}

	/*	Renvoie une url correctement constituée
		@param	string	$object	L'objet en question
		@param	int		$aff	L'affichage : download, info, edit, mini...
		@param	string	$paff	Les paramètres affichage à passer
		@param	int		$act	L'action : addcomment
		@param	string	$pact	Les paramètres action à passer
	 */
	function getUrl($object, $aff = AFF_INFO, $paff = null, $act = null, $pact = null) {

		$s = ROOT_URL.'/index.php';
		$s .= (URL_SCAN == 'QUERY_STRING') ? '?aff=' : '/';

		switch ($aff) {
			case AFF_MINI:		$s .= 'mini-'.$paff;	break;
			case AFF_DOWNLOAD:	$s .= 'download';		break;
			case AFF_UPLOAD:	$s .= 'upload';			break;
			case AFF_EDIT:		$s .= 'edit';			break;
			case AFF_LOGIN:		$s .= 'login';			break;
			default:
			case AFF_INFO:		$s .= 'info';			break;
		}

		switch ($act) {
			case ACT_ADDCOMMENT:	$s .= '_addcomment';	break;
			default:
		}

		$sep = (URL_SCAN == 'QUERY_STRING') ? ',' : null;
		$s .= $sep.$object;
		return $s;
	}

	/*	Renvoie un tableau contenant les informations sur un objet (fichier ou dossier)
		@param	string	$name	Le nom de l'objet
		@param	string	$base	La racine qui comporte $folder
		@param	bool	$data	Récupérer les infos en base de données (true)
		@param	bool	$pnext	Récupère ou non les objet précédent et suivant
	 */
	function getInfo($name, $base = null, $data = true, $pnext = true) {

		global 	$bdd;
		static	$s;
		$s++;

		$obj = new tFile;
		$obj->type = TYPE_UNKNOW;

		$target = null;
		@list($name, $target) = @explode('!', $name, 2);

		if ($target) {
			$obj->type = TYPE_ARCHIVE;
			$obj->path = file::getRealDir($name, $base);
			$obj->name = file::getRealFile($name, $base);
			$obj->file = $obj->path.$obj->name;
			$obj->target = $target;
			$obj->extension = file::getExtension(basename($target));
			$obj->realpath = FOLDER_ROOT.$obj->file;
			$obj->icon = get_icone_from_ext($obj->extension);

			if ($pnext) {
				$ret = archive::getPrevNext($obj->realpath, $obj->target);
				$obj->prev = ($ret['prev'] ? obj::getInfo($name.'!'.$ret['prev'], $base, false, false) : null);
				$obj->next = ($ret['next'] ? obj::getInfo($name.'!'.$ret['next'], $base, false, false) : null);
			}

		} else if (is_file($base.$name)) {
			$obj->type = TYPE_FILE;
			$obj->path = file::getRealDir($name, $base);
			$obj->name = file::getRealFile($name, $base);
			$obj->file = $obj->path.$obj->name;
			$obj->extension = file::getExtension($obj->name);
			$obj->realpath = FOLDER_ROOT.$obj->file;
			$obj->size = filesize(FOLDER_ROOT.$obj->file);

			if ($data) {
				// On récupère la description de l'objet et les commentaires
/*				$sql = "SELECT	obj_description, obj_plugin, comment_author, comment_mail, comment_url, comment_date, comment_content
						FROM	{$this->_object_table}
								INNER JOIN {$this->_comment_table} ON obj_object = comment_object
						WHERE	comment_object = obj_object
						AND		(comment_object = '$name' OR obj_object = '$name')";
*/
				//	Pas de jointure ici puisque il faudrait qu'au moins une des 2 tables soit rempli, ce qui n'est pas forcément le cas...

				$sql = "SELECT	obj_description, obj_plugin, obj_dcount
						FROM	{$this->_object_table}
						WHERE	obj_object = '{$obj->file}'";
				if (!$var = $bdd->execQuery($sql))
					error::log(__FILE__, __LINE__, $bdd);
				$tab = $bdd->fetchArray($var);
				$obj->info->description = $tab['obj_description'];
				$obj->info->dcount = (int)$tab['obj_dcount'];
				//$obj->info->plugin = strtolower($tab['obj_plugin']); -> Pour les fichiers aussi ??????

				$sql = "SELECT	comment_author, comment_mail, comment_url, comment_date, comment_content
						FROM	{$this->_comment_table}
						WHERE	comment_object = '{$obj->file}'";
				if (!$var = $bdd->execQuery($sql))
					error::log(__FILE__, __LINE__, $bdd);
				for ($i = 0; $res = $bdd->nextTuple($var); $i++) {
					$obj->info->comment[$i]->author		= $res['comment_author'];
					$obj->info->comment[$i]->mail		= $res['comment_mail'];
					$obj->info->comment[$i]->url		= $res['comment_url'];
					$obj->info->comment[$i]->content	= $res['comment_content'];
					$obj->info->comment[$i]->date		= $res['comment_date'];
				}
				$obj->info->nbr_comment = $i;
			}

			$obj->icon = get_icone_from_ext($obj->extension);

			if ($pnext) {
				$ret = $this->getPrevNext($obj->path, $obj->file);
				$obj->prev = ($ret['prev'] ? obj::getInfo($ret['prev'], $base, false, false) : null);
				$obj->next = ($ret['next'] ? obj::getInfo($ret['next'], $base, false, false) : null);
			}

		} else if (is_dir($base.$name)) {
			$obj->type = TYPE_DIR;
			$obj->path = file::getRealDir($name, $base);
			$obj->name = basename($base.$name);									// TODO: est-ce bien d'utiliser basename en lieu de place de getRealFile ?
			$obj->file = ($obj->path == '/') ? $obj->path : $obj->path.'/';
			$obj->realpath = FOLDER_ROOT.$obj->file;

			if ($data) {
				// On récupère la description de l'objet
				$sql = "SELECT	obj_description, obj_plugin, obj_dcount
						FROM	{$this->_object_table}
						WHERE	obj_object = '{$obj->file}'";
				if (!$var = $bdd->execQuery($sql))
					error::log(__FILE__, __LINE__, $bdd);
				$tab = $bdd->fetchArray($var);
				$obj->info->description = $tab['obj_description'];
				$obj->info->dcount = (int)$tab['obj_dcount'];
				$obj->info->plugin = strtolower($tab['obj_plugin'] ? $tab['obj_plugin'] : null);	//DIR_DEFAULT_PLUGIN); -> On met null, c'est mieux pour le par défaut !
			}

			$obj->icon = ($obj->info->plugin == 'gallery' || DIR_DEFAULT_PLUGIN == 'Gallery') ? 'gallery.png' : 'dir.png';
		}

		return $obj;
	}

	/*	Renvoie un tableau contenant les nom des fichiers précédent et suivant en tenant compte du tri
		@param	string	$path	Le chemin
		@param	string	$name	Le nom de l'objet
	 */
	function getPrevNext($path, $name) {

		$ret = array('prev' => null, 'next' => null);

		$tab = obj::_getDirContent($path, $this->_folder_root);

		// Et maintenant, on tri les données
		if ($this->_tri & SORT_ALPHA || $this->_tri & SORT_ALPHA_R)
			usort($tab, array($this, '__sort_alpha'));
		else if ($this->_tri & SORT_ALPHA_EXT || $this->_tri & SORT_ALPHA_EXT_R)
			usort($tab, array($this, '__sort_ext'));	

		$size = sizeof($tab);
		for ($i = 0; $i < $size; $i++) {
			if ($tab[$i] == $name) {
				$ret['prev'] = $prev;
				$ret['next'] = (($i + 1 >= $size) ? null : $tab[++$i]);
				break;
			}
			$prev = $tab[$i];
		}
		return $ret;
	}

	/*	Renvoie un tableau contenant toutes les informations sur un dossier ainsi que les fichier et dossiers contenu dedans
		@param	string	$name	Le nom de l'objet
		@param	int		$tri	Le tri à appliquer
		@param	int		$start	A partir d'ou on commence à compter
		@param	int		$nbr	Le nombre d'occurence à afficher
	 */
	function getDirContent($name, $tri = null, $start = 0, $nbr = 0, $tab = null) {

		global 	$bdd;

		if ($tri)
			$this->_tri = $tri;

		$arr = array();

		//	Récupère les fichiers et répertoire du système de fichiers
		if (!$tab) {
			$tab = obj::_getDirContent($name, $this->_folder_root);
		}

		$s_o = " WHERE obj_object = '$name' ";
		foreach ($tab as $occ) {
			$s_o .= 'OR obj_object = \''.$occ.'\' ';
		}

		$s_c = " WHERE comment_object = '$name' ";
		foreach ($tab as $occ) {
			$s_c .= 'OR comment_object = \''.$occ.'\' ';
		}

		// On récupère les infos des objets en BDD
/*		$sql = "SELECT	obj_object, obj_description, COUNT(comment_author) as nbr_comment
				FROM	{$this->_object_table}
				LEFT JOIN {$this->_comment_table} ON obj_object = comment_object
				$s
				GROUP 	BY obj_object
				ORDER	BY obj_object ASC";
*/
		$sql = "SELECT	obj_object, obj_description, obj_plugin
				FROM	{$this->_object_table}
				$s_o
				GROUP 	BY obj_object
				ORDER	BY obj_object ASC";
		if (!$var = $bdd->execQuery($sql))
			error::log(__FILE__, __LINE__, $bdd);
		for ($i = 0; $res = $bdd->nextTuple($var); $i++) {
			$a[$res['obj_object']]['description'] = $res['obj_description'];
			$a[$res['obj_object']]['plugin'] = $res['obj_plugin'];
		}


		$sql = "SELECT	comment_object, COUNT(comment_author) as nbr_comment
				FROM	{$this->_comment_table}
				$s_c
				GROUP 	BY comment_object
				ORDER	BY comment_object ASC";
		if (!$var = $bdd->execQuery($sql))
			error::log(__FILE__, __LINE__, $bdd);
		for ($i = 0; $res = $bdd->nextTuple($var); $i++) {
			$a[$res['comment_object']]['nbr_comment'] = $res['nbr_comment'];
		}

		// On mixe les données provenant du système de fichiers et de la base de données
		$i = 0;
		foreach ($tab as $occ) {
			if ($i >= $start) {
				$tab[$i] = obj::getInfo($occ, $this->_folder_root, false, false);
				if (@$a[@$tab[$i]->file]['description'] || @$a[@$tab[$i]->file]['nbr_comment'] || @$a[@$tab[$i]->file]['plugin']) {
					@$tab[$i]->info->description = $a[$tab[$i]->file]['description'];
					@$tab[$i]->info->plugin = $a[$tab[$i]->file]['plugin'];
					@$tab[$i]->icon = ($tab[$i]->type == TYPE_DIR) ? (($a[$tab[$i]->file]['plugin'] == 'gallery' || DIR_DEFAULT_PLUGIN == 'Gallery') ? 'gallery.png' : 'dir.png') : $tab[$i]->icon;		// TODO: ATTENTION: gallery.png en dur
					@$tab[$i]->info->nbr_comment = $a[$tab[$i]->file]['nbr_comment'];
				}
			}
			$i++;
		}

		// Et maintenant, on tri les données
		if ($tri & SORT_ALPHA || $tri & SORT_ALPHA_R)
			usort($tab, array($this, '_sort_alpha'));
		else if ($tri & SORT_ALPHA_EXT || $tri & SORT_ALPHA_EXT_R)
			usort($tab, array($this, '_sort_ext'));		

		// Et on affiche uniquement certain
		$cmpt = 0;
		$tab_out = array();
		$size = sizeof($tab);
		for ($i = 0, $c = 0; $i < $size; $i++) {
			if ($i >= $start) {
				$tab_out[$c++] = &$tab[$i];
				$cmpt++;
			}

			if ($nbr && $cmpt >= $nbr)
				break;
		}

		return $tab_out;
	}

	/*	Ajout d'un commentaire
		@param	string	$object L'objet demandé
		@param	string	$author	L'auteur du commentaire
		@param	string	$mail	Son email
		@param	string	$url	Son site
		@param	string	$content Le contenu
	 */
	function addComment($object, $author, $mail, $url, $content) {
		global $cobj;

		$sql = "INSERT INTO {$this->_comment_table}
				(comment_object, comment_author, comment_mail, comment_url, comment_date, comment_content)
				VALUES
				('".$object."', '$author', '$mail', '$url', '".system::time()."', '$content');";
		if (!$var = $this->_bdd->execQuery($sql))
			error::log(__FILE__, __LINE__, $bdd);

		$csize = sizeof($cobj->info->comment);

		$cobj->info->nbr_comment++;										// TODO: ATTENTION, mieux vaut le mettre dans act.php
		$cobj->info->comment[$csize] = new tComment;
		$cobj->info->comment[$csize]->author = $author;
		$cobj->info->comment[$csize]->mail = $mail;
		$cobj->info->comment[$csize]->url = $url;
		$cobj->info->comment[$csize]->content = $content;
		$cobj->info->comment[$csize]->date = system::time();
	}

	/*	Renvoie les derniers commentaires...
		@param	int	$nbr	Nombre maximum à renvoyer
	 */
	function getLastComment($nbr = 0) {
		$sql = "SELECT	comment_object, comment_author, comment_mail, comment_url, comment_date, comment_content
				FROM	{$this->_comment_table}
				ORDER	BY comment_date DESC";
		if (!$var = $this->_bdd->execQuery($sql))
			error::log(__FILE__, __LINE__, $bdd);
		for ($i = 0; $res = $this->_bdd->nextTuple($var); $i++) {
			$tab[$i] = new tComment;
			$tab[$i]->object = $res['comment_object'];
			$tab[$i]->author = $res['comment_author'];
			$tab[$i]->mail = $res['comment_mail'];
			$tab[$i]->url = $res['comment_url'];
			$tab[$i]->date = $res['comment_date'];
			$tab[$i]->content = $res['comment_content'];
		}
		return $tab;
	}

	/*	Modification d'un plugin sur un répertoite
		@param	string	$object L'objet demandé
		@param	string	$plugin	Le plugin voulu
	 */
	function setPlugin($object, $plugin) {
		global $cobj;

		// On change uniquement si le plugin est différent !
		if ($plugin != $cobj->info->plugin) {
			$sql = "SELECT obj_id FROM {$this->_object_table} WHERE obj_object = '$object'";
			if (!$var = $this->_bdd->execQuery($sql))
				error::log(__FILE__, __LINE__, $bdd);
			else {
				$tab = $this->_bdd->fetchArray($var);
				if (!$tab) {
					$sql = "INSERT INTO {$this->_object_table}
							(obj_object, obj_plugin)
							VALUES
							('".$object."', '$plugin');";
					if (!$var = $this->_bdd->execQuery($sql))
						error::log(__FILE__, __LINE__, $bdd);
				} else {
					$sql = "UPDATE {$this->_object_table} SET obj_plugin = '$plugin' WHERE obj_object = '$object'";
					if (!$var = $this->_bdd->execQuery($sql))
						error::log(__FILE__, __LINE__, $bdd);
				}
			}
			$cobj->info->plugin = $plugin;
			if ($cobj->type == TYPE_DIR)
				$cobj->icon = ($plugin == 'gallery' || DIR_DEFAULT_PLUGIN == 'Gallery') ? 'gallery.png' : 'dir.png';	// TODO: ATTENTION, mieux vaut le mettre dans act.php
		}
	}

	/*	Modification d'une description
		@param	string	$object		L'objet demandé
		@param	string	$content	La description formatée comme il faut
	 */
	function setDescription($object, $content) {

		// On change uniquement si la description est différente !
		$sql = "SELECT obj_id FROM {$this->_object_table} WHERE obj_object = '$object'";
		if (!$var = $this->_bdd->execQuery($sql))
			error::log(__FILE__, __LINE__, $bdd);
		else {
			$tab = $this->_bdd->fetchArray($var);
			if (!$tab) {
				$sql = "INSERT INTO {$this->_object_table}
						(obj_object, obj_description)
						VALUES
						('".$object."', '$content');";
				if (!$var = $this->_bdd->execQuery($sql))
					error::log(__FILE__, __LINE__, $bdd);
			} else {
				$sql = "UPDATE {$this->_object_table} SET obj_description = '$content' WHERE obj_object = '$object'";
				if (!$var = $this->_bdd->execQuery($sql))
					error::log(__FILE__, __LINE__, $bdd);
			}
		}
	}

	/*	Ajouter un téléchargement à l'objet courant
	 */
	function addDownload() {
		global $cobj;
		$sql = "SELECT obj_id FROM {$this->_object_table} WHERE obj_object = '{$cobj->file}'";
		if ($cobj->info->dcount && !$var = $this->_bdd->execQuery($sql))
			error::log(__FILE__, __LINE__, $bdd);
		else {
			$tab = $this->_bdd->fetchArray($var);
			if (!$tab) {
				$sql = "INSERT INTO {$this->_object_table}
						(obj_object, obj_dcount)
						VALUES
						('{$cobj->file}', '$content');";
				if (!$var = $this->_bdd->execQuery($sql))
					error::log(__FILE__, __LINE__, $bdd);
			} else {
				$sql = "UPDATE {$this->_object_table} SET obj_dcount = obj_dcount + 1 WHERE obj_object = '{$cobj->file}'";
				if (!$var = $this->_bdd->execQuery($sql))
					error::log(__FILE__, __LINE__, $bdd);
			}
		}
	}

	/*	Supprime l'objet courant
	 */
	function delete() {
		global $cobj;
		$sql = "DELETE
				FROM	{$this->_object_table}
				WHERE	obj_object = '{$cobj->file}'";
		if (!$ret = $this->_bdd->execQuery($sql))
			error::log(__FILE__, __LINE__, $this->_bdd);
		$sql = "DELETE
				FROM	{$this->_comment_table}
				WHERE	comment_object = '{$cobj->file}'";
		if (!$ret = $this->_bdd->execQuery($sql))
			error::log(__FILE__, __LINE__, $this->_bdd);
		return $ret;
	}

	/*	Renvoie le contenu d'un répertoire trié comme on veut
		@access	 private
	 */
	function _getDirContent($folder, $base) {
	
		$tab = array();
		
		$hdl = dir($base.$folder);
		if ($hdl) {
			while (false !== ($occ = $hdl->read())) {

				if ($folder == '/' && $occ{0} == '.' && isset($occ{1}) && $occ{1} == '.')
					continue;

				if ($occ == '.')
					continue;

				// Si on a un fichier caché...
				if ($occ{0} == '.' && isset($occ{1}) && $occ{1} != '.' && !VIEW_HIDDEN_FILE) {
					continue;
				}

				$tab[] = $folder.$occ.(is_dir(FOLDER_ROOT.$folder.$occ) ? '/' : null);
			}
		}

		return $tab;
	}


	/*	Tri les répertoires en premier
		@access private
	 */
	function _sort_folder_first($a, $b) {
		$ret = 0;

		$bas_a = basename($a->name);
		$bas_b = basename($b->name);

		if ($bas_a == '..' || $bas_a == '.' )
			$ret = -1;

		if ($bas_b == '..' || $bas_b == '.')
			$ret = 1;

		if (!$ret && $this->_tri & SORT_FOLDER_FIRST) {
			if (is_dir($a->realpath) && is_dir($b->realpath)) {
				$ret = strcmp(strtolower($a->realpath), strtolower($b->realpath));
				if ($this->_tri & SORT_ALPHA_R)
					$ret = ($ret == 1) ? -1 : 1;
			} else {
				if (is_dir($a->realpath))
					$ret = -1;
				if (is_dir($b->realpath))
					$ret = 1;
			}
		}
		return $ret;
	}

	/*	Tri sur les noms
		@access private
	 */
	function _sort_alpha($a, $b) {
		$ret = $this->_sort_folder_first($a, $b);
		if (!$ret) {
			if ($this->_tri & SORT_ALPHA_R) {
				$c = $a;
				$a = $b;
				$b = $c;
			}
			return strcmp(strtolower(basename($a->name)), strtolower(basename($b->name)));
		}
		return $ret;
	}

	/*	Tri sur les extensions
		@access private
	 */
	function _sort_ext($a, $b) {
		$ret = $this->_sort_folder_first($a, $b);
		if (!$ret) {
			if ($this->_tri & SORT_ALPHA_EXT_R) {
				$c = $a;
				$a = $b;
				$b = $c;
			}
			return strcmp(strtolower($a->extension), strtolower($b->extension));
		}
		return $ret;
	}



	/*	/!\ TODO : Le code ci dessous doit disparaitre et être merger avec le code au dessus !
	 */



	/*	Tri les répertoires en premier
		@access private
	 */
	function __sort_folder_first($a, $b) {
		$ret = 0;

		$bas_a = basename($a);
		$bas_b = basename($b);

		if ($bas_a == '..' || $bas_a == '.' )
			$ret = -1;

		if ($bas_b == '..' || $bas_b == '.')
			$ret = 1;

		if (!$ret && $this->_tri & SORT_FOLDER_FIRST) {
			if (is_dir($this->_folder_root.$a) && is_dir($this->_folder_root.$b)) {
				$ret = strcmp(strtolower($a), strtolower($b));
				if ($this->_tri & SORT_ALPHA_R)
					$ret = ($ret == 1) ? -1 : 1;
			} else {
				if (is_dir($this->_folder_root.$a))
					$ret = -1;
				if (is_dir($this->_folder_root.$b))
					$ret = 1;
			}
		}
		return $ret;
	}

	/*	Tri sur les noms
		@access private
	 */
	function __sort_alpha($a, $b) {
		$ret = $this->__sort_folder_first($a, $b);
		if (!$ret) {
			if ($this->_tri & SORT_ALPHA_R) {
				$c = $a;
				$a = $b;
				$b = $c;
			}
			return strcmp(strtolower(basename($a)), strtolower(basename($b)));
		}
		return $ret;
	}

	/*	Tri sur les extensions
		@access private
	 */
	function __sort_ext($a, $b) {
		$ret = $this->__sort_folder_first($a, $b);
		if (!$ret) {
			if ($this->_tri & SORT_ALPHA_EXT_R) {
				$c = $a;
				$a = $b;
				$b = $c;
			}
			return strcmp(strtolower(file::getExtension($a)), strtolower(file::getExtension($b)));
		}
		return $ret;
	}

}


/*
		// On récupère la description de l'objet
		$sql = "SELECT	obj_object, obj_description, COUNT(comment_author) as nbr
				FROM	".TABLE_LIST."
				LEFT JOIN ".TABLE_COMMENT." ON obj_object = comment_object
				WHERE	obj_object LIKE '$name%' and obj_object not LIKE '$name%/%'
				GROUP 	BY obj_object";
*/

?>
