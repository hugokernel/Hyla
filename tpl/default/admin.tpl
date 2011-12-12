
	<h2>Administration</h2>

	<div id="sidebar">
		<a href="{ADMIN_PAGE}"><img src="{DIR_TEMPLATE}/img/home.png" align="middle" width="32" height="32" alt="Maison" /> Accueil</a>
		<a href="{ADMIN_PAGE_CONF}"><img src="{DIR_TEMPLATE}/img/Control-Center2.png" align="middle" width="32" height="32" alt="Outils" /> Configuration</a>
		<a href="{ADMIN_PAGE_USERS}"><img src="{DIR_TEMPLATE}/img/users.png" align="middle" width="32" height="32" alt="Silhouettes" /> Utilisateurs</a>
		<a href="{ADMIN_PAGE_COMMENT}"><img src="{DIR_TEMPLATE}/img/comment.png" align="middle" width="32" height="32" alt="Commentaires" /> Commentaires</a>
		<a href="{ADMIN_PAGE_ANON}"><img src="{DIR_TEMPLATE}/img/gnome-fs-bookmark-missing.png" align="middle" width="32" height="32" alt="Poubelle" /> Fichiers anonymes</a>
		<a href="{ADMIN_PAGE_MAINTENANCE}"><img src="{DIR_TEMPLATE}/img/gnome-devel.png" align="middle" width="32" height="32" alt="Truelle" /> Maintenance</a>
	</div>

	<!-- BEGIN aff_home -->
		<h3><a href="http://www.digitalspirit.org/hyla/">Hyla</a> {HYLA_VERSION} Copyright (c) 2004-2006 Charles Rincheval</h3>

	<p>
		Il est important de toujours avoir la dernière version d'Hyla pour palier à d'éventuel problème de sécurité et bien sûr disposer des dernières nouveautés !
		<br />
		<!-- BEGIN test_version -->
			Tester si vous avez la dernière version en cliquant <a href="{TEST_VERSION}">sur ce lien</a>.
			<br />
			{STATUS_VERSION}
		<!-- END test_version -->

	</p>

	<fieldset>
		<legend>Récapitulatif de la configuration :</legend>
		<p>
			Configuration Php :
		</p>
		<ul>
			<li>Téléchargement de fichiers distants ( <em>allow_url_fopen</em> ) : <strong>{CONFIG_ALLOW_URL_FOPEN}</strong></li>
			<li>Autorise ou non le téléchargement de fichier sur le serveur ( <em>file_uploads</em> ) : <strong>{CONFIG_FILE_UPLOADS}</strong></li>
			<li>Taille maximale acceptée d'un fichier envoyé sur le serveur ( <em>upload_max_filesize</em> ) : <strong>{CONFIG_UPLOAD_MAX_FILESIZE}</strong></li>
		</ul>
		<p>
			Extensions :
		</p>
		<ul>
			<li>Bibliothèque GD (pour manipuler les images) : <strong>{EXTENSION_GD}</strong></li>
			<li>Bibliothèques EXIF (pour lire les données EXIF contenu dans certaine image) : <strong>{EXTENSION_EXIF}</strong></li>
		</ul>
		<p>
			Droits en écriture :
		</p>
		<ul>
			<li>Fichier conf ( &laquo; {FILE_INI} &raquo; ) : <strong>{ACCESS_FILE_INI}</strong></li>
			<li>Cache ( &laquo; {DIR_CACHE} &raquo; ) : <strong>{ACCESS_DIR_CACHE}</strong></li>
			<li>Fichiers anonymes ( &laquo; {DIR_ANON} &raquo; ) : <strong>{ACCESS_DIR_ANON}</strong></li>
		</ul>
	</fieldset>

	<p>
		Liens utiles :
	</p>

	<ul>
		<li><a href="http://www.digitalspirit.org/hyla/">Le site officiel de Hyla</a>
		<li><a href="http://www.digitalspirit.org/hyla/?aff=doc">La documentation</a>
		<li><a href="http://www.digitalspirit.org/forums/viewforum.php?id=11">Le forum dédié</a>
		<li><a href="http://www.digitalspirit.org/hyla/?aff=faq">Les questions les plus fréquemment posées</a>
		<li><a href="http://www.digitalspirit.org/blog/index.php">Le blog de développement de Hyla</a>
	</ul>

	<!-- END aff_home -->

	<!-- BEGIN aff_conf -->
	<h3>Edition du fichier de configuration ( {FILE_INI} )</h3>

	<blockquote class="info">
		Les modifications effectuées ici même peuvent aussi être effectuées en éditant le fichier /conf/hyla.ini
	</blockquote>
	{ERROR}

	<form method="post" name="ad_form" action="{ADMIN_PAGE_SAVECONF}">
	<fieldset>
		<legend><a href="#" onclick="swap_couche('gen');">Général</a></legend>
		<div id="Layergen" style="display: none;">
			<label for="conf_webmaster_mail">
				Courriel du webmestre :
			</label>
			<input type="text" name="conf_webmaster_mail" id="conf_webmaster_mail" size="25" value="{WEBMASTER_MAIL}" />
			<p class="help">
				C'est à ce mail que seront envoyés les notifications (ajout de fichiers anonymes...)
			</p>
			<label for="conf_template">
				Le template par défaut :
			</label>
			<select name="conf_template" id="conf_template">
				<!-- BEGIN aff_conf_template -->
				<option value="{TEMPLATE_NAME}" {CONF_TEMPLATE_NAME}>{TEMPLATE_NAME}</option>
				<!-- END aff_conf_template -->
			</select>
			<p class="help">
				Il s'agit du répertoire dans (tpl/) contenant le template définissant l'apparence.
			</p>
			<label for="conf_lng">
				Langue :
			</label>
			<input type="text" name="conf_lng" id="conf_lng" size="8" value="{LNG}" />
			<p class="help">
				La langue, assurez-vous qu'un répertoire contenant les fichiers adéquates se trouvent bien dans le répertoire l10n/ .
			</p>
			<label for="conf_title">
				Titre navigateur :
			</label>
			<input type="text" name="conf_title" id="conf_title" size="25" value="{TITLE}" />
			<p class="help">
				Il s'agit du texte mis entre les balises "title" qui apparaitra sur la barre de titre du navigateur.
			</p>
		</div>
	</fieldset>
	<br />
	<fieldset>
		<legend><a href="#" onclick="swap_couche('filedir');">Ajout de fichiers et répertoires</a></legend>
		<div id="Layerfiledir" style="display: none;">
			<label for="conf_file_chmod">
				Les droits à attribuer aux fichiers uploadés :
			</label>
			<input type="text" name="conf_file_chmod" id="conf_file_chmod" size="8" value="{FILE_CHMOD}" />
			<p class="help">
				Il est fortement recommandé pour des raisons de sécurité évidente de laiser la valeur par défaut !
			</p>
			<label for="conf_dir_chmod">
				Les droits à attribuer aux répertoires créés :
			</label>
			<input type="text" name="conf_dir_chmod" id="conf_dir_chmod" size="8" value="{DIR_CHMOD}" />
			<p class="help">
				Il est fortement recommandé pour des raisons de sécurité évidente de laisser la valeur par défaut !
			</p>
			<label for="conf_anonymous_add_file">
				Autoriser l'ajout de fichier anonyme :
			</label>
			<select name="conf_anonymous_add_file" id="conf_anonymous_add_file">
				<option value="false" {CONF_ANONYMOUS_ADD_FILE_0}>Non</option>
				<option value="true" {CONF_ANONYMOUS_ADD_FILE_1}>Oui</option>
			</select>
			<p class="help">
				En autorisant l'envoie de fichiers anonyme, toute personne non authentifiée pourra envoyer des fichiers,
				ils seront placés dans un lieu accessible uniquement par un usager connecté et ayant les droits d'administrer ces derniers.
			</p>
			<label for="conf_send_mail">
				Envoyer un mail lors de l'envoie d'un fichier anonyme :
			</label>
			<select name="conf_send_mail" id="conf_send_mail">
				<option value="false" {CONF_SEND_MAIL_0}>Non</option>
				<option value="true" {CONF_SEND_MAIL_1}>Oui</option>
			</select>
			<p class="help">
				A chaque fichier anonyme envoyé, vous recevrez une notification par courriel.
			</p>
		</div>
	</fieldset>
	<br />
	<fieldset>
		<legend><a href="#" onclick="swap_couche('list');">Listage de répertoires</a></legend>
		<div id="Layerlist" style="display: none;">
			<label for="conf_sort">
				Le tri par défaut :
			</label>
			<select name="conf_sort" id="conf_sort">
				<option value="0" {CONF_SORT_0}>&nbsp;Ordre par défaut (dépend du système de fichiers)</option>
				<option value="1" {CONF_SORT_1}>&nbsp;Alphabétique A / Z</option>
				<option value="2" {CONF_SORT_2}>&nbsp;Alphabétique Z / A</option>
				<option value="3" {CONF_SORT_3}>&nbsp;Extensions   A / Z</option>
				<option value="4" {CONF_SORT_4}>&nbsp;Extensions   Z / A</option>
				<option value="5" {CONF_SORT_5}>&nbsp;Catégories   A / Z</option>
				<option value="6" {CONF_SORT_6}>&nbsp;Catégories   Z / A</option>
				<option value="7" {CONF_SORT_7}>&nbsp;Taille - / +</option>
				<option value="8" {CONF_SORT_8}>&nbsp;Taille + / -</option>
			</select>
			<p class="help">
				Défini l'ordre ou seront affiché les fichiers et répertoires.
			</p>
			<label for="conf_folder_first">
				Mettre les répertoires en premier ?
			</label>
			<select name="conf_folder_first" id="conf_folder_first">
				<option value="false" {CONF_FOLDER_FIRST_0}>Non</option>
				<option value="true" {CONF_FOLDER_FIRST_1}>Oui</option>
			</select>
			<p class="help">
				Affiche les répertoires en premier.
			</p>
			<label for="conf_group_by_sort">
				Grouper par critère de tri :
			</label>
			<select name="conf_group_by_sort" id="conf_group_by_sort">
				<option value="false" {CONF_GROUP_BY_SORT_0}>Non</option>
				<option value="true" {CONF_GROUP_BY_SORT_1}>Oui</option>
			</select>
			<p class="help">
				Si le tri courant est alphabétique, les fichiers commençant par la lettre A seront groupés ensemble,
				suivi des fichiers commençant la lettre B, et ainsi de suite...et cela de manière dynamique, par rapport au critère de tri.
			</p>
			<label for="conf_nbr_obj">
				Nombre d'objet par page :
			</label>
			<input type="text" name="conf_nbr_obj" id="conf_nbr_obj" size="3" value="{NBR_OBJ}" />
			<p class="help">
				Pour afficher tous les fichiers et répertoires, indiquez 0 mais cela est FORTEMENT non recommandé pour des raisons de charge serveur
				dans le cas de génération de galeries, vous êtes prévenu !!!
			</p>
			<label for="conf_default_plugin">
				Plugin des répertoires par défaut :
			</label>

			<select name="conf_default_plugin" id="conf_default_plugin">
				<!-- BEGIN aff_conf_plugin -->
				<option value="{PLUGIN_NAME}" {CONF_PLUGIN_NAME}>{PLUGIN_NAME} ( {PLUGIN_DESCRIPTION} ) </option>
				<!-- END aff_conf_plugin -->
			</select>

<!--			<input type="text" name="conf_default_plugin" id="conf_default_plugin" size="15" value="{DIR_DEFAULT_PLUGIN}" />-->
			<p class="help">
				Spécifiez ici le plugin par défaut à utiliser, ainsi, si vous désirez générer uniquement des galerie photos avec le plugin zenphoto, choisissez "zenphoto".
			</p>
			<label for="conf_view_hidden_file">
				Afficher les fichiers cachés ou non :
			</label>
			<select name="conf_view_hidden_file" id="conf_view_hidden_file">
				<option value="false" {CONF_VIEW_HIDDEN_FILE_0}>Non</option>
				<option value="true" {CONF_VIEW_HIDDEN_FILE_1}>Oui</option>
			</select>
			<p class="help">
				En autorisant l'affichage des fichiers cachés, vous afficherez les fichiers commençant par un "." .
			</p>
		</div>
	</fieldset>
	<br />
	<fieldset>
		<legend><a href="#" onclick="swap_couche('misc');">Divers</a></legend>
		<div id="Layermisc" style="display: none;">
			<label for="conf_download_counter">
				Activer le compteur de téléchargement :
			</label>
			<select name="conf_download_counter" id="conf_download_counter">
				<option value="no" {CONF_DOWNLOAD_COUNTER_0}>Non</option>
				<option value="yes" {CONF_DOWNLOAD_COUNTER_1}>Oui</option>
			</select>
			<p class="help">
				 Cette option vous permet de compter les téléchargements et non les visualisations.
			</p>
			<label for="conf_view_toolbar">
				Toujours voir les actions :
			</label>
			<select name="conf_view_toolbar" id="conf_view_toolbar">
				<option value="false" {CONF_VIEW_TOOLBAR_0}>Non</option>
				<option value="true" {CONF_VIEW_TOOLBAR_1}>Oui</option>
			</select>
			<p class="help">
				Permet d'afficher les liens vers les actions de la barre d'outils même lorsque l'on n'est pas connecté
			</p>
			<label for="conf_view_tree">
				Afficher l'arborescence des répertoires :
			</label>
			<select name="conf_view_tree" id="conf_view_tree">
				<option value="false" {CONF_VIEW_TREE_0}>Non</option>
				<option value="true" {CONF_VIEW_TREE_1}>Oui</option>
			</select>
			<p class="help">
				Permet d'afficher tous les répertoires en arborescence.
			</p>
		</div>
	</fieldset>
		<p>
			<input type="submit" name="Submit" value="Sauvegarder" />
		</p>
	</form>
	<!-- END aff_conf -->

	<!-- BEGIN comment -->
	<div id="comment">

		<p>
			{MSG}
		</p>

		<!-- BEGIN comment_line -->
		<div class="comment_line">
			<div class="comment_info">
				<a href="{ADMIN_DEL_COMMENT}"><img src="{DIR_TEMPLATE}/img/emblem-trash.png" width="32" height="32" border="0" align="middle" alt="Infos" /></a>
				<a href="{PATH_INFO}"><img src="{FILE_ICON}" width="32" height="32" border="0" align="middle" alt="Infos" /></a> {PATH_FORMAT}
				{DATE} - <a href="{MAIL}">{AUTHOR}</a> <a href="{URL}">{URL}</a>
			</div>
			<div class="comment_content">
				{COMMENT}
			</div>
		</div>
		<!-- END comment_line -->	
	</div>
	<!-- END comment -->

	<!-- BEGIN anon -->
	<div class="table">
		{MSG}
		<table width="100%">
		<!-- BEGIN anon_line -->
			<tr class="line">
				<td width="2%"><a href="{ADMIN_ANON_DEL}" title="Supprimer le fichier" onclick="return confirm('Voulez-vous vraiment supprimer définitivement l\'objet ?');"><img src="{DIR_TEMPLATE}/img/emblem-trash.png" width="32" height="32" border="0" align="middle" alt="Corbeille" /></a> </td>
				<td width="2%"><a href="{ADMIN_ANON_MOVE}" title="Déplacer le fichier"><img src="{DIR_TEMPLATE}/img/gnome-dev-symlink.png" width="32" height="32" border="0" align="middle" alt="Doigt" /></a> </td>
				<td width="30%"><img src="{FILE_ICON}" width="32" height="32" border="0" align="middle" alt="Infos" /> {FILE_NAME}</td>
				<td width="56%" align="right" class="description">{FILE_DESCRIPTION}</td>
				<td width="10%" align="right">{FILE_SIZE}</td>
				<td width="2%" align="right"><a href="{PATH_DOWNLOAD}" title="Télécharger"><img src="{DIR_TEMPLATE}/img/gnome-fs-bookmark.png" width="32" height="32" border="0" align="middle" alt="Télécharger" /></a></td>
			</tr>
		<!-- END anon_line -->
		</table>

		<!-- BEGIN anon_move -->
		<form method="post" name="form_move" action="{FORM_ANON_MOVE}">
			<fieldset>
				<legend><img src="{DIR_TEMPLATE}/img/gnome-dev-symlink.png" align="middle" width="32" height="32" alt="Renommage" /> Déplacer le fichier &laquo; {FILE} &raquo;  </legend>
				{ERROR}
				<p>
					<label for="mv_destination">
						Choisissez le répertoire de destination :
					</label>
					<select name="mv_destination" id="mv_destination">
						<option value="/">/</option>
						<!-- BEGIN anon_move_dir_occ --><option value="{DIR_NAME}">{DIR_NAME}</option><!-- END anon_move_dir_occ -->
					</select>
				</p>
				<p>
					<label for="mv_redirect">
						Être redirigé vers l'objet :
					</label>
					<input type="checkbox" name="mv_redirect" id="mv_redirect" value="1" checked="checked" />
				</p>
				<input type="submit" name="Submit" value="Déplacer" />
			</fieldset>
		</form>
		<!-- END anon_move -->

	</div>
	<!-- END anon -->

	<!-- BEGIN users -->

	<p>
		<a href="{ADMIN_USER_ADD}">Ajouter un utilisateur</a>
	</p>

	<table class="tab" width="100%" cellspacing="0">
		<tr>
			<th rowspan="2">Id</th>
			<th rowspan="2">Nom</th>
			<th colspan="2">Droits sur les fichiers et répertoires</th>
			<th colspan="2">Droits sur les fichiers</th>
			<th colspan="2">Droits sur les répertoires</th>
			<th rowspan="2">Administration</th>
			<th rowspan="2">Actions</th>
		</tr>
		<tr>
			<th>Ajout de commentaires</th>
			<th>Edition</th>
			<th>Ajout fichiers</th>
			<th>Suppression fichiers</th>
			<th>Création répertoires</th>
			<th>Suppression répertoires</th>
		</tr>
		<!-- BEGIN users_line -->
		<tr class="line">
			<td>{USER_ID}</td>
			<td>{USER_NAME}</td>
			<td align="center">{PERM_ADD_COMMENT}</td>
			<td align="center">{PERM_EDIT_FILE}</td>
			<td align="center">{PERM_ADD_FILE}</td>
			<td align="center">{PERM_DEL_FILE}</td>
			<td align="center">{PERM_CREATE_DIR}</td>
			<td align="center">{PERM_DEL_DIR}</td>
			<td align="center">{PERM_ADMIN}</td>
			<td align="center"><a href="{ADMIN_USER_EDIT}">Modifier</a><!-- BEGIN users_line_del --> - <a href="{ADMIN_USER_DEL}" onclick="return confirm('Voulez-vous vraiment supprimer l\'utilisateur ?');">Supprimer</a><!-- END users_line_del --></td>
		</tr>
		<!-- END users_line -->
	</table>

	<blockquote class="info">
			L'utilisateur Anonymous correspond à toute personne non connectée (qui n'est pas authentifiée), prenez garde en éditant ses droits !
			<br />
			<br />
			Pour avoir les permissions nécessaires au déplacement ou au renommage d'un objet, il faut que l'utilisateur concerné dispose des droits d'ajouter et de supprimer.
			<br />
			Si vous modifiez les droits d'un utilisateur, n'oubliez pas que les modifications prendront effet à sa prochaine connection !
	</blockquote>

	<!-- BEGIN user_edit -->
	<div id="form_misc">
		<fieldset>
			{MSG}
			<legend>Edition de l'utilisateur &laquo; {USER_NAME} &raquo; </legend>
			<!-- BEGIN user_edit_password -->
			<form method="post" name="ad_form" action="{FORM_USER_EDIT_PASSWORD}">
				<p>
					<label for="ad_password">Changer le mot de passe :</label>
					<input name="ad_password" id="ad_password" size="20" maxlength="255" value="{PASSWORD}" type="password" />
				</p>
				<p>
					<label for="ad_password_bis">Confirmer le mot de passe :</label>
					<input name="ad_password_bis" id="ad_password_bis" size="20" maxlength="255" value="{PASSWORD_BIS}" type="password" />
				</p>
				<input type="submit" name="Submit" value="Modifier le mot de passe" />
			</form>
			<!-- END user_edit_password -->
			<form method="post" name="ad_form" action="{FORM_USER_EDIT_PERM}">
				<p>
					<label for="ad_add_comment">Ajout de commentaires :</label>
					<input name="ad_add_comment" id="ad_add_comment" type="checkbox" {CHECKBOX_ADD_COMMENT} />
				</p>
				<p>
					<label for="ad_edit_file">Edition de fichiers :</label>
					<input name="ad_edit_file" id="ad_edit_file" type="checkbox" {CHECKBOX_EDIT_FILE} />
				</p>
				<p>
					<label for="ad_add_file">Ajout de fichiers :</label>
					<input name="ad_add_file" id="ad_add_file" type="checkbox" {CHECKBOX_ADD_FILE}  />
				</p>
				<p>
					<label for="ad_del_file">Suppression de fichiers :</label>
					<input name="ad_del_file" id="ad_del_file" type="checkbox" {CHECKBOX_DEL_FILE} />
				</p>
				<p>
					<label for="ad_create_dir">Création de répertoires :</label>
					<input name="ad_create_dir" id="ad_create_dir" type="checkbox" {CHECKBOX_CREATE_DIR} />
				</p>
				<p>
					<label for="ad_del_dir">Suppression de répertoires :</label>
					<input name="ad_del_dir" id="ad_del_dir" type="checkbox" {CHECKBOX_DEL_DIR} />
				</p>
				<p>
					<label for="ad_admin">Accès à l'administration :</label>
					<input name="ad_admin" id="ad_admin" type="checkbox" {CHECKBOX_ADMIN} />
				</p>
				<input type="submit" name="Submit" value="Modifier les droits" />
			</form>
		</fieldset>
	</div>
	<!-- END user_edit -->

	<!-- BEGIN user_add -->
	<div id="form_misc">
		<form method="post" name="ad_form" action="{FORM_USER_SAVE}">
			<fieldset>
				{ERROR}
				<legend>Ajout d'un utilisateur</legend>
				<p>
					<label for="ad_login">Nom :</label>
					<input name="ad_login" id="ad_login" size="20" maxlength="32" value="{NAME}" type="text" />
					<p class="help">
						Toutes les lettres de l'alphabet sont acceptées ainsi que les chiffres,
						le trait d'union (-) et l'underscore (_), attention tout de même,
						le nom doit commencer par une lettre et est limité à 32 caractères.
					</p>
				</p>
				<p>
					<label for="ad_password">Mot de passe :</label>
					<input name="ad_password" id="ad_password" size="20" maxlength="255" value="" type="password" />
				</p>
				<p>
					<label for="ad_password_bis">Confirmation du mot de passe :</label>
					<input name="ad_password_bis" id="ad_password_bis" size="20" maxlength="255" value="" type="password" />
				</p>
				<p>
					<label for="ad_add_comment">Ajout de commentaires :</label>
					<input name="ad_add_comment" id="ad_add_comment" type="checkbox" />
				</p>
				<p>
					<label for="ad_add_file">Ajout de fichiers :</label>
					<input name="ad_add_file" id="ad_add_file" type="checkbox"  />
				</p>
				<p>
					<label for="ad_edit_file">Edition de fichiers :</label>
					<input name="ad_edit_file" id="ad_edit_file" type="checkbox" />
				</p>
				<p>
					<label for="ad_del_file">Suppression de fichiers :</label>
					<input name="ad_del_file" id="ad_del_file" type="checkbox" />
				</p>
				<p>
					<label for="ad_create_dir">Création de répertoires :</label>
					<input name="ad_create_dir" id="ad_create_dir" type="checkbox" />
				</p>
				<p>
					<label for="ad_del_dir">Suppression de répertoires :</label>
					<input name="ad_del_dir" id="ad_del_dir" type="checkbox" />
				</p>
				<p>
					<label for="ad_admin">Accès à l'administration :</label>
					<input name="ad_admin" id="ad_admin" type="checkbox" />
				</p>
				<input type="submit" name="Submit" value="Ajouter" />
			</fieldset>
		</form>
	</div>
	<!-- END user_add -->

	<!-- END users -->

	<!-- BEGIN maintenance -->
	<div id="form_misc">
		<ul>
			<li>
				<a href="{ADMIN_PAGE_MAINTENANCE_PURGE}">Vider le cache</a>
				<p class="help">
					Supprime tous les fichiers "cache", utile si des erreurs de lecture d'archives se produisent
				</p>
				<p>
					{PURGE_RAPPORT}
				</p>
			</li>
			<li>
				<a href="{ADMIN_PAGE_MAINTENANCE_SYNC}">Lancer une synchronisation</a>
				<p class="help">
					Si vous supprimer des fichiers ou des répertoires sans passer par l'interface de Hyla (ftp...), il peut être utile d'effectuer une synchronisation
					de la base de données avec le système de fichiers.
				</p>
				<p>
					{SYNC_RAPPORT}
				</p>
			</li>
		</ul>

	</div>
	<!-- END maintenance -->
