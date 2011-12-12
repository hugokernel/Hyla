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

	You should have received a copy of the GNU General Public Licensetod
	along with Hyla; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */


class tComment {
	var $id;
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

	var	$rights;		// Les droits d'accès (pour les répertoires)

	function tFileInfo() {
		$this->description = null;
		$this->nbr_comment = 0;
		$this->comment = array();
		$this->plugin = null;
	}
}

/*	Informations sur le nom, l'adresse et le type du fichier
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

	var $info;

	var $prev;			// Nom du fichier précédent
	var $next;			// Nom du fichier suivant

	function tFile() {

		$this->type = TYPE_UNKNOW;
		$this->path = '/';
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

//	var		$_current_obj;

	var		$_tri;				// Le tri à appliquer

	var		$_nbr_object;		// Le nombre d'éléments total dans le répertoire courant (utile pour la pagination)

	var		$_prefix;

	function obj($_folder_root) {

		global 	$bdd, $conf;

		$this->_bdd = &$bdd;

		$this->_folder_root = $_folder_root;

		$this->_object_table = TABLE_LIST;
		$this->_comment_table = TABLE_COMMENT;

		$this->_nbr_object = 0;

//		$this->_current_obj = null;

		$this->_tri = $conf['sort_config'];

		$this->_prefix = null;
	}

	/*	Renvoie un tableau contenant les informations sur un objet (fichier ou dossier)
		@param	string	$name	Le nom de l'objet
		@param	bool	$data	Récupérer les infos en base de données (true)
		@param	bool	$pnext	Récupère ou non les objet précédent et suivant
	 */
	function getInfo($name, $data = true, $pnext = true) {

		global $conf;

		$obj = new tFile;

		$target = null;
		@list($name, $target) = @explode('!', $name, 2);

		$dir = file::getRealDir($name, $this->_folder_root);

		if ($dir) {

			if ($target) {
				$obj->type = TYPE_ARCHIVE;
				$obj->path = $dir;
				$obj->name = file::getRealFile($name, $this->_folder_root);
				$obj->file = $obj->path.$obj->name;
				$obj->target = $target;
				$obj->extension = file::getExtension(basename($target));
				$obj->realpath = $this->_folder_root.$obj->file;
				$obj->icon = get_icon($obj->extension);

				if ($pnext) {
					$ret = archive::getPrevNext($obj->realpath, $obj->target);
					$obj->prev = ($ret['prev'] ? obj::getInfo($name.'!'.$ret['prev'], false, false) : null);
					$obj->next = ($ret['next'] ? obj::getInfo($name.'!'.$ret['next'], false, false) : null);
				}

			} else if (is_file($this->_folder_root.$name)) {
				$obj->type = TYPE_FILE;
				$obj->path = $dir;
				$obj->name = file::getRealFile($name, $this->_folder_root);
				$obj->file = $obj->path.$obj->name;
				$obj->extension = file::getExtension($obj->name);
				$obj->realpath = $this->_folder_root.$obj->file;
				$obj->size = filesize($this->_folder_root.$obj->file);

				if ($data) {
					// On récupère la description de l'objet et les commentaires
					// Pas de jointure ici puisque il faudrait qu'au moins une des 2 tables soit rempli, ce qui n'est pas forcément le cas...
					$sql = "SELECT	obj_description, obj_plugin, obj_dcount
							FROM	{$this->_object_table}
							WHERE	obj_object = '{$obj->file}'";
					if (!$var = $this->_bdd->execQuery($sql))
						trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
					$tab = $this->_bdd->fetchArray($var);
					$obj->info->description = $tab['obj_description'];
					$obj->info->dcount = (int)$tab['obj_dcount'];

					$sql = "SELECT	comment_author, comment_mail, comment_url, comment_date, comment_content
							FROM	{$this->_comment_table}
							WHERE	comment_object = '{$obj->file}'";
					if (!$var = $this->_bdd->execQuery($sql))
						trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
					for ($i = 0; $res = $this->_bdd->nextTuple($var); $i++) {
						$obj->info->comment[$i]->author	= $res['comment_author'];
						$obj->info->comment[$i]->mail		= $res['comment_mail'];
						$obj->info->comment[$i]->url		= $res['comment_url'];
						$obj->info->comment[$i]->content	= $res['comment_content'];
						$obj->info->comment[$i]->date		= $res['comment_date'];
					}
					$obj->info->nbr_comment = $i;
				}

				$obj->icon = get_icon($obj->extension);

				if ($pnext) {
					$ret = $this->getPrevNext($obj->path, $obj->file);
					$obj->prev = ($ret['prev'] ? obj::getInfo($ret['prev'], false, false) : null);
					$obj->next = ($ret['next'] ? obj::getInfo($ret['next'], false, false) : null);
				}

			} else if (is_dir($this->_folder_root.$name)) {

				$obj->type = TYPE_DIR;
				$obj->path = $dir;
				$obj->name = basename($this->_folder_root.$name);									// TODO: est-ce bien d'utiliser basename en lieu et place de getRealFile ?
				$obj->file = ($obj->path == '/') ? $obj->path : $obj->path.'/';
				$obj->realpath = $this->_folder_root.$obj->file;

				if ($data) {
					// On récupère la description de l'objet
					$sql = "SELECT	obj_description, obj_plugin, obj_dcount
							FROM	{$this->_object_table}
							WHERE	obj_object = '{$obj->file}'";
					if (!$var = $this->_bdd->execQuery($sql))
						trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
					$tab = $this->_bdd->fetchArray($var);
					$obj->info->description = $tab['obj_description'];
					$obj->info->dcount = (int)$tab['obj_dcount'];
					$obj->info->plugin = strtolower($tab['obj_plugin'] ? $tab['obj_plugin'] : null);	//DIR_DEFAULT_PLUGIN); -> On met null, c'est mieux pour le par défaut !
				}

				$obj->icon = ($obj->info->plugin == 'gallery' || $conf['dir_default_plugin'] == 'Gallery') ? 'img/mimetypes/gallery.png' : get_icon('.');
			}
		}

		return $obj;
	}

/*
	function getPerm($obj) {
		$sql = "SELECT	ac_id, ac_user_id, ac_object, ac_perm, ac_recurs
				FROM	list_acontrol
				WHERE	ac_object = '{$obj->file}'";
		if (!$var = $this->_bdd->execQuery($sql))
			trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
	}
*/

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
		for ($i = 0, $prev = null; $i < $size; $i++) {
			if ($tab[$i] == $name) {
				$ret['prev'] = $prev;
				$ret['next'] = (($i + 1 >= $size) ? null : $tab[++$i]);
				break;
			}
			$prev = $tab[$i];
		}
		return $ret;
	}

	/*	Renvoie un tableau contenant toutes les informations sur un dossier ainsi que les fichiers et dossiers contenu dedans
		@param	string	$name	Le nom de l'objet
		@param	int		$tri	Le tri à appliquer
		@param	int		$start	A partir d'oû on commence à compter
		@param	int		$nbr	Le nombre d'occurence à afficher
		@param	array	$tab	Tableau de données
	 */
	function getDirContent($name, $tri = null, $start = 0, $nbr = 0, $tab = null) {
		
		global 	$bdd, $conf;

		if ($tri)
			$this->_tri = $tri;

		if (!$nbr)
			$nbr = $conf['nbr_obj'];

		$arr = array();

		//	Récupère les fichiers et répertoire du système de fichiers
		if (!$tab) {
			$tab = obj::_getDirContent($name, $this->_folder_root);
		}

		$s_o = " WHERE obj_object = '{$this->_prefix}$name' ";
		foreach ($tab as $occ) {
			$s_o .= 'OR obj_object = \''.$this->_prefix.addslashes($occ).'\' ';
		}

		$s_c = " WHERE comment_object = '$name' ";
		foreach ($tab as $occ) {
			$s_c .= 'OR comment_object = \''.addslashes($occ).'\' ';
		}

		// On récupère les infos des objets en BDD
		$sql = "SELECT	obj_object, obj_description, obj_plugin
				FROM	{$this->_object_table}
				$s_o
				GROUP 	BY obj_object
				ORDER	BY obj_object ASC";

		if (!$var = $bdd->execQuery($sql))
			trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
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
			trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
		for ($i = 0; $res = $bdd->nextTuple($var); $i++) {
			$a[$res['comment_object']]['nbr_comment'] = $res['nbr_comment'];
		}

		// On mixe les données provenant du système de fichiers et de la base de données
		$i = 0;
		foreach ($tab as $occ) {
			$tab[$i] = obj::getInfo($occ, false, false);
//			if (@$a[@$tab[$i]->file]['description'] || @$a[@$tab[$i]->file]['nbr_comment'] || @$a[@$tab[$i]->file]['plugin']) {
			if ((isset($a[$this->_prefix.$tab[$i]->file]['description']) || isset($a[$this->_prefix.$tab[$i]->file]['nbr_comment']) || isset($a[$this->_prefix.$tab[$i]->file]['plugin']))) {
				$tab[$i]->info->description = $a[$this->_prefix.$tab[$i]->file]['description'];
				$tab[$i]->info->plugin = $a[$this->_prefix.$tab[$i]->file]['plugin'];
				$tab[$i]->icon = ($tab[$i]->type == TYPE_DIR) ? (($a[$this->_prefix.$tab[$i]->file]['plugin'] == 'gallery' || $conf['dir_default_plugin'] == 'Gallery') ? 'img/mimetypes/gallery.png' : get_icon('.')) : $tab[$i]->icon;		// TODO: ATTENTION: gallery.png en dur
				$tab[$i]->info->nbr_comment = isset($a[$this->_prefix.$tab[$i]->file]['nbr_comment']) ? $a[$this->_prefix.$tab[$i]->file]['nbr_comment'] : null;
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
		$this->_nbr_object = $size;
		if ($start)
			$start = ($start >= $this->_nbr_object) ? (($this->_nbr_object - $nbr < 0) ? 0 : $this->_nbr_object - $nbr) : $start;
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

	/*	Renvoie le nombre d'objet dans le répertoire courant (faire après getDirContent)
	 */
	function getNbrObject() {
		return $this->_nbr_object;
	}

	/*	Ajout d'un commentaire à l'objet courant
		@param	string	$object L'objet demandé
		@param	string	$author	L'auteur du commentaire
		@param	string	$mail	Son email
		@param	string	$url	Son site
		@param	string	$content Le contenu
	 */
	function addComment($obj, $author, $mail, $url, $content) {
		$sql = "INSERT INTO {$this->_comment_table}
				(comment_object, comment_author, comment_mail, comment_url, comment_date, comment_content)
				VALUES
				('".$obj->file."', '$author', '$mail', '$url', '".system::time()."', '$content');";
		if (!$var = $this->_bdd->execQuery($sql))
			trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
	}

	/*	Suppression d'un commentaire par son ID
		@param	int	$id	L'id du commentaire
	 */
	function delComment($id) {
		$sql = "DELETE
				FROM	{$this->_comment_table}
				WHERE	comment_id = '$id'";
		if (!$ret = $this->_bdd->execQuery($sql))
			trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
	}

	/*	Renvoie les derniers commentaires...
		@param	int	$nbr	Nombre maximum à renvoyer
	 */
	function getLastComment($nbr = 0) {
		$tab = array();
		$sql = "SELECT	comment_id, comment_object, comment_author, comment_mail, comment_url, comment_date, comment_content
				FROM	{$this->_comment_table}
				ORDER	BY comment_date DESC";
		if (!$var = $this->_bdd->execQuery($sql))
			trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
		for ($i = 0; $res = $this->_bdd->nextTuple($var); $i++) {
			$tab[$i] = new tComment;
			$tab[$i]->id = $res['comment_id'];
			$tab[$i]->object = $res['comment_object'];
			$tab[$i]->author = $res['comment_author'];
			$tab[$i]->mail = $res['comment_mail'];
			$tab[$i]->url = $res['comment_url'];
			$tab[$i]->date = $res['comment_date'];
			$tab[$i]->content = $res['comment_content'];
		}
		return $tab;
	}

	/*	Modification d'un plugin sur un répertoire
		@param	string	$object L'objet demandé
		@param	string	$plugin	Le plugin voulu
	 */
	function setPlugin($object, $plugin) {
		global $cobj, $conf;

		// On change uniquement si le plugin est différent !
		if ($plugin != $cobj->info->plugin) {
			$sql = "SELECT obj_id FROM {$this->_object_table} WHERE obj_object = '$object'";
			if (!$var = $this->_bdd->execQuery($sql))
				trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
			else {
				$tab = $this->_bdd->fetchArray($var);
				if (!$tab) {
					$sql = "INSERT INTO {$this->_object_table}
							(obj_object, obj_plugin)
							VALUES
							('".$object."', '$plugin');";
					if (!$var = $this->_bdd->execQuery($sql))
						trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
				} else {
					$sql = "UPDATE {$this->_object_table} SET obj_plugin = '$plugin' WHERE obj_object = '$object'";
					if (!$var = $this->_bdd->execQuery($sql))
						trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
				}
			}
			$cobj->info->plugin = $plugin;
			if ($cobj->type == TYPE_DIR)
				$cobj->icon = ($plugin == 'gallery' || $conf['dir_default_plugin'] == 'Gallery') ? 'img/mimetypes/gallery.png' : get_icon('.');	// TODO: ATTENTION, mieux vaut le mettre dans act.php
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
			trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
		else {
			$tab = $this->_bdd->fetchArray($var);
			if (!$tab) {
				$sql = "INSERT INTO {$this->_object_table}
						(obj_object, obj_description)
						VALUES
						('".$object."', '$content');";
				if (!$var = $this->_bdd->execQuery($sql))
					trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
			} else {
				$sql = "UPDATE {$this->_object_table} SET obj_description = '$content' WHERE obj_object = '$object'";
				if (!$var = $this->_bdd->execQuery($sql))
					trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
			}
		}
	}

	/*	Ajouter un téléchargement à l'objet courant
	 */
	function addDownload() {
		global $cobj;
		$sql = "SELECT obj_id FROM {$this->_object_table} WHERE obj_object = '{$cobj->file}'";
//		if ($cobj->info->dcount && !$var = $this->_bdd->execQuery($sql))
		if (!$var = $this->_bdd->execQuery($sql))
			trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
		else {
			$tab = $this->_bdd->fetchArray($var);
			if (!$tab) {
				$sql = "INSERT INTO {$this->_object_table}
						(obj_object, obj_dcount)
						VALUES
						('{$cobj->file}', '1');";
				if (!$var = $this->_bdd->execQuery($sql))
					trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
			} else {
				$sql = "UPDATE {$this->_object_table} SET obj_dcount = obj_dcount + 1 WHERE obj_object = '{$cobj->file}'";
				if (!$var = $this->_bdd->execQuery($sql))
					trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
			}
		}
	}

	/*	Supprime l'objet
		@param	object	$obj	L'objet concerné !
	 */
	function delete($obj) {

		global $conf;
		$ret = false;

		switch ($obj->type) {
			case TYPE_FILE:
				if (unlink($obj->realpath)) {
					$sql = "DELETE
							FROM	{$this->_object_table}
							WHERE	obj_object = '{$obj->file}'";
					if (!$ret = $this->_bdd->execQuery($sql))
						trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
					$sql = "DELETE
							FROM	{$this->_comment_table}
							WHERE	comment_object = '{$obj->file}'";
					if (!$ret = $this->_bdd->execQuery($sql))
						trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
				}
				break;

			case TYPE_DIR:
				$ret = file::rmDirs($obj->realpath);
				$sql = "DELETE
						FROM	{$this->_object_table}
						WHERE	obj_object LIKE '{$obj->file}%'";
				if (!$ret = $this->_bdd->execQuery($sql))
					trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
				$sql = "DELETE
						FROM	{$this->_comment_table}
						WHERE	comment_object LIKE '{$obj->file}%'";
				if (!$ret = $this->_bdd->execQuery($sql))
					trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
				break;
		}

		return $ret;
	}

	/*	Copie l'objet vers la destination spécifié
		@param	string	$copy			L'objet
		@param	string	$destination	La destination de l'objet
		@param	string	$base			La base si elle est autre que $this->_folder_root
		@param	string	$prefix			Le prefix à utiliser (utile pour les fichier anonyme et la corbeille)
	 */
	function move($copy, $destination, $base = null, $prefix = null) {

		global $conf;
		$ret = null;

		$base = $base ? $base : $this->_folder_root;
		$file = substr($copy, strlen($base), (strlen($copy) - strlen($base)));

		if (is_file($copy)) {
			$destination = ($destination != '/' ? $destination.'/' : '/').basename($copy);
			$ret = rename($copy, $this->_folder_root.$destination);
			if ($ret) {
				$sql = "UPDATE {$this->_object_table} SET obj_object = '$destination' WHERE obj_object = '$prefix$file'";
				if (!$var = $this->_bdd->execQuery($sql))
					trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
				$sql = "UPDATE {$this->_comment_table} SET comment_object = '$destination' WHERE comment_object = '$prefix$file'";
				if (!$var = $this->_bdd->execQuery($sql))
					trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
				$ret = 1;
			}
		} else if (is_dir($copy)) {
			if ($ret = file::copyDir($copy, $this->_folder_root.$destination, $conf['dir_chmod']))
				file::rmDirs($copy);
			if ($ret) {
				$destination = ($destination != '/' ? $destination.'/' : '/').basename($copy);
				$file = substr($copy, strlen($base), (strlen($copy) - strlen($base)) - 1);
				$sql = "UPDATE {$this->_object_table} SET obj_object = REPLACE(obj_object, '$file', '$destination') WHERE obj_object LIKE '$prefix$file%'";
				if (!$var = $this->_bdd->execQuery($sql))
					trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);	
				$sql = "UPDATE {$this->_comment_table} SET comment_object = REPLACE(comment_object, '$file', '$destination') WHERE comment_object LIKE '$prefix$file%'";
				if (!$var = $this->_bdd->execQuery($sql))
					trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
			}
		}

		return $ret;
	}

	/*	Renomme un objet
		@param	string	$name		L'objet
		@param	string	$newname	Son nouveau nom
	 */
	function rename($name, $newname) {
		$ret = null;
		$newname = (dirname($name) == '/' ? null : dirname($name)).'/'.$newname;
		if (is_file($this->_folder_root.$name)) {
			$ret = rename($this->_folder_root.$name, $this->_folder_root.$newname);
			if ($ret) {
				$sql = "UPDATE {$this->_object_table} SET obj_object = '$newname' WHERE obj_object = '$name'";
				if (!$var = $this->_bdd->execQuery($sql))
					trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
				$sql = "UPDATE {$this->_comment_table} SET comment_object = '$newname' WHERE comment_object = '$name'";
				if (!$var = $this->_bdd->execQuery($sql))
					trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
				$ret = 1;
			}
		} else if ($this->_folder_root.$name) {
			$ret = rename($this->_folder_root.$name, $this->_folder_root.$newname);
			if ($ret) {
				$newname .= '/';
				$sql = "UPDATE {$this->_object_table} SET obj_object = REPLACE(obj_object, '$name', '$newname') WHERE obj_object LIKE '$name%'";
				if (!$var = $this->_bdd->execQuery($sql))
					trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);	
				$sql = "UPDATE {$this->_comment_table} SET comment_object = REPLACE(comment_object, '$name', '$newname') WHERE comment_object LIKE '$name%'";
				if (!$var = $this->_bdd->execQuery($sql))
					trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
			}
		}
		return $ret;
	}

	/*	Renvoie le contenu d'un répertoire trié comme on veut
		@access	 private
	 */
	function _getDirContent($folder, $base) {

		global $conf;
		$tab = array();
		
		$hdl = dir($base.$folder);
		if ($hdl) {
			while (false !== ($occ = $hdl->read())) {

				if ($folder == '/' && $occ{0} == '.' && isset($occ{1}) && $occ{1} == '.')
					continue;

				if ($occ == '.')
					continue;

				// Si on a un fichier caché...
				if ($occ{0} == '.' && isset($occ{1}) && $occ{1} != '.' && !$conf['view_hidden_file']) {
					continue;
				}

				$tab[] = $folder.$occ.(is_dir($this->_folder_root.$folder.$occ) ? '/' : null);
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

			$a->extension = strtolower($a->extension);
			$b->extension = strtolower($b->extension);
			if (!strcmp($a->extension, $b->extension))
				return strcmp(basename($a->name), basename($b->name));
			else
				return strcmp($a->extension, $b->extension);
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

			$a = strtolower($a);
			$b = strtolower($b);

			$a_ext = file::getExtension($a);
			$b_ext = file::getExtension($b);

			if (!strcmp($a_ext, $b_ext))
				return strcmp(basename($a), basename($b));
			else
				return strcmp($a_ext, $b_ext);

//			return strcmp(strtolower(file::getExtension($a)), strtolower(file::getExtension($b)));
		}
		return $ret;
	}

}

?>
