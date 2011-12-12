
<div class="plugin">

	<div class="table">

		<b>Contenu de l'archive :</b>

		<table width="100%">
		<!-- BEGIN zipfile -->
			<tr class="line">
				<td width="1%"><a href="{OBJECT}!{FILE_NAME}"><img src="img/mimetypes/{FILE_ICON}" width="24" height="24" border="0" align="middle" alt="Infos" /></a></td>
				<td width="81%" align="left"><a href="{OBJECT}!{FILE_NAME}">{FILE_NAME}</a></td>
				<td width="16%" align="right">{FILE_SIZE}</td>
				<td width="2%" align="right"><a href="{PATH_DOWNLOAD}" title="Télécharger"><img src="{FOLDER_TEMPLATE}/img/gnome-fs-bookmark.png" width="32" height="32" border="0" align="middle" alt="Télécharger" /></a></td>
			</tr>
		<!-- END zipfile -->
		</table>

	<p>
		<a href="{OBJECT}&amp;pact=extract">Extraire dans le répertoire parent</a>
		<p>
			{RAPPORT}
		</p>
	</p>

	</div>

	<div class="info" style="text-align: left">
		Taille de l'archive : {COMPRESSED_SIZE}<br />
		Taille de l'archive décompressé : {REAL_SIZE}<br />
		Nombre de fichiers : {NBR_FILE}<br />
		Status : {STATUS}<br />
		Commentaires : {COMMENT}
	</div>

</div>
