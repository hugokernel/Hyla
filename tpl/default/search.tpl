
<fieldset>
	<legend><img src="{DIR_IMAGE}/find.png" align="middle" width="32" height="32" alt="Recherche" /> {LANG:Search in &laquo; %s &raquo;}</legend>

	<form method="post" name="form_upload" action="{FORM_SEARCH}">
		<p>
			<label for="sc_id_content">{LANG:Keyword search}</label> 
			<input type="text" name="word" size="40" value="{WORD}" id="sc_id_content" />
		</p>
		<p>
			<input type="checkbox" name="scandir" {SCANDIR_CHECKED} id="sc_id_scandir" />
			<label for="sc_id_scandir">{LANG:Search in dir name}</label>
		</p>
		<p>
			<input type="checkbox" name="recurs" {RECURS_CHECKED} id="sc_id_recurs" />
			<label for="sc_id_recurs">{LANG:In a recursive way}</label>
		</p>
		<input type="submit" name="Submit" value="{LANG:Searching}" />

		<p>
			<a href="#info" name="comment" onclick="swap_layer('layer_info');" title="{LANG:View / Hide}"><img src="{DIR_IMAGE}/help.png" width="32" height="32" alt="Help" /> {LANG:Information}</a>
		</p>
		<blockquote id="layer_info" class="jhidden">
			<div class="info">
				<p>{LANG:Examples of use :}</p>
				<ul>
					<li>{LANG:For searching all jpg, enter *.jpg}</li>
					<li>{LANG:For searching all file with gray or grey in name, enter *gr[ae]y}</li>
				</ul>
			</div>
		</blockquote>
	</form>
	{ERROR}
	<!-- BEGIN result -->
	<hr />

	<p>
		{LANG:Search results for &laquo; %s &raquo; :}
	</p>
	
	<table width="100%" class="line" summary="Liste des résultats de la recherche">
		<tr>
			<th>Objet</th>
			<th>Description</th>
			<th>Taille</th>
		</tr>
	<!-- BEGIN line -->
		<tr>
			<td><img src="{FILE_ICON}" class="icon" alt="Infos" /> {PATH_FORMAT}</td>
			<td class="description">{FILE_DESCRIPTION}</td>
			<td align="right">{FILE_SIZE}</td>
		</tr>
	<!-- END line -->
	</table>
	<br />
	<!-- END result -->

</fieldset>
