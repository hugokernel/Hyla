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

	function getPage($aff, $object = null, $act = null, $pact = null, $paff = null) {
		$tab = array('page');
		if (is_array($aff))
			$tab = array_merge($tab, $aff);
		else
			$tab[] = $aff;
		return url::_get($object, $tab, $act, $pact, $paff, false);
	}

	function getObj($object, $aff = '', $act = null, $pact = null, $paff = null) {
		return url::_get($object, $aff, $act, $pact, $paff, true);
	}

	function getCurrentObj($aff = '', $act = null, $pact = null, $paff = null) {
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
	 */
	function _get($object, $aff = '', $act = null, $pact = null, $paff = null, $b = true) {

		global $conf;
		$s = ROOT_URL.'/index.php';		// ToDo: Remplacer par PHP_SELF ou autre...

		if ($conf['url_scan'] == 'QUERY_STRING') {

			$s .= '?p='.(($object && $b) ? 'obj' : null);

			if (is_array($aff)) {
				$s .= ($object && $b) ? '-' : null;
				foreach ($aff as $a) {
					$s .= $a.'-';
				}
				$s = substr($s, 0, strlen($s) - 1);
			} else {
				$s .= $aff ? '-'.$aff : null;
			}

			$sep = ',';

			if (is_string($object))
				$s .= $sep.$object;
//				$s .= $sep.urlencode($object);

			if ($act) {
				if (is_array($act)) {
					foreach ($act as $a) {
						$s .= $a.'-';
					}
					$s = substr($s, 0, strlen($s) - 1);
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

	/*	Scan l'url et affecte les variables $aff, $act et renvoie le tableau d'infos de l'objet
	 */
	function scan() {

		global $conf;

		$url = new tUrl;

		if ($conf['url_scan'] == 'QUERY_STRING') {

			if (array_key_exists('p', $_REQUEST)) {
				@list($url->aff, $url->obj) = explode(',', $_REQUEST['p']);
			}

			if (array_key_exists('act', $_REQUEST)) {
				$url->act = $_REQUEST['act'];
			}

			$url->act = explode('-', $url->act);
			$url->aff = explode('-', $url->aff);

//			$url->pact = explode('-', $url->pact);
//			$url->paff = explode('-', $url->paff);

/*
			@list($aff, $object) = explode(',', $_REQUEST['aff']);
			$aff = explode('-', $aff);
*/

//			$object = ($object) ? $object : $actobject;

			$url->paff = isset($_REQUEST['paff']) ? $_REQUEST['paff'] : null;
			$url->pact = isset($_REQUEST['pact']) ? $_REQUEST['pact'] : null;
		}
/*
		else if ($this->_mode == 'PATH_INFO') {
			$exp = preg_quote('#'.ROOT_URL, '/').'\/index.php\/(.*)#se';
			preg_match($exp, $_SERVER['REQUEST_URI'], $match);

			$slpos = strpos(@$match[1], '/');
			if ($slpos) {
				$aff = substr(@$match[1], 0, $slpos);
				$object = substr(@$match[1], $slpos);
			} else
				$aff = @$match[1];

			if (!$object)
				$object = '/';
		}
*/

//		$this->_current_obj = obj::getInfo($object);
//		return $this->_current_obj;
		return $url;
	}
}

?>
