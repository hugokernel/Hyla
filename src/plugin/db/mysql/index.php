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

class plugin_db_mysql extends plugin_db {

    var $_base;
    var $last_resource;

    /**
     *  Constructor
     */
    function plugin_db_mysql() {
        parent::plugin_db();

        $this->_base = null;
        $this->_last_resource = null;
    }

    /**
     *  Get driver name
     */
    function getDriverName() {
        return 'mysql';
    }

    /**
     *  Server connection
     *  @param  string  $host   Sql host
     *  @param  string  $user   User
     *  @param  string  $pass   Password
     */
    function connect($host, $user, $pass) {

        // Connexion à la base de données
        if (!$this->_id_bdd = mysql_connect($host, $user, $pass)) {
            system::log(L_FATAL, __('Couldn\'t connect to sql server !'));
        }

        // Pour l'UTF8
        mysql_query("SET NAMES 'utf8'");
        mysql_query("SET character_set_server = utf8");

        return $this->_id_bdd;
    }

    /**
     *  Database selection
     *  @param  string  $base   Base de données
     */
    function select($base) {
        $this->_base = $base;

        $db = mysql_select_db($this->_base, $this->_id_bdd);
        if (!$db) {
            system::log(L_FATAL, __('Unable to use database &laquo; %s &raquo;', $this->_db_base));
        }
        return $db;
    }

    /**
     *  Close connection
     */
    function close($id_bdd = null) {
        if ($id_bdd == null) {
            $id_bdd = $this->_id_bdd;
        }

        if (!$ret = mysql_close($this->_id_bdd)) {
            system::log(L_FATAL, __('Couldn\'t close connection to sql server !'));
        }
        return $ret;
    }

    /**
     *  Run query
     *  @param  string  $qry    Query
     */
    function execQuery($qry, $_id_bdd = null) {

        if (!$_id_bdd) {
            $_id_bdd = $this->_id_bdd;
        }

        $this->_last_resource = mysql_query($qry, $_id_bdd);
//dlog($qry);
        if (!$this->_last_resource) {
            system::log(L_ERROR, $this->getErrorMsg(), 'global:sql');
            $this->_last_resource = 0;
        }

        $this->_last_query = $qry;
        $this->_query_count++;

        return $this->_last_resource;
    }

    /**
     *  Quote string
     *  @param  string  $str    String
     */
    function quote($str) {
        if(version_compare(phpversion(),"4.3.0") == '-1') {
            return mysql_escape_string($str);
        } else {
            return mysql_real_escape_string($str);
        }
    }

    /**
     *  Get next data
     */
    function nextTuple($last_resource = null) {
        $last_resource = (!$last_resource) ? $this->_last_resource : $last_resource;
        return $this->fetchAssoc($last_resource);
    }

    /**
     *  Reset
     */
    function reset() {
        $last_resource = (!$last_resource) ? $this->_last_resource : $last_resource;
        return mysql_data_seek($last_resource, 0);
    }

    /**
     *  Get result line in an array
     */
    function fetchArray($last_resource = null) {
        $last_resource = (!$last_resource) ? $this->_last_resource : $last_resource;
        return mysql_fetch_array($last_resource);
    }

    /**
     *  Get result line in an assoc array
     */
    function fetchAssoc($last_resource = null) {
        $last_resource = (!$last_resource) ? $this->_last_resource : $last_resource;
        return mysql_fetch_assoc($last_resource);
    }

    /**
     *  Get the line count
     */
    function getNumRows($last_resource = null) {
        $last_resource = (!$last_resource) ? $this->_last_resource : $last_resource;
        return mysql_num_rows($last_resource);
    }

    /**
     *  Free result !
     */
    function freeResult($last_resource = null) {
        $last_resource = (!$last_resource) ? $this->_last_resource : $last_resource;
        return mysql_free_result($last_resource);
    }

    /**
     *  Get the last inserted id
     */
    function getInsertID($id_bdd = null) {
        $id_bdd = ($id_bdd) ? $id_bdd : $this->_id_bdd;
        return mysql_insert_id($id_bdd);
    }

    /**
     *  Get error in an array
     */
    function getError() {
        return array(   'message'   =>  mysql_error($this->_id_bdd),
                        'code'      =>  mysql_errno($this->_id_bdd),
                        'query'     =>  $this->_last_query
                    );
    }

    function getErrorMsg() {
        return mysql_error($this->_id_bdd);
    }
}

?>
