<span id="current">
	<h3>&laquo; <img src="{FOLDER_IMAGES}/mimetypes/{FILE_ICON}" width="32" height="32" border="0" align="middle" alt="Icone" /> {OBJECT_URL} &raquo;</h3>
	{DOWNLOAD_COUNT}
</span>

<!-- BEGIN pagination -->
<div class="pagination">
	<!-- BEGIN previous_page -->
		<span id="previous">
			<a href="?aff=info,{PREV_PATH}" title="Objet précédent"> &laquo; <img src="{FOLDER_IMAGES}/mimetypes/{PREV_FILE_ICON}" width="32" height="32" border="0" align="middle" alt="Icone" />{OBJ_PREV}</a>
		</span>
	<!-- END previous_page -->
	<!-- BEGIN next_page -->
		<span id="next">
			<a href="?aff=info,{NEXT_PATH}" title="Objet suivant"><img src="{FOLDER_IMAGES}/mimetypes/{NEXT_FILE_ICON}" width="32" height="32" border="0" align="middle" alt="Icone" />{OBJ_NEXT} &raquo; </a>
		</span>
	<!-- END next_page -->
</div>
<!-- END pagination -->

<h4>{DESCRIPTION}</h4>

<div class="main">
	{CONTENT}
</div>
