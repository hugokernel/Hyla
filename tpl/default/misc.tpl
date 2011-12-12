
<!-- BEGIN error -->
<div class="error">
	<img src="{DIR_TEMPLATE}/img/emblem-danger.png" width="16" height="16" />&nbsp;{ERROR}
</div>
<!-- END error -->

<!-- BEGIN status -->
<div class="status">
	{STATUS}
</div>
<!-- END status -->

<!-- BEGIN sort -->
	<div class="sort">
		<form method="post" name="dir_form" action="{OBJECT}">
			<select name="param[]"><!-- onchange="submit();"-->
				<option value="sort:-1" {SELECT_SORT}>&nbsp;&laquo; Ordre de la configuration &raquo;</option>
				<option value="sort:0" {SELECT_SORT_0}>&nbsp;Sans tri</option>
				<option value="sort:1" {SELECT_SORT_1}>&nbsp;Alphabétique A / Z</option>
				<option value="sort:2" {SELECT_SORT_2}>&nbsp;Alphabétique Z / A</option>
				<option value="sort:3" {SELECT_SORT_3}>&nbsp;Extensions   A / Z</option>
				<option value="sort:4" {SELECT_SORT_4}>&nbsp;Extensions   Z / A</option>
				<option value="sort:5" {SELECT_SORT_5}>&nbsp;Catégories   A / Z</option>
				<option value="sort:6" {SELECT_SORT_6}>&nbsp;Catégories   Z / A</option>
				<option value="sort:7" {SELECT_SORT_7}>&nbsp;Taille - / +</option>
				<option value="sort:8" {SELECT_SORT_8}>&nbsp;Taille + / -</option>
			</select>
			<label for="ffirst">
				<input type="checkbox" name="param[]" id="ffirst" value="ffirst:ok" {FFIRST_CHECKED} />
				Les répertoires en premier
			</label>
			<label for="grp">
				<input type="checkbox" name="param[]" id="grp" value="grp:ok" {GRP_CHECKED} />
				Grouper par critère de tri
			</label>
			<input type="submit" name="" value="Envoyer !" />
		</form>
	</div>
<!-- END sort -->

<!-- BEGIN toolbar -->
<div id="toolbar">
	<fieldset>
		<legend>Actions / Affichages</legend>

		<a href="{URL_COMMENT}" title="Voir la liste des derniers commentaires"><img src="{DIR_TEMPLATE}/img/comment.png" align="middle" width="32" height="32" alt="Commentaires" /> Derniers commentaires</a>
		<a href="{URL_SEARCH}" title="Faire une recherche à partir de l'emplacement courant"><img src="{DIR_TEMPLATE}/img/Find-Files1.png" align="middle" width="32" height="32" alt="Dossier vu à la loupe" /> Rechercher</a>
		
		<!-- BEGIN aff_slideshow --><a href="#" title="Lancer un diaporama" onclick="window.open('{URL_SLIDESHOW}', 'Diaporama', 'width=900,height=800,left=0,top=0');"><img src="{DIR_TEMPLATE}/img/emblem-multimedia.png" align="middle" width="32" height="32" alt="Stylo" /> Diaporama</a><!-- END aff_slideshow -->
		<!-- BEGIN aff_download --><a href="{URL_DOWNLOAD}" title="Télécharger"><img src="{DIR_TEMPLATE}/img/gnome-fs-bookmark.png" alt="Planète" align="middle" />Télécharger</a><!-- END aff_download -->

		<!-- BEGIN action_edit --><a href="{URL_EDIT}" title="Éditer l'objet courant"><img src="{DIR_TEMPLATE}/img/Editor.png" align="middle" width="32" height="32" alt="Stylo" /> Éditer</a><!-- END action_edit -->
		<!-- BEGIN action_addfile --><a href="{URL_UPLOAD}" title="De votre poste ou à distance"><img src="{DIR_TEMPLATE}/img/Floppy.png" align="middle" width="32" height="32" alt="Disquette" /> Ajouter un fichier</a><!-- END action_addfile -->
		<!-- BEGIN action_copy --><a href="{URL_COPY}"><img src="{DIR_TEMPLATE}/img/emblem-documents.png" align="middle" width="32" height="32" alt="Disquette" /> Copier</a><!-- END action_copy -->
		<!-- BEGIN action_move --><a href="{URL_MOVE}" title="Déplacer l'objet courant"><img src="{DIR_TEMPLATE}/img/gnome-dev-symlink.png" align="middle" width="32" height="32" alt="Doigt" /> Déplacer</a><!-- END action_move -->
		<!-- BEGIN action_rename --><a href="{URL_RENAME}" title="Renommer l'objet courant"><img src="{DIR_TEMPLATE}/img/PenWrite.png" align="middle" width="32" height="32" alt="Stylo" /> Renommer</a><!-- END action_rename -->
		<!-- BEGIN action_del --><a href="{URL_DEL}" title="Supprimer l'objet courant" onclick="return confirm('Voulez-vous vraiment supprimer l\'objet ?');"><img src="{DIR_TEMPLATE}/img/emblem-trash.png" align="middle" width="32" height="32" alt="Disquette" /> Supprimer</a><!-- END action_del -->
		<!-- BEGIN action_mkdir --><a href="{URL_MKDIR}" title="Créer un nouveau répertoire"><img src="{DIR_TEMPLATE}/img/Folder-Yellow_Tigert.png" align="middle" width="32" height="32" alt="Répertoire" />  Créer un répertoire</a><!-- END action_mkdir -->
		
		<!-- BEGIN aff_login --><a href="{URL_LOGIN}" title="Identifiez-vous"><img src="{DIR_TEMPLATE}/img/Lock-Screen.png" align="middle" width="32" height="32" alt="Cadenas" /> Se connecter</a><!-- END aff_login -->
		<!-- BEGIN aff_logout --><a href="{URL_LOGOUT}" title="Fermer votre session"><img src="{DIR_TEMPLATE}/img/Shutdown.png" align="middle" width="32" height="32" alt="Arrêt d'urgence" /> Déconnecter (<i>{USER_NAME}</i>)</a><!-- END aff_logout -->
		<!-- BEGIN aff_admin --><a href="{URL_ADMIN}" title="Administrer Hyla"><img src="{DIR_TEMPLATE}/img/GNOME-Laptop.png" align="middle" width="24" height="24" alt="Pc portable" /> Administration</a><!-- END aff_admin -->
		
	</fieldset>
</div>
<!-- END toolbar -->
