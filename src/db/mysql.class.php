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

class db
{
	/*	Connection serveur
	 */
	var $_db_host;		// Serveur MySQL
	var $_db_user;		// Login de la base
	var $_db_pass;		// Mot de Passe de la base
	var $_db_base;		// Base de données

	/*	Attribut divers
	 */
	var $_id_bdd;		// ID de la connexion à la base
	var $_id_query;		// ID de la dernière requête
	var $_nbr_query;	// Comptage des requêtes
	var $_last_query;	// La dernière requête


	/*	Le constructeur...
	 */
	function db()
	{
		$this->_db_host = null;
		$this->_db_user = null;
		$this->_db_pass = null;
		$this->_db_base = null;
	
		$this->_id_bdd = null;
		$this->_id_query = null;
		
		$this->_nbr_query = 0;
		$this->_last_query = null;
	}

	/*	Connection au serveur...
		@param string $_db_host Serveur SQL
		@param string $_db_user User
		@param string $_db_pass Password
		@param string $_db_base Base de données
	 */
	function connect($_db_host, $_db_base, $_db_user, $_db_pass)
	{
		$this->_db_host = $_db_host;
		$this->_db_user = $_db_user;
		$this->_db_pass = $_db_pass;
		$this->_db_base = $_db_base;
		
		//extension_loaded('mysql');
		
		// Connexion à la base de données
		if (@!defined(ID_BDD))
		{
			if (!$this->_id_bdd = mysql_pconnect($this->_db_host, $this->_db_user, $this->_db_pass))
				trigger_error(__('Couldn\'t connect to sql server !'), E_USER_ERROR);
			else
				define('ID_BDD', $this->_id_bdd);

			// Sélection de la base de données
			$db = mysql_select_db($this->_db_base, $this->_id_bdd);
			if (!$db)
				trigger_error(__('Unable to use database &laquo; %s &raquo;', $this->_db_base), E_USER_ERROR);
		}
		else
			$this->_id_bdd = ID_BDD;

		return $this->_id_bdd;
	}

	/*	Fermeture de la base de données
	 */
	function close($_id_bdd = null)
	{
		if ($_id_bdd == null)
			$_id_bdd = $this->_id_bdd;
		if (!$ret = mysql_close($this->_id_bdd))
			trigger_error(__('Couldn\'t close connection to sql server !'), E_USER_ERROR);
		return $ret;
	}

	/*	Exécution d'une requête
	 */
	function execQuery($qry, $_id_bdd = null)
	{
		$this->_nbr_query++;

		if (!$_id_bdd)
			$_id_bdd = $this->_id_bdd;
		$this->_last_query = $qry;
		$this->_id_query = mysql_query($qry, $_id_bdd);
			
		return $this->_id_query;
	}

	/*	Renvoie le nombre de requêtes exécuté en tout !
	 */
	function getNbrQuery() {
		return $this->_nbr_query;
	}

	/*	Retourne le tuple suivant !
	 */
	function nextTuple($_id_query = null) {
		$_id_query = ($_id_query == null) ? $this->_id_query : $_id_query;
		return $this->fetchAssoc($_id_query);
	}

	/* Reset tuple
	 */
	function reset() {
		$_id_query = ($_id_query == null) ? $this->_id_query : $_id_query;
		return mysql_data_seek($_id_query, 0);
	}

	/*	Retourne une ligne de résultat sous la forme d'un tableau associatif
	 */
	function fetchArray($_id_query = null)
	{
		$_id_query = ($_id_query == null) ? $this->_id_query : $_id_query;
		return mysql_fetch_array($_id_query);
	}

	/*	Retourne une ligne de résultat sous la forme d'un tableau associatif
	 */
	function fetchAssoc($_id_query = null) {
		$_id_query = ($_id_query == null) ? $this->_id_query : $_id_query;
		return mysql_fetch_assoc($_id_query);
	}

	/*	Retourne le nombre de ligne d'un résultat
	 */
	function getNumRows($_id_query = null)
	{
		$_id_query = ($_id_query == null) ? $this->_id_query : $_id_query;
		return mysql_num_rows($_id_query);
	}

	/*	Efface le résultat de la mémoire
	 */
	function freeResult($_id_query)
	{
		$_id_query = ($_id_query == null) ? $this->_id_query : $_id_query;
		return mysql_free_result($_id_query);
	}

	/*	Retourne l'identifiant généré par la dernière requête INSERT
	 */
	function getInsertID($_id_bdd = null) {
		if (!$_id_bdd)
			$_id_bdd = $this->_id_bdd;
		return mysql_insert_id($_id_bdd);
	}

	/*	Retourne le numéro d'erreur et l'erreur
	 */
	function getError() {
		$error['message'] = mysql_error($this->_id_bdd);
		$error['code'] = mysql_errno($this->_id_bdd);
		$error['query'] = $this->_last_query;
		return $error;
	}

	function getErrorMsg() {
		return mysql_error($this->_id_bdd);
	}
}

?>
