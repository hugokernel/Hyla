
<div style="width: 60%; padding: 20px">

<!-- BEGIN edit_description -->
<a name="description"> </a>

<form method="post" name="desc_form" action="?aff=edit,{OBJECT}&act=setdescription">
<fieldset>
	<legend><a name="comment"><a href="#" onclick="swap_couche('description');"><img src="{FOLDER_TEMPLATE}/img/Editor.png" align="middle" width="32" height="32" alt="Edition" /> Description</a></a> </legend>

	<div id="Layerdescription" style="display: none;">

		<p>
			<textarea name="description" cols="50" rows="7">{DESCRIPTION}</textarea>
		</p>

		<input type="submit" name="Submit" value="Modifier" />

	</div>
</fieldset>
</form>
<!-- END edit_description -->

<!-- BEGIN edit_plugins -->
<a name="plugins"> </a>
<form method="post" name="pg_form" action="?aff=edit,{OBJECT}&act=setplugin">
<fieldset>
	<legend><a name="comment"><a href="#" onclick="swap_couche('plugins');"><img src="{FOLDER_TEMPLATE}/img/Control-Center2.png" align="middle" width="32" height="32" alt="Edition plugin" /> Plugins</a></a> </legend>

	<div id="Layerplugins" style="display: none;">
		<p>
			<input name="pg_name" {PLUGIN_DEFAULT_CHECKED} type="radio" id="pg_name_default" />
			<label for="pg_name_default">
				<strong>Default</strong>
				Utilise le plugin par défaut pour les répertoires ( <em>{DEFAULT_PLUGIN}</em> )
			</label>
			<hr />
		</p>
		<!-- BEGIN plugin -->
		<p>
			<input name="pg_name" {PLUGIN_CHECKED} type="radio" id="pg_name_{PLUGIN_NAME}" />
			<label for="pg_name_{PLUGIN_NAME}">
				<strong>{PLUGIN_NAME}</strong>
				{PLUGIN_DESCRIPTION}
			</label>
		</p>
		<!-- END plugin -->
		<input type="submit" name="Submit" value="Modifier" />
	</div>

</fieldset>
</form>
<!-- END edit_plugins -->

<!--
<a name="image"> </a>
<form method="post" name="pg_form" action="?aff=edit,{OBJECT}&act=setimage">
<fieldset>
	<legend><a name="comment"><a href="#" onclick="swap_couche('images');"><img src="{FOLDER_TEMPLATE}/img/image.png" align="middle" width="32" height="32" alt="Edition image" /> Image</a></a> </legend>

	<div id="Layerimages" style="display: none;">
		<p>
			<label for="img_name_{IMAGE_NAME}">
				<img src="{FOLDER_TEMPLATE}/img/image.png" align="middle" width="32" height="32" alt="Edition image" />
				<input name="pg_name" {PLUGIN_CHECKED} type="radio" id="img_name_{IMAGE_NAME}" />
			</label>

			<img src="{FOLDER_TEMPLATE}/img/images/FAQ.png" align="middle" width="32" height="32" alt="Edition image" />
			<input name="pg_name" {PLUGIN_CHECKED} type="radio" id="pg_name_{PLUGIN_NAME}" />
		</p>
	</div>

</fieldset>
</form>
-->

</div>
