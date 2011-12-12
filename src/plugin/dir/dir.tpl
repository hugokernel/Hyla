<div class="table">
	<table width="100%">
	<!-- BEGIN line -->
	<!-- BEGIN line_header -->
		<tr>
			<td colspan="5" class="header">
				{HEADER_VALUE}
				<span class="header-info">{HEADER_INFO_VALUE}</span>
			</td>
		</tr>
	<!-- END line_header -->
	<!-- BEGIN line_content -->
		<tr class="line">
			<td width="2%"><a href="{PATH_INFO}"><img src="{FILE_ICON}" width="32" height="32" border="0" align="middle" alt="Infos" /></a></td>
			<td width="30%">
				<a href="{PATH_INFO}">{FILE_NAME}</a>
				<!-- BEGIN line_comment -->
				<a href="{PATH_INFO}#comment" title="{NBR_COMMENT} commentaire(s)"><img src="{DIR_TEMPLATE}/img/comment.png" width="32" height="32" border="0" align="middle" alt="Commentaires" /><!--<sub>{NBR_COMMENT}</sub>--></a>
				<!-- END line_comment -->
			</td>
			<td width="56%" align="right" class="description">{FILE_DESCRIPTION}</td>
			<td width="10%" align="right">{FILE_SIZE}</td>
			<td width="2%" align="right"><a href="{PATH_DOWNLOAD}" title="Télécharger"><img src="{DIR_TEMPLATE}/img/gnome-fs-bookmark.png" width="32" height="32" border="0" align="middle" alt="Télécharger" /></a></td>
		</tr>
	<!-- END line_content -->
	<!-- END line -->
	</table>
</div>
