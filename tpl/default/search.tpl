
<fieldset>
	<legend><img src="{DIR_TEMPLATE}/img/Find-Files1.png" align="middle" width="32" height="32" alt="Disque dûr" /> Recherche dans &laquo; {OBJECT} &raquo;  </legend>

	<form method="post" name="form_upload" action="{FORM_SEARCH}">
		<p>
			<label for="sc_id_content">Saisissez le terme recherché :</label> 
			<input type="text" name="word" size="40" value="{WORD}" id="sc_id_content" />
		</p>
		<p>
			<input type="checkbox" name="scandir" {SCANDIR_CHECKED} id="sc_id_scandir" />
			<label for="sc_id_scandir">Chercher dans le nom des dossiers</label>
		</p>
		<p>
			<input type="checkbox" name="recurs" {RECURS_CHECKED} id="sc_id_recurs" />
			<label for="sc_id_recurs">De manière récursive</label>
		</p>
		<input type="submit" name="Submit" value="Rechercher" />
		<p>
			<a href="#comment" name="comment" onclick="swap_couche('1');"><img src="{DIR_TEMPLATE}/img/FAQ.png" width="32" height="32" alt="Voir / Cacher l'info" /> Info</a>
		</p>
		<blockquote id="Layer1" style="display: none;" class="info">
			<p>Quelques exemples d'utilisation :</p>
			<ul>
				<li>Pour effectuer une recherche de tous les fichiers jpg, vous pouvez taper ceci : <strong>*.jpg</strong></li>
				<li>Chercher les fichiers mp3 finissant par "libres" ou "libre" : <strong>*libre[s].mp3</strong></li>
			</ul>
		</blockquote>
	</form>
	{ERROR}
	<!-- BEGIN result -->
	<hr />
	
	<p>
		Voici le(s) résultat(s) de la recherche sur le terme &laquo; <b>{WORD}</b> &raquo; :
	</p>
	
	<div class="table">
		<table width="100%">
		<!-- BEGIN line -->
			<tr class="line">
				<td width="2%"><a href="{PATH_INFO}"><img src="{FILE_ICON}" width="24" height="24" border="0" align="middle" alt="Infos" /></a></td>
				<td width="30%"><a href="{PATH_INFO}">{PATH_FORMAT}</a></td>
				<td width="52%" align="right" class="description">{FILE_DESCRIPTION}</td>
				<td width="16%" align="right">{FILE_SIZE}</td>
			</tr>
		<!-- END line -->
		</table>
	</div>
	<br />
	<!-- END result -->
	
</fieldset>
