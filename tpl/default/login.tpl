<form method="post" name="form_upload" action="?aff=login,{OBJECT}">
<fieldset>
	<legend><img src="{FOLDER_TEMPLATE}/img/Lock-Screen.png" align="middle" width="32" height="32" alt="Login" /> Authentification</legend>	{ERROR}
	<p>
		<label for="lg_name">Nom :</label>
		<input name="lg_name" id="lg_name" size="15" maxlength="32" value="{NAME}" type="text" />
	</p>

	<p>
		<label for="lg_password">Mot de passe :</label>
		<input name="lg_password" id="lg_password" size="15" maxlength="32" value="{NAME}" type="password" />
	</p>

	<input type="submit" name="Submit" value="Envoyer" />
</fieldset>
</form>
