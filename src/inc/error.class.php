<?php
/*
	This file is part of iFile
	Copyright (c) 2004-2006 Charles Rincheval.
	All rights reserved

	iFile is free software; you can redistribute it and/or modify it
	under the terms of the GNU General Public License as published
	by the Free Software Foundation; either version 2 of the License,
	or (at your option) any later version.

	iFile is distributed in the hope that it will be useful, but
	WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with iFile; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

// Erreur SQL...
define('MSG_ERROR_SQL_CONNECT',		'Erreur durant la connection au serveur SQL !');
define('MSG_ERROR_SQL_SELECT_BDD',	'Erreur durant la sélection de la base SQL !');
define('MSG_ERROR_SQL_CLOSE',		'Erreur durant la fermeture de la connection au serveur SQL !');

//require 'format.class.php';

/*

<?xml version="1.0" encoding="ISO-8859-1" ?>

<content>
	<error type="" file="" line="">
		<page></page>
		<msg></msg>
		<msgext></msgext>
	</error>
</content>


*/

/* Les codes des erreurs...
 */
define('ERROR_FATAL', 1);
define('ERROR_WARNING', 2);
define('ERROR_EXEC_QUERY', 3);


/*	Structure erreur
 */
class tError {
	// Infos erreur
	var $type;		// Le type d'erreur (sql, file...)
	var $niv;		// Le niveau (critique, warning...)
	var $file;		// Le fichier concerné
	var $line;		// La ligne de l'erreur
	var $page;		// La page en cours
	var $msg;		// Le message de l'erreur
	var $msgext;	// PLus d'info
	
	// Infos diverses
	var $date;
	var $user;
}

class error	// extends format
{
	var $_niv;
	var $_msg;
	var $_serror;
	
	/*	Les constructeur
	 */
	function error($_niv_error)
	{
		$this->_niv = $_niv_error;
		$this->_msg = null;
		$this->_serror = null;
	}
	
	/*	Affichage d'erreur...
		@param string $file Fichier oû l'erreur se trouve
		@param int $line Ligne de l'erreur
		@param string|object $st Objet ou message
		@param string $msg Message complémentaire
	 */
	function log($file, $line, $st, $msg = null)
	{
		$this->_serror = new tError;
		$this->_serror->file = $file;
		$this->_serror->line = (int)$line;
		$this->_serror->page = $_SERVER['PHP_SELF'];
		$this->_serror->msg = $msg;
		$this->_serror->date = system::date("d/m/Y H:i:s");
		$this->_serror->user = $_SERVER['REMOTE_ADDR'];
		
		// Si st est un objet, on matte la méthode getError()
		if (is_object($st)) {
			$ret = $st->getError();
			$this->_serror->msg = $ret['code'].' : '.$ret['message'];
			$this->_serror->msgext = $ret['query'];
		}
		else
			$this->_serror->msg = $st;
		
		$str_error = null;
		$str_error .= '<error date="'.$this->_serror->date.'" user="'.$this->_serror->user.'" file="'.$this->_serror->file.'" line="'.$this->_serror->line.'">';
		$str_error .= "\n\t".'<page>'.$this->_serror->page.'</page>';
		$str_error .= "\n\t".'<msg>'.$this->_serror->msg.'</msg>';
		if ($this->_serror->msgext)
			$str_error .= "\n\t".'<msgext>'.$this->_serror->msgext.'</msgext>';
		$str_error .= "\n</error>\n";
		
		if (ERROR_REPORT == 2)
			exit('<pre>'.htmlspecialchars($str_error).'</pre>');
		else if (ERROR_REPORT > 0)
			print('<pre>'.htmlspecialchars($str_error).'</pre>');

/*
		if (ERROR_FILE_LOG) {
			$fp = fopen(ERROR_FILE_LOG, 'ab');
			set_file_buffer($fp, 0);
			fputs($fp, $str_error);
			fclose($fp);
		}
*/
		/*
		// Affichage erreur
		if (ERROR_REPORT == 5)
			print("<!-- Start Query --><font size=\"2\" color=\"#FF9999\"><font color=\"#00CC00\">&laquo;</font> $st <font color=\"#00CC00\">&raquo;</font></font><br /><!-- End Query -->");
		else if (ERROR_REPORT > 0)
		{
			$msg = '<!-- Start Debug --><span style="color: #000">Erreur ligne : <b>'.$this->_serror->line.'</b>, dans le fichier : <b>'.$this->_serror->file.'</b> ( '.$this->_serror->page.' ) <b>'.$this->_serror->msg.'</b>';
			if (ERROR_REPORT > 2 && $ret != null)
				$msg .= '<br />&laquo; <b>'.$this->_serror->msgext.'</b> &raquo;';
			$msg .= '</span><br /><!-- End Debug -->';
			print($msg);
	
			// On quitte le script si on le veut bien...
			if (ERROR_REPORT == 2 || ERROR_REPORT == 4)
				exit;
		}
		*/
	/*
		// On écrit dans le fichier de logs
		if (ERROR_FILE_LOG != null)
		{
			$fp = fopen(ERROR_FILE_LOG, 'ab');
			set_file_buffer($fp, 0);
			fputs($fp, $str_error);	//system::date("d/m/Y H:i:s")."\t\t".$_SERVER['REMOTE_ADDR']."\t".PSEUDO_SESSION."\t".$file."\t".$line."\t ( ".$_SERVER['PHP_SELF']." )\n".$msg."\n".$st."\n\n");
			fclose($fp);
		}
		*/
	}
	
	function dump()
	{
		$_str_msg = null;
		//$this->loadTagFromTpl(true);
		
		echo $this->parseTpl('error_msg', array(
				'LINE'	=>	__LINE__,
				'FILE'	=>	__FILE__,
				'PAGE'	=>	$_SERVER['PHP_SELF'],
				'MSG'	=>	'Super Error !',
				));
		/*
		$_str_msg = str_replace('{LINE}', __LINE__, $this->_tag['error_msg']);
		$_str_msg = str_replace('{FILE}', __FILE__, $_str_msg);
		$_str_msg = str_replace('{PAGE}', $_SERVER['PHP_SELF'], $_str_msg);
		$_str_msg = str_replace('{MSG}', '', $_str_msg);
		
		echo $_str_msg;
		*/
		//exit($_str_msg);
	}
}


?>
