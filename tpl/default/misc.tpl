
<!-- BEGIN error -->
<span class="error">
	<img src="{FOLDER_TEMPLATE}/img/emblem-danger.png" width="16" height="16" />{ERROR}
</span>
<!-- END error -->

<!-- BEGIN status -->
<div class="status">
	{STATUS}
</div>
<!-- END status -->

<!-- BEGIN mkdir -->
<form method="post" name="form_mkdir" action="?act=mkdir,{OBJECT}">
<fieldset>
	<legend><img src="{FOLDER_TEMPLATE}/img/Folder-Yellow_Tigert.png" align="middle" width="32" height="32" alt="Edition" /> Création d'un répertoire </legend>
	
	Le répertoire sera créé dans {OBJECT}
	<br />
	<br />
	Nom : <input type="input" name="folder_name" width="20" />
	<br />
	Description :<br />
	<textarea name="folder_description" cols="50" rows="5"></textarea>
	<br />
	<a name="comment"><a href="#comment" onclick="swap_couche('1');"><img src="{FOLDER_TEMPLATE}/img/Control-Center2.png" width="32" height="32" alt="Options" /> Options</a></a>
	<div class="option" id="Layer1" style="display: none;">
		Galerie image :
		<input type="checkbox" name="folder_gallery" value="1" />
	</div>
	<br />
	<input type="submit" name="Submit" value="Créer" />

</fieldset>
</form>
<!-- END mkdir -->

<!-- BEGIN rename -->
<form method="post" name="form_rename" action="?act=rename,{OBJECT}">
<fieldset>
	<legend><img src="{FOLDER_TEMPLATE}/img/Folder-Yellow_Tigert.png" align="middle" width="32" height="32" alt="Renommage" /> Renommage </legend>
	
	Nouveau nom pour {OBJECT} : <input type="input" name="new_name" width="20" />
	<br />
	<input type="submit" name="Submit" value="Renommer" />

</fieldset>
</form>
<!-- END rename -->


