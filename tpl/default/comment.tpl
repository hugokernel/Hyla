<!-- BEGIN current_comment -->
<a name="comment"><a href="#comment" onclick="swap_couche('comment');"><img src="{FOLDER_TEMPLATE}/img/comment.png" width="32" height="32" alt="Voir / Cacher les commentaires" /> {COMMENT_NBR} commentaire(s)</a></a>
<div id="Layercomment" style="display: none;">
	<div id="comment">
		<!-- BEGIN comment_line -->	
		<div class="comment_line">
			<div class="comment_info">
				{DATE} - <a href="{MAIL}">{AUTHOR}</a> <a href="{URL}">{URL}</a>
			</div>
			<div class="comment_content">
				{COMMENT}
			</div>
		</div>
		<!-- END comment_line -->	
	</div>

	<div id="comment_add">
		<form method="post" name="cm_form" action="?aff=info,{OBJECT}&act=addcomment">
			<fieldset>
				{ERROR}
				<legend><img src="{FOLDER_TEMPLATE}/img/comment.png" width="32" height="32" alt="Commentaires" /> Ajouter un commentaire</legend>
				<p>
					<label for="cm_author">Nom ou pseudo :</label>
					<input name="cm_author" id="cm_author" size="20" maxlength="255" value="{AUTHOR}" type="text" />
				</p>
				<p>
					<label for="cm_mail">Email (facultatif) :</label>
					<input name="cm_mail" id="cm_mail" size="20" maxlength="255" value="{MAIL}" type="text" />
				</p>
				<p>
					<label for="cm_site">Site Web (facultatif) :</label>
					<input name="cm_site" id="cm_site" size="30" maxlength="255" value="{SITE}" type="text" />
				</p>
				<p>
					<label for="cm_content">Commentaire :</label>
					<textarea name="cm_content" id="cm_content" cols="35" rows="7">{CONTENT}</textarea>
				</p>
				<input type="submit" name="Submit" value="Envoyer" />
			</fieldset>
		</form>
	</div>

</div>
<!-- END current_comment -->

<!-- BEGIN last_comment -->

	<div id="comment">
		<!-- BEGIN last_comment_line -->
		<div class="comment_line">
			<div class="comment_info">
				<a href="{PATH_INFO}"><img src="{FOLDER_IMAGES}mimetypes/{FILE_ICON}" width="32" height="32" border="0" align="middle" alt="Infos" /></a>
				{DATE} - <a href="{MAIL}">{AUTHOR}</a> <a href="{URL}">{URL}</a>
			</div>
			<div class="comment_content">
				{COMMENT}
			</div>
		</div>
		<!-- END last_comment_line -->	
	</div>

<!-- END last_comment -->
