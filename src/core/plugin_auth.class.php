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

class plugin_auth extends plugin {

    function plugin_auth() {
        parent::plugin();
    }

    /*  Charge le plugin et l'instancie
     */
    function load($id = null) {
        session_name('Hyla');

        // Id overflow 
        if ($id) {
            session_id($id);
        }

        session_start();
    }

    /*  Renvoie les infos de l'utilisateur courant
     */
    function getUser() {

        $usr = new users();
        if (isset($_SESSION['sess_cuser_id']) && $_SESSION['sess_cuser_id']) {
            $ret = $usr->getUser($_SESSION['sess_cuser_id']);
        } else {
            $ret = $usr->getUser(ANONYMOUS_ID);
            if (!$ret) {
                system::end(__('Fatal error : Anonymous user not present !'));
            }
            $_SESSION['sess_cuser_id'] = $ret->id;
        }

        return $ret;
    }

    /*  "Déloggue" l'utilisateur courant
     */
    function logout() {
        return session_destroy();
    }
}

?>
