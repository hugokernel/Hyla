<!-- BEGIN current_comment -->
<div style="clear: both">
	<a href="#comment" onclick="swap_layer('comment');"><img src="{DIR_IMAGE}/comment.png" width="32" height="32" alt="Voir / Cacher les commentaires" /> {COMMENT_NBR} commentaire(s)</a>
</div>

<div style="width: 80%">

	<div id="comment" class="jhidden">

		<p>
			{MSG}
		</p>

		<!-- BEGIN comment_line -->
		<div id="comment,{ID}">
			<p class="comment_info">
				Le {DATE} par <strong><a href="{URL}">{AUTHOR}</a></strong> | <a href="#comment,{ID}">#</a>
			</p>
			<blockquote class="comment_content">
				<p>
					{COMMENT}
				</p>
			</blockquote>
		</div>
		<!-- END comment_line -->	

		<!-- BEGIN add_comment -->
		<form method="post" name="cm_form" action="{FORM_ADD_COMMENT}#comment" style="width: 60%">
			<fieldset>
				{ERROR}
				<legend>Ajouter un commentaire</legend>
				<p>
					<label for="cm_author">Nom ou pseudo :</label>
					<input name="cm_author" id="cm_author" size="20" maxlength="255" value="{AUTHOR}" type="text" />
				</p>
				<p>
					<label for="cm_mail">Email (facultatif) :</label>
					<input name="cm_mail" id="cm_mail" size="20" maxlength="255" value="{EMAIL}" type="text" />
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
		<!-- END add_comment -->

	</div>

</div>
<!-- END current_comment -->

<!-- BEGIN last_comment -->
<h2>Liste des derniers commentaires</h2>

<p>
	{MSG}
</p>

<!-- BEGIN last_comment_line -->
<div id="comment,{ID}">
	<p class="comment_info">
		<a href="{PATH_INFO}"><img src="{FILE_ICON}" width="32" height="32" border="0" align="middle" alt="Infos" /></a>
		<strong>{PATH_FORMAT}</strong>
		<br />
		Le {DATE} par <strong><a href="{URL}">{AUTHOR}</a></strong> | <a href="#comment,{ID}">#</a>
	</p>
	<blockquote class="comment_content">
		<p>
			{COMMENT}
		</p>
	</blockquote>
</div>
<!-- END last_comment_line -->	
<!-- END last_comment -->
