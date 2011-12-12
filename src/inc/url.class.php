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

class tUrl {

	var $obj;		// L'objet courant

	var $act;		// L'action courante
	var	$aff;		// L'affichage courant

	var $pact;		// Action plugin
	var $paff;		// Affichage plugin

	function url() {
		$this->obj = null;

		$this->act = array();
		$this->aff = array();

		$this->pact = array();
		$this->paff = array();
	}
}


class url {

	var	$abs;

	var $current;

	/*	Initialisation
	 	@param	bool	$abs	Créer des url absolue ou non ?
	 */
	function url($abs = false) {
		$this->abs = $abs;
		$this->current = new tUrl;
	}

	/*	Renvoie l'url correspondante pour les rss
		@param	string	$obj	L'objet
		@param	string	$type	Type !
		@access	static
	 */
	function getRss($obj, $type = null) {
		$s = REAL_ROOT_URL;
		$s .= '/rss.php';
		$s .='?p=obj,'.url::_encode($obj);
		$s .= ($type) ? '&amp;type='.$type : null;
		return $s;
	}

	/*	Renvoie l'url correspondante pour la visualisation d'une page (admin...)
		@access	static
	 */
	function getPage($aff, $object = null, $act = null, $pact = null, $paff = null) {
		$tab = array('page');
		if (is_array($aff))
			$tab = array_merge($tab, $aff);
		else
			$tab[] = $aff;
		return url::_get($object, $tab, $act, $pact, $paff, false);
	}

	/*	Renvoie l'url correspondante pour la visualisation d'un objet
		@access	static
	 */
	function getObj($object, $aff = null, $act = null, $pact = null, $paff = null) {
		return url::_get($object, $aff, $act, $pact, $paff, true);
	}

	/*	Renvoie l'url correspondante pour la visualisation de l'objet courant
		@access	static
	 */
	function getCurrentObj($aff = null, $act = null, $pact = null, $paff = null) {
		global $cobj;
		$s = (isset($cobj->target) ? $cobj->file.'!'.$cobj->target : $cobj->file);
		$s = $s ? $s : '/';
		$s = url::_get($s, $aff, $act, $pact, $paff, true);
		return $s;
	}

	/*	Renvoie une url correctement constituée
		@param	string	$object	L'objet en question
		@param	int		$aff	L'affichage : download, info, edit, mini...
		@param	int		$act	L'action : addcomment
		@param	string	$pact	Les paramètres action à passer
		@param	string	$paff	Les paramètres affichage à passer
		@param	bool	$b		Permet de forcer la génération d'un 'obj'
		@access	static
	 */
	function _get($object, $aff = '', $act = null, $pact = null, $paff = null, $b = true) {

		global $conf, $url;

		$s = ($url->abs) ? url::getHost().REAL_ROOT_URL.'/index.php' : REAL_ROOT_URL.'/index.php';

		if ($conf['url_scan'] == 'QUERY_STRING') {

			$s .= '?p='.(($object && $b) ? 'obj' : null);

			if (is_array($aff)) {
				$s .= ($object && $b) ? '-' : null;
				foreach ($aff as $a) {
					$s .= $a.'-';
				}
				$s = substr($s, 0, strlen($s) - 1);	// On enlève le - de fin
			} else {
				$s .= $aff ? '-'.$aff : null;
			}

			$sep = ',';

			if (is_string($object)) {
				$s .= $sep.url::_encode($object);
			}

			if ($act) {
				if (is_array($act)) {
					$s_a = null;
					foreach ($act as $a) {
						$s_a .= $a.'-';
					}
					$s .= '&amp;act='.substr($s_a, 0, strlen($s_a) - 1);
				} else {
					$s .= '&amp;act='.$act;
				}
			}

			if ($pact)
				$s .= '&amp;pact='.$pact;

			if ($paff)
				$s .= '&amp;paff='.$paff;
		}

		return $s;
	}

	/*	Encode l'objet
	 */
	function _encode($obj) {
		global $conf;
		$ret = null;

		if ($conf['url_encode']) {
			$a = array();			
			$ret = explode('/', $obj);
			foreach ($ret as $occ) {
				$a[] = urlencode($occ);
			}
			$ret = implode('/', $a);
		} else
			$ret = $obj;
		return $ret;
	}

	/*	Renvoie l'adresse complète (basé sur le code de DotClear d'Olivier Meunier)
		@access	static
	 */
	function getHost() {
		$server_name = $_SERVER['HTTP_HOST'];
		if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
			$scheme = 'https';
			$port = ($_SERVER['SERVER_PORT'] == '443') ? null : ':'.$_SERVER['SERVER_PORT'];
		} else {
			$scheme = 'http';
			$port = ($_SERVER['SERVER_PORT'] == '80') ? null : ':'.$_SERVER['SERVER_PORT'];
		}
		return $scheme.'://'.htmlspecialchars($server_name).$port;
	}


	/*
	 *	Scan de l'url et accès aux valeurs scannées
	 */


	/*	Scan l'url et affecte les variables $aff, $act et renvoie le tableau d'infos de l'objet
	 */
	function scan() {

		global $conf;

		if ($conf['url_scan'] == 'QUERY_STRING') {

			if (array_key_exists('p', $_REQUEST)) {
				@list($this->current->aff, $this->current->obj) = explode(',', $_REQUEST['p'], 2);
			}

			if (array_key_exists('act', $_REQUEST)) {
				$this->current->act = $_REQUEST['act'];
			}

			$this->current->act = explode('-', $this->current->act);
			$this->current->aff = explode('-', $this->current->aff);

			$this->current->paff = isset($_REQUEST['paff']) ? $_REQUEST['paff'] : null;
			$this->current->pact = isset($_REQUEST['pact']) ? $_REQUEST['pact'] : null;
		}
	}

	/*	Renvoie l'élément demandé
		@access	static
	 */
	function getQueryAct($num) {
		global $url;
		return isset($url->current->act[$num]) ? $url->current->act[$num] : null;
	}

	/*	Renvoie l'élément demandé
		@access	static
	 */
	function getQueryAff($num) {
		global $url;
		return isset($url->current->aff[$num]) ? $url->current->aff[$num] : null;
	}

	/*	Renvoie l'élément demandé
		@access	static
	 */
	function getQueryPact() {
		global $url;
		return isset($url->current->pact) ? $url->current->pact : null;
	}

	/*	Renvoie l'élément demandé
		@access	static
	 */
	function getQueryPaff() {
		global $url;
		return isset($url->current->paff) ? $url->current->paff : null;
	}

	/*	Renvoie l'élément demandé
		@access	static
	 */
	function getQueryObj() {
		global $url;
		return isset($url->current->obj) ? $url->current->obj : null;
	}

	/*	Set l'élément demandé
		@access	static
	 */
	function setQueryObj($obj) {
		global $url;
		$url->current->obj = $obj;
		return $url->current->obj;
	}

	/*	Set l'élément demandé
		@access	static
	 */
	function setQueryAff($num, $val) {
		global $url;
		$url->current->aff[$num] = $val;
		return $url->current->aff[$num];
	}
}

?>
