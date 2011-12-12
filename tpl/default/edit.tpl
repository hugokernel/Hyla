
<p>
	<a href="doc/{L10N}/edition.htm" onclick="popup(this.href); return false;" title="Aide contextuelle"><img src="{DIR_IMAGE}/help.png" alt="Point d'interrogation" /> </a>
</p>

<!-- BEGIN edit_description -->
<form method="post" name="desc_form" action="{FORM_EDIT_SETDESCRIPTION}">
<fieldset>
	<legend><a href="#description" onclick="swap_layer('description');"><img src="{DIR_IMAGE}/edit.png" align="middle" width="32" height="32" alt="Édition" /> Description</a> </legend>

	<div id="description" class="jhidden">

		<p>
			<textarea name="description" cols="50" rows="7">{DESCRIPTION}</textarea>
		</p>

		<p>
			<label for="desc_redirect">
				Être redirigé vers l'objet :
			</label>
			<input type="checkbox" name="redirect" id="desc_redirect" value="1" />
		</p>
	
		<input type="submit" name="Submit" value="Modifier" />

	</div>
</fieldset>
</form>
<!-- END edit_description -->

<!-- BEGIN edit_icons -->
<form method="post" name="pg_form" action="{FORM_EDIT_SETIMAGE}">
<fieldset>
	<legend><a href="#icons" onclick="swap_layer('icons');"><img src="{DIR_IMAGE}/image.png" align="middle" width="32" height="32" alt="Edition icone" /> Icones</a> </legend>

	<div id="icons" class="jhidden">
		<p>
			<label for="img_name_default" style="margin-right: 20px">
				Icone par défaut : <input name="icon_name" type="radio" id="img_name_default" value="default" <!-- BEGIN icon_default_checked --> checked="checked"<!-- END icon_default_checked --> />
			</label>
			<!-- BEGIN icon -->
			<label for="img_name_{ICON_NAME}" style="margin-right: 20px">
				<img src="img/perso/{ICON_NAME}" align="middle" class="icon" alt="Image" /><input name="icon_name" type="radio" id="img_name_{ICON_NAME}" value="{ICON_NAME}" <!-- BEGIN icon_checked --> checked="checked"<!-- END icon_checked --> />
			</label>
			<!-- END icon -->
		</p>
		<p>
			<label for="ic_redirect">
				Être redirigé vers l'objet :
			</label>
			<input type="checkbox" name="redirect" id="ic_redirect" value="1" />
		</p>

		<input type="submit" name="Submit" value="Modifier" />
		<p class="info">
			Les images affichés ici sont celles contenues dans le dossier img/perso.
		</p>
	</div>

</fieldset>
</form>
<!-- END edit_icons -->

<!-- BEGIN edit_plugins -->
<form method="post" name="pg_form" action="{FORM_EDIT_SETPLUGIN}">
<fieldset>
	<legend><a href="#plugins" onclick="swap_layer('plugins');"><img src="{DIR_IMAGE}/config-gen.png" align="middle" width="32" height="32" alt="Edition plugin" /> Plugins</a> </legend>

	<div id="plugins" class="jhidden">
		<p>
			<input name="pg_name" {PLUGIN_DEFAULT_CHECKED} type="radio" id="pg_name_default" />
			<label for="pg_name_default">
				<strong>Default</strong>
				Utilise le plugin par défaut pour les dossiers ( <em>{DIR_DEFAULT_PLUGIN}</em> )
			</label>
		</p>
		<hr />
		<!-- BEGIN plugin -->
		<p>
			<input name="pg_name" {PLUGIN_CHECKED} type="radio" id="pg_name_{PLUGIN_NAME}" />
			<label for="pg_name_{PLUGIN_NAME}">
				<strong>{PLUGIN_NAME}</strong>
				{PLUGIN_DESCRIPTION}
			</label>
		</p>
		<!-- END plugin -->

		<p>
			<label for="pg_redirect">
				Être redirigé vers l'objet :
			</label>
			<input type="checkbox" name="redirect" id="pg_redirect" value="1" />
		</p>

		<input type="submit" name="Submit" value="Modifier" />
	</div>

</fieldset>
</form>
<!-- END edit_plugins -->

<!-- BEGIN edit_rights -->
<fieldset>
	<legend><a href="#rights" onclick="swap_layer('rights');"><img src="{DIR_IMAGE}/rights.png" align="middle" width="32" height="32" alt="Les droits" /> Droits</a> </legend>

	<div id="rights" class="jhidden">

		<p>
			<a href="doc/{L10N}/rights.htm" onclick="popup(this.href); return false;"><img src="{DIR_IMAGE}/help.png" alt="Point d'interrogation" /></a>
		</p>

		<a href="{URL_RIGHTS}#rights">Liste des utilisateurs</a> - 
		<a href="{URL_ADD_RIGHTS}#rights">Ajouter un ou des utilisateurs</a>

		{STATUS}

		<!-- BEGIN edit_rights_error -->
		<p class="error">
			Des erreurs ont été trouvées dans les droits, des utilisateurs ne pourront visualiser des dossiers, pour résoudre le problème,
			Hyla peut résoudre le problème, pour cela, veuillez aller dans <a href="{URL_ADMIN_RIGHTS}">la gestion des droits dans l'administration</a>.
		</p>
		<!-- END edit_rights_error -->

		<!-- BEGIN edit_rights_add -->
		<form method="post" name="pg_form_add" action="{FORM_ADD_RIGHTS}#rights">
			<fieldset>
				<legend>Ajout d'un droit dans le dossier courant</legend>

				<p>
					Choisir un ou plusieurs utilisateurs :
				</p>

				<p class="info">
					Les groupes apparaissent entre crochets ( [groupe] ).
					<br />
					Sélectionnez plusieurs utilisateurs / groupes en maintenant la touche [CTRL] appuyé, vous pouvez aussi cliquer sur un élément
					et glisser vers le haut ou le bas.
				</p>

				<select name="rgt_users[]" multiple="multiple">
					<!-- BEGIN add_user -->
					<option value="{USER_ID}">{USER_NAME}</option>
					<!-- END add_user -->
				</select>


				<p>
					Lui attribuer des droits :
				</p>
				<p class="info">
					Si le droit de "Visualisation" ou de "Listage" est décoché, il n'est pas possible d'accéder au contenu du dossier donc impossible d'accéder aux actions (édition, copie...).
				</p>
				<p>
					<input type="checkbox" name="rgt_value[]" value="1" id="rgt_value_view" checked="checked" onclick="test('rgt_value_view', 'rgt_value_multiple');" />
					<label for="rgt_value_view">
						Visualisation <span class="help">(si ce droit n'est pas présent, le dossier et son contenu seront cachés)</span>
					</label>
				</p>

				<select name="rgt_value[]" id="rgt_value_multiple" size="11" multiple="multiple">
					<!-- BEGIN add_right -->
					<option value="{RIGHT_VALUE}">{RIGHT_NAME}</option>
					<!-- END add_right -->
				</select>

				<p>
					<input type="submit" name="Submit" value="Ajouter" />
				</p>
			</fieldset>
		</form>
		<!-- END edit_rights_add -->

   		<div style="clear: both;"></div>

		<!-- BEGIN edit_rights_edit -->
		<fieldset>
			<legend>Édition</legend>

			<!-- BEGIN edit_rights_user -->
			<p>
				Édition des droits de l'utilisateur &laquo; <b>{USER_NAME}</b> &raquo; pour le dossier courant :
			</p>

			<form method="post" name="pg_form_edit" action="{FORM_EDIT_RIGHTS}#rights">

				<p class="info">
					Si le droit de "Visualisation" est décoché, il n'est pas possible d'accéder au contenu du dossier donc impossible d'accéder aux actions (édition, copie...).
				</p>
				<p>
					<input type="checkbox" name="rgt_value[]" value="1" id="rgt_value_view" <!-- BEGIN edit_right_selected_view --> checked="checked"<!-- END edit_right_selected_view --> onclick="test('rgt_value_view', 'rgt_value_multiple');" />
					<label for="rgt_value_view">
						Visualisation <span class="help">(si ce droit n'est pas présent, le dossier et son contenu seront cachés)</span>
					</label>
				</p>

				<select name="rgt_value[]" size="11" multiple="multiple" id="rgt_value_multiple" <!-- BEGIN edit_right_disabled_multiple -->disabled="disabled"<!-- END edit_right_disabled_multiple -->>
					<!-- BEGIN edit_right -->
					<option value="{RIGHT_VALUE}" <!-- BEGIN edit_right_selected --> selected="selected"<!-- END edit_right_selected -->>{RIGHT_NAME}</option>
					<!-- END edit_right -->
				</select>

				<p>
					<input type="hidden" name="rgt_user" value="{USER_ID}" />
					<input type="submit" name="Submit" value="Modifier" />
				</p>
			</form>

			<!-- END edit_rights_user -->

		</fieldset>
		<!-- END edit_rights_edit -->

		<!-- BEGIN edit_rights_list -->
		<p>
			Liste des utilisateurs ayant des droits dans ce dossier :
		</p>

			<form method="post" name="pg_form_edit" action="{FORM_DEL_RIGHTS}#rights">
				<table class="tab" summary="Liste des droits pour le dossier courant">
					<tr>
						<th>Utilisateurs / Groupes</th>
						<th>Droits</th>
						<th>Éditer</th>
						<th>Sélection</th>
					</tr>
					<!-- BEGIN edit_rights_list_line -->
					<tr class="line">
						<td width="20%">{USER_NAME}</td>
						<td style="font-size: 80%;">{RIGHTS}</td>
						<td width="1%" align="center"><a href="{URL_EDIT_RIGHTS}#rights">Éditer</a></td>
						<td width="1%" align="center"><input type="checkbox" name="rgt_user[]" id="rgt_delete" value="{USER_ID}" /></td>
					</tr>
					<!-- END edit_rights_list_line -->
				</table>

				<p>
					<input type="submit" name="Submit" onclick="return confirm('Voulez-vous vraiment supprimer les droits ?');" value="Supprimer la sélection" />
				</p>

				<p class="info">
					Les groupes sont affichés entre crochets.
				</p>
			</form>
		<!-- END edit_rights_list -->


		<!-- BEGIN edit_rights_no_right -->
			<p class="status">
				{MSG_NO_RIGHT}
				<!-- BEGIN edit_rights_no_right_parent_path -->
					Aucun droit dans ce dossier, les droits héritent donc du dernier dossier parent en comportant, il s'agit de &laquo; <a href="{URL_EDIT_RIGHTS_LAST}#rights">{LAST_OBJECT}</a> &raquo;
				<!-- END edit_rights_no_right_parent_path -->
			</p>
		<!-- END edit_rights_no_right -->
	</div>

</fieldset>
<!-- END edit_rights -->

