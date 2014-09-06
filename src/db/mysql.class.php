<?php
/*
    This file is part of Hyla
    Copyright (c) 2004-2012 Charles Rincheval.
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
    /*  Connection serveur
     */
    private $_db_host;      // Serveur MySQL
    private $_db_user;      // Login de la base
    private $_db_pass;      // Mot de Passe de la base
    private $_db_base;      // Base de données

    /*  Attribut divers
     */
    private $_id_bdd;       // ID de la connexion à la base
    private $_id_query;     // ID de la dernière requête
    private $_nbr_query;    // Comptage des requêtes
    private $_last_query;   // La dernière requête


    /*  Le constructeur...
     */
    public function __construct() {
        $this->_db_host = null;
        $this->_db_user = null;
        $this->_db_pass = null;
        $this->_db_base = null;

        $this->_id_bdd = null;
        $this->_id_query = null;

        $this->_nbr_query = 0;
        $this->_last_query = null;
    }

    /*  Connection au serveur...
        @param  string  $_db_host   Serveur SQL
        @param  string  $_db_user   User
        @param  string  $_db_pass   Password
     */
    public function connect($_db_host, $_db_user, $_db_pass, $_db_base) {

        $this->_db_host = $_db_host;
        $this->_db_user = $_db_user;
        $this->_db_pass = $_db_pass;
        $this->_db_base = $_db_base;

        // Connexion à la base de données
        $this->_id_bdd = new mysqli( $this->_db_host, $this->_db_user, $this->_db_pass, $this->_db_base);
		$err = mysqli_connect_error();
		if ( $err !== null) {
			trigger_error( __( 'Couldn\'t connect to sql server ! '.$err ));
		}

		// Pour l'UTF8
		$this->_id_bdd->set_charset( 'utf8'); 
		$this->_id_bdd->query( 'SET character_set_server = utf8');
		
        return $this->_id_bdd;
    }

    /*  Fermeture de la base de données
     */
    public function close() {
		if (!$ret = $this->_id_bdd->close()) {
			trigger_error(__('Couldn\'t close connection to sql server !'), E_USER_ERROR);
		}
		return $ret;
    }

    /*  Exécution d'une requête
     */
    public function execQuery( $qry) {
        $this->_nbr_query++;

		$this->_last_query = $qry;
        $this->_id_query = $this->_id_bdd->query( $qry);

        return $this->_id_query;
    }

    /*  Renvoie le nombre de requêtes exécuté en tout !
     */
    public function getNbrQuery() {
        return $this->_nbr_query;
    }

    /*  Retourne le tuple suivant !
     */
    public function nextTuple( $_id_query = null) {
        $_id_query = ($_id_query == null) ? $this->_id_query : $_id_query;
        return $this->fetchAssoc( $_id_query);
    }

    /* Reset tuple
     */
    public function reset() {
        return $this->_id_query->seek( 0);
    }

    /*  Retourne une ligne de résultat sous la forme d'un tableau indice
     */
    public function fetchArray( $_id_query = null) {
        $_id_query = ($_id_query == null) ? $this->_id_query : $_id_query;
        return $_id_query->fetch_array();
    }

    /*  Retourne une ligne de résultat sous la forme d'un tableau associatif
     */
    public function fetchAssoc($_id_query = null) {
        $_id_query = ($_id_query == null) ? $this->_id_query : $_id_query;
        return $_id_query->fetch_assoc();
    }

    /*  Retourne le nombre de ligne d'un résultat
     */
    public function getNumRows($_id_query = null) {
        $_id_query = ($_id_query == null) ? $this->_id_query : $_id_query;
        return $_id_query->num_rows();
    }

    /*  Efface le résultat de la mémoire
     */
    public function freeResult($_id_query = null) {
        $_id_query = ($_id_query == null) ? $this->_id_query : $_id_query;
        return $_id_query->free_result();
    }

    /*  Retourne l'identifiant généré par la dernière requête INSERT
     */
    public function getInsertID() {
		return $this->_id_bdd->insert_id;
    }

    /*  Retourne le numéro d'erreur et l'erreur
     */
    public function getError() {
        $error['message'] = $this->_id_bdd->error;
        $error['code'] = $this->_id_bdd->errno;
        $error['query'] = $this->_last_query;
        return $error;
    }

    public function getErrorMsg() {
        return $this->_id_bdd->error;
    }
}
