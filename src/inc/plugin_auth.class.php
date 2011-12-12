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

class plugin_auth {

	var $_auth_obj;

	function plugin_auth() {
		$this->_auth_obj = null;
	}

	/*	Charge le plugin et l'instancie
	 */
	function load() {
		global $conf;

		session_name('Hyla');
		session_start();
		
		$auth_method = (!isset($conf['auth_method']) || empty($conf['auth_method'])) ? 'default' : $conf['auth_method'];

		if (file_exists(DIR_PLUGINS_AUTH.$auth_method)) {
			include_once(DIR_PLUGINS_AUTH.$auth_method.'/index.php');

			$cname = 'plugin_auth_'.$auth_method;
			$this->_auth_obj = new $cname();
		}

		if (!is_object($this->_auth_obj)) {
			system::end(__('Fatal error : Authentification Plugin not present !'));
		}
	}

	/*	Charge le plugin d'authentification adéquate et essaie d'authentifier l'utilisateur
		@return	Retourne un objet de type tUser si l'authentification à réussi
	 */
	function auth($name, $password) {
		return $this->_auth_obj->auth($name, $password);
	}

	/*	Renvoie les infos de l'utilisateur courant
	 */
	function getUser() {

		global $conf;

		if (isset($_SESSION['sess_cuser_id']) && $_SESSION['sess_cuser_id']) {
			$usr = new users();
			$ret = $usr->getUser($_SESSION['sess_cuser_id']);
		} else {
			$usr = new users();
			$ret = $usr->getUser(ANONYMOUS_ID);
			if (!$ret) {
				system::end(__('Fatal error : Anonymous user not present !'));
			}
			$_SESSION['sess_cuser_id'] = $ret->id;
		}
		
		return $ret;
	}

	/*	"Déloggue" l'utilisateur courant
	 */
	function logout() {
		session_destroy();
	}
}

?>
