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

	You should have received a copy of the GNU General Public License
	along with Hyla; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

class tUser {
	var $id;
	var $name;
	var $perm;
}

class users {

	var		$_bdd;

	var		$_users_table;

	function users() {
		global 	$bdd;
		$this->_bdd = &$bdd;
		$this->_users_table = TABLE_USERS;
	}

	/*	Tente d'authentifier l'utilisateur
		@param	string	$login		Le login
		@param	string	$password	Le mot de passe
		@return	Null en cas d'erreur sinon, renvoie un objet tUser
	 */
	function auth($login, $password) {
		$ret = null;
		$sql = "SELECT	usr_id, usr_name, usr_password_hash, usr_perm
				FROM	{$this->_users_table}
				WHERE	usr_name = '$login'";
		if (!$var = $this->_bdd->execQuery($sql))
			trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
		else {
			$res = $this->_bdd->fetchAssoc($var);
			if (crypt($password, CRYPT_SALT) == $res['usr_password_hash']) {
				$ret = new tUser;
				$ret->id = $res['usr_id'];
				$ret->name = $res['usr_name'];
				$ret->perm = $res['usr_perm'];
			}
		}

		return $ret;
	}

	/*	Ajoute un utilisateur
		@param	string	$name		Le nom
		@param	string	$password	Le mot de passe en clair
		@return	Renvoie l'id du nouvel utilisateur
	 */
	function addUser($name, $password) {
		$ret = null;
		$password = crypt($password, CRYPT_SALT);
		$sql = "INSERT INTO {$this->_users_table}
				(usr_name, usr_password_hash)
				VALUES
				('$name', '$password');";
		if (!$var = $this->_bdd->execQuery($sql))
			trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
		$ret = $this->getUser($name);
		return $ret->id;
	}

	/*	Retourne la structure tUser contenant les infos de l'utilisateur demandé
	 */
	function getUsers() {
		$tab = array();
		$sql = "SELECT	usr_id, usr_name, usr_perm
				FROM	{$this->_users_table}
				ORDER	BY usr_id ASC";
		if (!$var = $this->_bdd->execQuery($sql))
			trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
		for ($i = 0; $res = $this->_bdd->nextTuple($var); $i++) {
			$tab[$i] = new tUser;
			$tab[$i]->id = $res['usr_id'];
			$tab[$i]->name = $res['usr_name'];
			$tab[$i]->perm = $res['usr_perm'];
		}
		return $tab;
	}

	/*	Retourne un tableau contenant la liste des utilisateurs
		@param	int	$id	L'id de l'utilisateur voulu ou le son nom
	 */
	function getUser($id) {
		$ret = null;
		$rsql = (is_numeric($id)) ? "usr_id = '$id'" : "usr_name = '$id'";
		$sql = "SELECT	usr_id, usr_name, usr_perm
				FROM	{$this->_users_table}
				WHERE	$rsql";
		if (!$var = $this->_bdd->execQuery($sql))
			trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
		else {
			if ($res = $this->_bdd->fetchAssoc($var)) {
				$ret = new tUser;
				$ret->id = $res['usr_id'];
				$ret->name = $res['usr_name'];
				$ret->perm = $res['usr_perm'];
			}
		}
		return $ret;
	}

	/*	Modifie les permissions d'un utilisateur
		@param	int		$id		L'utilisateur
		@param	string	$perm	Les permissions
	 */
	function setPerm($id, $perm) {
		$sql = "UPDATE {$this->_users_table} SET usr_perm = '$perm' WHERE usr_id = '$id'";
		if (!$var = $this->_bdd->execQuery($sql))
			trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
	}

	/*	Modifie le mot de passe de l'utilisateur
		@param	int		$id			L'utilisateur
		@param	string	$password	Le mot de passe
	 */
	function setPassword($id, $password) {
		$password = crypt($password, CRYPT_SALT);
		$sql = "UPDATE {$this->_users_table} SET usr_password_hash = '$password' WHERE usr_id = '$id'";
		if (!$var = $this->_bdd->execQuery($sql))
			trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
	}

	/*	Supprime un utilisateur
		@param	int	$id	L'id de l'utilisateur
	 */
	function delUser($id) {
		$sql = "DELETE
				FROM	{$this->_users_table}
				WHERE	usr_id = '{$id}'";
		if (!$ret = $this->_bdd->execQuery($sql))
			trigger_error($this->_bdd->getErrorMsg(), E_USER_ERROR);
	}

	/*	Validation des noms des users à l'inscription
		@param	string	$login	Login à valider
		return -1 si le login n'est pas valide, 0 si l'utilisateur existe déjà, 1 si tout est ok
	 */
	function testLogin($login) {
		$ret = -1;
		if (preg_match('/^[A-Z]{1}[A-Z0-9._-]{1,31}$/i', $login)) {
			$ret = (!$s = $this->getUser($login)) ? 1 : 0;
		}
		return $ret;
	}

}

?>
