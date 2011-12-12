
<p>
Taille maximale d'un fichier : <strong>{MAX_FILESIZE}</strong>
</p>
<select name="sort" onchange="location.href='?aff=upload,{OBJECT}&amp;file=' + this.options[this.selectedIndex].value">
	<option selected="selected">Nombre de fichiers à ajouter...</option>
	<option value="1">&nbsp;1</option>
	<option value="2">&nbsp;2</option>
	<option value="3">&nbsp;3</option>
	<option value="4">&nbsp;4</option>
</select>

<form enctype="multipart/form-data" method="post" name="form_upload" action="?aff=upload,{OBJECT}">
{STATUS}
<!-- BEGIN form_upload -->
<fieldset>
	<legend><a href="#" onclick="swap_couche('upload_{NUM}');"><img src="{FOLDER_TEMPLATE}/img/Floppy.png" align="middle" width="32" height="32" alt="Disquette" /> Ajout de fichier <strong>({NUM})</strong></a></legend>

	{ERROR}

	<div id="Layerupload_{NUM}" style="display: none;">
	<p>
		<input type="radio" name="ul_file_method[{NUM}]" value="local" checked="checked" id="ul_url_method_{NUM}" />
		<label for="ul_url_method_{NUM}">
			<img src="{FOLDER_TEMPLATE}/img/Disks.png" align="middle" width="32" height="32" alt="Disque dûr" /> Fichier local : 
		</label>
		<input type="file" name="ul_file_local[]" size="40" onclick="eval(getID('ul_url_method_{NUM}') + '.checked = true;');" />
	</p>
	<!-- BEGIN from_url -->
	<p>
		<input type="radio" name="ul_file_method[{NUM}]" value="fromurl" id="ul_file_method_{NUM}" />
		<label for="ul_file_method_{NUM}">
			<img src="{FOLDER_TEMPLATE}/img/WWW.png" align="middle" width="32" height="32" alt="Disque dûr" /> Fichier distant : 
		</label>
		<input type="text" name="ul_file_fromurl[]" size="40" onclick="eval(getID('ul_file_method_{NUM}') + '.checked = true;');" />
	</p>
	<!-- END from_url -->
	<p>
		<label for="ul_description">Description :</label>
		<textarea name="ul_description[]" cols="50" rows="5">{FILE_DESCRIPTION}</textarea>
	</p>
	</div>
</fieldset>
<br />
<!-- END form_upload -->
	<input type="submit" name="Submit" value="Envoyer" />
</form>
