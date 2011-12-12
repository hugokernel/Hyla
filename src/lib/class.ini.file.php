<?php
# ***** BEGIN LICENSE BLOCK *****
# This file is part of DotClear.
# Copyright (c) 2004 Olivier Meunier and contributors. All rights
# reserved.
#
# DotClear is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
# 
# DotClear is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with DotClear; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
# ***** END LICENSE BLOCK *****

class iniFile
{
	var $content;
	var $var_reg = '/^[\s]*(%s)[\s]*?=[\s*](.*)$/m';
	
	function iniFile($file)
	{
		if (file_exists($file)) {
			$this->file = $file;
			$this->content = implode('',file($file));
		} else {
			$this->file = false;
		}
	}
	
	/* édition d'une variable */
	function editVar($name,$value)
	{
		if ($this->file !== false)
		{
			$match = sprintf($this->var_reg,preg_quote($name));
			
			if (preg_match($match,$this->content))
			{
				$replace = '$1 = '.$value;
				$this->content = preg_replace($match,$replace,$this->content);
			}
			else
			{
				$this->createVar($name,$value);
			}
		}
	}
	
	/* création d'un variable */
	function createVar($name,$value,$comment='')
	{
		$match = sprintf($this->var_reg,preg_quote($name));
		
		if ($comment != '') {
			$comment = '; '.str_replace("\n","\n; ",$comment)."\n";
		}
		
		if (!preg_match($match,$this->content))	{
			$this->content .= "\n\n".$comment.$name.' = '.$value;
		}
	}
	
	/* sauvegarde du fichier */
	function saveFile()
	{
		if (($fp = @fopen($this->file,'w')) !== false) {
			if (@fwrite($fp,$this->content,strlen($this->content)) !== false) {
				$res = true;
			} else {
				$res = false;
			}
			fclose($fp);
			return $res;
		} else {
			return false;
		}
	}
	
	/*
	Static method that puts ini vars in constants or returns array
	*/
	function read($file,$return=false)
	{
		if (!file_exists($file)) {
			trigger_error('No config file',E_USER_ERROR);
			exit;
		}
		
		$f = file($file);
		
		if ($return) {
			$res = array();
		}
		
		foreach ($f as $v)
		{
			$v = trim ($v);
			if (substr($v,0,1) != ';' && $v != '') {
				$p = strpos($v,'=');
				$K = (string) trim(substr($v,0,$p));
				$V = (string) trim(substr($v,($p+1)));
				
				if (substr($V,0,1) == '"' && substr($V,-1) == '"') {
					$V = substr(substr($V,1),0,-1);
				}
				
				if ($V === 'yes' || $V === 'true' || $V === '1') {
					$V = true;
				}
				
				if ($V === 'no' || $V === 'false' || $V === '0') {
					$V = false;
				}
				
				if ($return) {
					$res[$K] = $V;
				} elseif (!defined($K)) {
					define($K,$V);
				}
			}
		}
		
		if ($return) {
			return $res;
		}
	}
}


?>
