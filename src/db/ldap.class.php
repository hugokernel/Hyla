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

class ldap
{
    /*  Connection serveur
     */
    var $_ldap_host;        // Serveur Ldap
    var $_ldap_user;        // Dn de connexion à l'annuaire
    var $_ldap_pass;        // Mot de Passe pour se connecter à l'annuaire
    var $_ldap_basedn;      // Dn de base pour les recherches

    /*  Attribut divers
     */
    var $_id_bdd;       // ID de la connexion à la base


    /*  Le constructeur...
     */
    function ldap() {
        $this->_ldap_host = null;
        $this->_ldap_user = null;
        $this->_ldap_pass = null;
        $this->_ldap_base = null;

        $this->_id_conn = null;
    }

    /*  Connection au serveur...
        @param  string  $_ldap_host Serveur ldap
        @param  string  $_ldap_user Dn
        @param  string  $_ldap_pass Password
     */
    function bind($_ldap_host, $_ldap_user, $_ldap_pass) {

        $this->_ldap_host = $_ldap_host;
        $this->_ldap_user = $_ldap_user;
        $this->_ldap_pass = $_ldap_pass;

        // Connexion à l'annuaire
        $id_conn = ldap_connect($this->_ldap_host);

        if (!$id_conn)
            trigger_error(__('Couldn\'t connect to ldap server !'));

        $this->_id_conn = $id_conn;
        @ldap_set_option($this->_id_conn, LDAP_OPT_PROTOCOL_VERSION, 3);

        $ldap_bind = ldap_bind($this->_id_conn, $this->_ldap_user, $this->_ldap_pass);

        return $ldap_bind;
    }
}

?>
