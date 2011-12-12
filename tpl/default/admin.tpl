<fieldset>
	<legend><img src="{FOLDER_TEMPLATE}/img/GNOME-Laptop.png" align="middle" width="32" height="32" alt="Login" /> Administration</legend>
	
	<h3>Liste des utilisateurs :</h3>
	(root n'apparait pas)

	<form method="post" name="form_admin" action="?aff=admin&amp;action=edituser">
	<div class="table">
		<table width="100%">
			<tr>
				<th>Nom</th>
				<th>Ajout de commentaires</th>
				<th>Edition</th>
				<th>Ajout</th>
				<th>Suppression</th>
				<th>Action</th>
			</tr>
		<!-- BEGIN user -->
			<tr>
				<td>{USER_NAME}</td>
				<td align="center"><input name="{USER_NAME}:1" type="checkbox" <!-- BEGIN chk-add-comment -->checked="checked"<!-- END chk-add-comment --> /></td>
				<td align="center"><input name="{USER_NAME}:2" type="checkbox" <!-- BEGIN chk-edit-file -->checked="checked"<!-- END chk-edit-file --> /></td>
				<td align="center"><input name="{USER_NAME}:4" type="checkbox" <!-- BEGIN chk-add-file -->checked="checked"<!-- END chk-add-file --> /></td>
				<td align="center"><input name="{USER_NAME}:8" type="checkbox" <!-- BEGIN chk-del-file -->checked="checked"<!-- END chk-del-file --> /></td>
				<td align="center"><a href="?aff=admin&amp;action=deluser&amp;name={USER_NAME}">Supprimer</a></td>
			</tr>
		<!-- END user -->
		</table>
		<br />
		<input type="submit" name="Submit" value="Modifier" />
	</div>
	</form>


	<h3>Ajout d'un utilisateur :</h3>
	<div class="error">{ERROR}</div>
	<div class="table">
	<form method="post" name="form_admin" action="?aff=admin&amp;action=adduser">
		<!-- 	OUI,
				Je sais, un tableau, ça sert pas à ça ...
				... mais là, pas le temps
		-->
		<table>
			<tr>
				<td>Nom :</td>
				<td><input type="text" name="name" size="10" value="{NAME}" /></td>
			</tr>
			<tr>
				<td>Mot de passe :</td>
				<td><input type="password" name="password" size="10" value="" /></td>
			</tr>
			<tr>
				<td>Ajout de commentaire :</td>
				<td><input name="add_comment" type="checkbox" /></td>
			</tr>
			<tr>
				<td>Edition :</td>
				<td><input name="edit_file" type="checkbox" /></td>
			</tr>
			<tr>
				<td>Ajout :</td>
				<td><input name="add_file" type="checkbox" /></td>
			</tr>
			<tr>
				<td>Suppression :</td>
				<td><input name="del_file" type="checkbox" /></td>
			</tr>
		</table>
		<br />
		<input type="submit" name="Submit" value="Ajouter l'utilisateur" />
		<br />
		<br />
	</form>
	</div>
	
	<h3>Re-synchroniser le fichier XML</h3>
	Si il vous arrive de supprimer des fichiers / répertoires autrement que par ce programme,
	le fichier xml contenant des infos sur vos fichiers risque de ne plus être à jour.<br />
	En lançant une re-synchronisation, le fichier xml va être lu et toute les entrées de ce dernier n'existants
	plus dans le système de fichiers seront supprimés.
	<br />
	<a href="?aff=admin&amp;action=synch#synch">Lancer une Re-Synchronisation</a>
	<br />
	{RAPPORT}

</fieldset>
