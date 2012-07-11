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
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Hyla; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA     02111-1307  USA
 */

class plugin_auth_default extends plugin_auth {

    /*  Initialisations
     */
    function plugin_auth_default() {
        parent::plugin_auth();
    }

    /*  Phase d'authentification
        @param  $name       Le nom de l'utilisateur
        @param  $password   Le mot de passe associé
     */
    function auth($name, $password) {
        $usr = new users();
        return $usr->auth($name, $password);
    }
}

?>
