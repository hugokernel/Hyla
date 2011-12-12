
<div id="main">
	<form method="post" name="form_upload" action="{PAGE_LOGIN}">
	<fieldset>
		<legend><img src="{DIR_TEMPLATE}/img/Lock-Screen.png" align="middle" width="32" height="32" alt="Login" /> Authentification</legend>		{ERROR}
		<p>
			<label for="lg_name">Nom :</label>
			<input name="lg_name" id="lg_name" size="15" maxlength="32" value="{NAME}" type="text" />
		</p>

		<p>
			<label for="lg_password">Mot de passe :</label>
			<input name="lg_password" id="lg_password" size="15" maxlength="32" value="" type="password" />
		</p>

		<input type="submit" name="Submit" value="Envoyer" />
	</fieldset>
	</form>
</div>
