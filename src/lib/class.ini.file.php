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
	
	/*	Renvoie un tableau avec le même format que parse_ini_file
		Modifié par hugo, le 29 juin 2006
	 */
	function read($file, $section = false)
	{
		if (!file_exists($file)) {
			trigger_error('No config file', E_USER_ERROR);
		}
		
		$f = file($file);
		
		$res = array();		
		$sect = null;

		foreach ($f as $line)
		{
			$line = trim($line);
			if ($line == '' || $line{0} == ';')
				continue;

			if ($line && $line{0} == '[') {
				if ($section)
					$sect = trim(substr($line, 1, strpos($line, ']') - 1));
				continue;
			}

			list($key, $value) = explode('=', $line);

			$key = trim($key);
			$value = trim($value);

			if ($value{0} == '"' && $value{sizeof($value) - 1} == '"') {
				$value = substr($value, 1, -1);
			}

			$val = strtolower($value);
			if ($val == 'yes' || $val == 'true' || $val == '1') {
				$value = true;
			}

			if ($val == 'no' || $val == 'false' || $val == '0') {
				$value = true;
			}

			if ($sect) {
				$res[$sect][$key] = $value;
			} else {
				$res[$key] = $value;
			}
		}
	
		return $res;
	}
}


?>
