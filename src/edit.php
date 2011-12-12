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
	WITHOUT ANY WARRANTY; without even the implied warcranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Hyla; if not, write to the Free Software
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

if (!defined('PAGE_HOME'))
	header('location: ../index.php');

$tpl->set_file('edit', 'edit.tpl');

$tpl->set_block('edit', array(
		'plugin'						=>	'Hdlplugin',
		'edit_plugins'					=>	'Hdledit_plugins',
		'edit_description'				=>	'Hdledit_description',

		'icon_default_checked'			=>	'Hdlicon_default_checked',
		'icon_checked'					=>	'Hdlicon_checked',
		'icon'							=>	'Hdlicon',
		'edit_icons'					=>	'Hdledit_icons',

		'edit_right_selected'			=>	'Hdledit_right_selected',
		'edit_right'					=>	'Hdledit_right',

		'edit_right_selected_view'		=>	'Hdledit_right_selected_view',
		'edit_right_disabled_multiple'	=>	'Hdledit_right_disabled_multiple',

		'edit_rights_user'				=>	'Hdledit_rights_user',
		'edit_rights_edit'				=>	'Hdledit_rights_edit',
		'add_right'						=>	'Hdladd_right',
		'add_user'						=>	'Hdladd_user',
		'edit_rights_add'				=>	'Hdledit_rights_add',
		'edit_rights_list_line'			=>	'Hdledit_rights_list_line',
		'edit_rights_list'				=>	'Hdledit_rights_list',

		'edit_rights_no_right_parent_path'	=>	'Hdledit_rights_no_right_parent_path',
		'edit_rights_no_right'			=>	'Hdledit_rights_no_right',
		'edit_rights_error'				=>	'Hdledit_rights_error',
		'edit_rights'					=>	'Hdledit_rights',
		));

/*	Édition de la description
 */
if (acl::ok(AC_EDIT_DESCRIPTION)) {
	$tpl->set_var(array(
			'FORM_EDIT_SETDESCRIPTION'	=>	url::getCurrentObj('edit', 'setdescription'),
			'DESCRIPTION'				=>	isset($cobj->info->description) ? string::unFormat($cobj->info->description) : null,
			));
	$tpl->parse('Hdledit_description', 'edit_description', true);
}

/*	Les dossiers
 */
if ($cobj->type == TYPE_DIR) {

	/*	Édition de l'icone
	 */
	if (acl::ok(AC_EDIT_ICON) && $cobj->file != '/') {

		// L'icone par défaut
		if (empty($cobj->icon) || substr($cobj->icon, 0, strlen(DIR_IMG_PERSO)) != DIR_IMG_PERSO) {
			$tpl->parse('Hdlicon_default_checked', 'icon_default_checked', true);
		}

		$hdl = dir(DIR_IMG_PERSO);
		if ($hdl) {
			while (false !== ($occ = $hdl->read())) {
				if ($occ{0} == '.')
					continue;

				$tpl->set_var('Hdlicon_checked');

				if (DIR_IMG_PERSO.$occ == $cobj->icon) {
					$tpl->parse('Hdlicon_checked', 'icon_checked', true);
				}

				$tpl->set_var(array(
						'ICON_NAME'			=>	$occ,
						));
				$tpl->parse('Hdlicon', 'icon', true);
			}
		}

		$tpl->set_var('FORM_EDIT_SETIMAGE', url::getCurrentObj('edit', 'seticon'));
		$tpl->parse('Hdledit_icons', 'edit_icons', true);
	}

	/*	Édition des plugins
	 */
	if (acl::ok(AC_EDIT_PLUGIN)) {
		$tab = plugins::getDirPlugins();

		$tpl->set_var(array(
				'PLUGIN_DEFAULT_CHECKED'	=>	(!$cobj->info->plugin) ? 'value="default" checked="checked"' : 'value="default"',
				'FORM_EDIT_SETPLUGIN'		=>	url::getCurrentObj('edit', 'setplugin'),
				'DIR_DEFAULT_PLUGIN'		=>	$conf['dir_default_plugin'],
				));

		foreach($tab as $occ) {
			$name = strtolower($occ['name']);
			$tpl->set_var('PLUGIN_CHECKED', ($cobj->info->plugin == $name) ? 'value="'.$name.'" checked="checked"' : 'value="'.$name.'"');		
			$tpl->set_var(array(
					'PLUGIN_NAME'			=>	$occ['name'],
					'PLUGIN_DESCRIPTION'	=>	$occ['description'],
					));
			$tpl->parse('Hdlplugin', 'plugin', true);
		}
		$tpl->parse('Hdledit_plugins', 'edit_plugins', true);
	}

	/*	Édition des utilisateurs
	 */
	if (acl::ok(ADMINISTRATOR_ONLY)) {
		$usr = new users();

		$t_error = $obj->findError();
		if ($t_error)
			$tpl->parse('Hdledit_rights_error', 'edit_rights_error', true);

		switch (url::getQueryAff(3)) {

			case 'add':

				$tab = $usr->getUsers(true);

				$tab_exist = $obj->getObjRights($cobj->file);

				$size = sizeof($tab);
				for ($i = 0; $i < $size; $i++) {
					if (!array_key_exists($tab[$i]->name, $tab_exist)) {
						$tpl->set_var(array(
								'USER_ID'	=>	$tab[$i]->id,
								'USER_NAME'	=>	$tab[$i]->type == USR_TYPE_GRP ? '['.$tab[$i]->name.']' : $tab[$i]->name,
								));
						$tpl->parse('Hdladd_user', 'add_user', true);
					}
				}
				unset($tab);


				foreach($obj->acl_rights as $val => $name) {
					if (constant($val) & AC_VIEW)
						continue;
					$tpl->set_var(array(
							'RIGHT_VALUE'	=>	constant($val),
							'RIGHT_NAME'	=>	$name,
							));
					$tpl->parse('Hdladd_right', 'add_right', true);
				}

				$tpl->parse('Hdledit_rights_add', 'edit_rights_add', true);
				break;

			case 'edit':

				if (isset($_POST['rgt_user']))
					$user = $_POST['rgt_user'];
				else if (url::getQueryAff(4))
					$user = url::getQueryAff(4);


				// Édition de l'utilisateur courant
				if (isset($user) || isset($_POST['rgt_user'])) {

					$uinfo = $usr->getUser($user);

					if ($uinfo) {
						$right = $obj->getRightsFromUserAndPath($uinfo->id, $cobj->file);

						foreach($obj->acl_rights as $val => $name) {
							if (constant($val) & AC_VIEW)
								continue;

							$tpl->set_var('Hdledit_right_selected');
							if ($right & constant($val))
								$tpl->parse('Hdledit_right_selected', 'edit_right_selected', true);
							$tpl->set_var(array(
									'RIGHT_VALUE'	=>	constant($val),
									'RIGHT_NAME'	=>	$name,
									));
							$tpl->parse('Hdledit_right', 'edit_right', true);
						}

						// Le droit de Visualisation est-il présent ?
						if ($right & AC_VIEW)
							$tpl->parse('Hdledit_right_selected_view', 'edit_right_selected_view', true);
						else
							$tpl->parse('Hdledit_right_disabled_multiple', 'edit_right_disabled_multiple', true);

						$tpl->set_var(array(
								'USER_NAME'	=>	$uinfo->name,
								'USER_ID'	=>	$uinfo->id,
								));
						$tpl->parse('Hdledit_rights_user', 'edit_rights_user', true);
					}
				}

				$tpl->parse('Hdledit_rights_edit', 'edit_rights_edit', true);
				break;

			default:

				$tab = $obj->getObjRights($cobj->file);
				if ($tab) {
					foreach ($tab as $key => $val) {

						$tpl->set_var(array(
								'USER_ID'			=>	$val['id'],
								'USER_NAME'			=>	$val['type'] == USR_TYPE_GRP ? '['.$key.']' : $key,
								'RIGHTS'			=>	$obj->getTextRights($val['perm']),
								'URL_EDIT_RIGHTS'	=>	url::getCurrentObj(array('edit', 'rights', 'edit', $key)),
								'URL_DEL_RIGHTS'	=>	url::getCurrentObj(array('edit', 'rights', 'del', $key)),
								));
						$tpl->parse('Hdledit_rights_list_line', 'edit_rights_list_line', true);
						}
					$tpl->parse('Hdledit_rights_list', 'edit_rights_list', true);
					unset($tab);
				} else {
					// Récupération du premier dossier parent ayant des droits
					$last_obj = $obj->getParentHaveRights($cobj->file);
					if ($last_obj) {
						$tpl->set_var(array(
								'URL_EDIT_RIGHTS_LAST'	=>	url::getObj($last_obj, array('edit', 'rights')),
								'LAST_OBJECT'			=>	$last_obj,
								));
						$tpl->parse('Hdledit_rights_no_right_parent_path', 'edit_rights_no_right_parent_path', true);
					} else
						$tpl->set_var('MSG_NO_RIGHT', __('No right was found in all the filesystem !'));

					$tpl->parse('Hdledit_rights_no_right', 'edit_rights_no_right', true);
				}

				break;
		}

		unset($usr);

		$tpl->set_var(array(
				'URL_RIGHTS'		=>	url::getCurrentObj(array('edit', 'rights')),
				'URL_ADD_RIGHTS'	=>	url::getCurrentObj(array('edit', 'rights', 'add')),
				'URL_ADMIN_RIGHTS'	=>	url::getPage(array('admin', 'rights')),
				'FORM_EDIT_RIGHTS'	=>	url::getCurrentObj(array('edit', 'rights', 'edit'), 'editrights'),
				'FORM_DEL_RIGHTS'	=>	url::getCurrentObj(array('edit', 'rights'), 'delrights'),
				'FORM_ADD_RIGHTS'	=>	url::getCurrentObj(array('edit', 'rights'), 'addrights'),
				));

		$tpl->parse('Hdledit_rights', 'edit_rights', true);
	}
}

$var_tpl .= $tpl->parse('OutPut', 'edit');

?>
