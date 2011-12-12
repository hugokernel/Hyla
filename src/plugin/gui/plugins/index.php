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

class plugin_gui_plugins extends plugin_gui {

	function plugin_gui_plugins() {
		parent::plugin_gui();
	}

	function _getPlugins($dir_type_plugin) {

		$tab = array();

		$hdl = dir($dir_type_plugin);
		if ($hdl) {
			while (false !== ($occ = $hdl->read())) {

				// Si on a un fichier caché...
				if ($occ{0} == '.')
					continue;

				// Si ce n'est pas un répertoire
				if (!is_dir($dir_type_plugin.'/'.$occ))
					continue;

				$xfile = $dir_type_plugin.$occ.'/info.xml';
				if (file_exists($xfile)) {
					$xml =& new XPath($xfile);
					//$exp = $type ? '/plugin[contains(@target,"dir")]/*' : '/plugin[contains(@target,"file") ]/*';
					$exp = '/plugin/*';
					$res = $xml->match($exp);
					if ($res) {
						$tab[] = array(
							'dir'           =>  $xfile,
							'name'          =>  $xml->getData('/plugin/name'),
							'description'   =>  $xml->getData('/plugin/description'),
							'author'        =>  $xml->getData('/plugin/author'),
							'version'       =>  $xml->getData('/plugin/version'),
							'target'        =>  $xml->getData('/plugin/@target'),
							'enabled'       =>  $xml->getData('/plugin/@enabled'),

						);
					}
				}
			}
		}

		return $tab;
	}

	function act() {
		// prevoir les actions : Enable et Disable 
	}

	function aff() {

		// Declare tpl
		$this->tpl->set_file('plugins', 'tpl/plugins.tpl');
		$this->tpl->set_block('plugins', array(
			'type_plugin_current'      =>  'Hdltype_plugin_current',
			'type_plugin'              =>  'Hdltype_plugin',
			'line_content'             =>  'Hdlline_content',
		));

		$tab_type_plugins= array (PLUGIN_TYPE_AUTH,
			PLUGIN_TYPE_URL,
			PLUGIN_TYPE_OBJ,
			PLUGIN_TYPE_DB,
			PLUGIN_TYPE_WS,
			PLUGIN_TYPE_GUI
		);


		// Generate Tools Bar.
		$current_type_plugin = $this->url->getParam('aff', 3);


		switch($current_type_plugin)
		{
		case 'obj':   $current_type_plugin = PLUGIN_TYPE_OBJ;    break; 
		case 'auth':  $current_type_plugin = PLUGIN_TYPE_AUTH;   break; 
		case 'url':   $current_type_plugin = PLUGIN_TYPE_URL;    break; 
		case 'db':    $current_type_plugin = PLUGIN_TYPE_DB;     break; 
		case 'act':   $current_type_plugin = PLUGIN_TYPE_WS;    break; 
		case 'gui':   $current_type_plugin = PLUGIN_TYPE_GUI;    break;   
		default:      $current_type_plugin = PLUGIN_TYPE_OBJ;  break;
		}


		// Current plugin ?
		foreach ($tab_type_plugins as $typename) {
			$this->tpl->set_var('Hdltype_plugin_current');
			if ($current_type_plugin == $typename) {
				$this->tpl->parse('Hdltype_plugin_current', 'type_plugin_current', true);
			}
			$name = plugins::getNameFromType($typename);   
			$this->tpl->set_var(  array(
				'URL_PLUGIN'            =>  $this->url->linkToPage(array('admin', 'plugins', $name)),
				'PLUGIN_NAME'           =>  'Plugin '.$name,
				'LEGEND_URL'            =>  HYLA_ROOT_URL.DIR_PLUGINS_GUI.'plugins'.'/img/',
			));
			$this->tpl->parse('Hdltype_plugin', 'type_plugin', true);
		}


		// liste les  plugins 
		$dir = plugins::getDirFromType($current_type_plugin);
		$tab_list_plugin = plugin_gui_plugins::_getPlugins($dir);
		$size_tab = sizeof($tab_list_plugin);
		$this->tpl->set_var(  array(
			'PLUGINS_TYPE'            =>  strtoupper($current_type_plugin),
		));
		$this->tpl->set_var('Hdlline_content'); 


		for ($i = 0; $i < $size_tab; $i++) {

			// Icon Disable/Enable
			$icon_status = HYLA_ROOT_URL.DIR_PLUGINS_GUI.'plugins'.'/img/';   

			switch (strtoupper($tab_list_plugin[$i]['enabled']))
			{
			case 'TRUE':   
				$icon_status .='enable.png';    
				break; 
			case 'FALSE':  
				$icon_status .='disable.png';   
				break;

			default :   $icon_status .='nodisable.png';   
			}

			// Icon Plugin
			$icon_plugin = $dir.strtolower($tab_list_plugin[$i]['name']);

			if (file_exists(DIR_ROOT.$icon_plugin.'/icon.png')) {
				$icon_plugin = HYLA_ROOT_URL.$icon_plugin.'/icon.png';
			} else {
				$icon_plugin = HYLA_ROOT_URL.DIR_PLUGINS_GUI.'plugins'.'/img/plugins-system.png';
			}


			$this->tpl->set_var(array(
				'PLUGINS_NAME'               => $tab_list_plugin[$i]['name'],
				'PLUGINS_DESCRIPTION'        => $tab_list_plugin[$i]['description'],
				'PLUGINS_AUTHOR'             => $tab_list_plugin[$i]['author'],
				'PLUGINS_VERSION'            => $tab_list_plugin[$i]['version'],
				'PLUGINS_TARGET'             => $tab_list_plugin[$i]['target'],
				'PLUGINS_ENABLED'            => $tab_list_plugin[$i]['enabled'],
				'PLUGINS_ICON'               => $icon_plugin,
				'PLUGINS_ICON_STATUS'        => $icon_status,

			));

			$this->tpl->parse('Hdlline_content', 'line_content', true);                           


		}
		return $this->tpl->parse('OutPut', 'plugins');
	}
}

?>
