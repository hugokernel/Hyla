
<!-- BEGIN error -->
<div class="error">
    <img src="{DIR_IMAGE}/error.png" width="16" height="16" alt="Croix" />&nbsp;{VIEW_ERROR}
</div>
<!-- END error -->

<!-- BEGIN status -->
<div class="status">
    {VIEW_STATUS}
</div>
<!-- END status -->

<!-- BEGIN suggestion -->
<div class="suggestion">
    {SUGGESTION}
</div>
<!-- END suggestion -->

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
                Les dossiers en premier
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

        <a href="{URL_COMMENT}" title="Voir la liste des derniers commentaires"><img src="{DIR_IMAGE}/comment.png" alt="Commentaires" /> Derniers commentaires</a>
        <a href="{URL_SEARCH}" title="Faire une recherche à partir de l'emplacement courant"><img src="{DIR_IMAGE}/find.png" alt="Dossier vu à la loupe" /> Rechercher</a>

        <!-- BEGIN aff_slideshow --><a href="{URL_SLIDESHOW}" title="Lancer un diaporama" onclick="popup(this.href, 'scrollbars=yes,width=900,height=800,left=' + (screen.width / 2 - 450) + ',top=' + (screen.height / 2 - 400)); return false;"><img src="{DIR_IMAGE}/slideshow.png" alt="Stylo" /> Diaporama</a><!-- END aff_slideshow -->
        <!-- BEGIN aff_download --><a href="{URL_DOWNLOAD}" title="Télécharger l'objet courant"><img src="{DIR_IMAGE}/download.png" alt="Télécharger" /> Télécharger</a><!-- END aff_download -->

        <!-- BEGIN aff_info --><a href="{URL_INFO}" title="Informations sur l'objet"><img src="{DIR_IMAGE}/info.png" alt="Info" /> Info</a><!-- END aff_info -->

        <!-- BEGIN action_edit --><a href="{URL_EDIT}" title="Éditer l'objet courant"><img src="{DIR_IMAGE}/edit.png" alt="Stylo" /> Éditer</a><!-- END action_edit -->
        <!-- BEGIN action_addfile --><a href="{URL_UPLOAD}#upload_0" title="Ajouter un ou plusieurs fichiers de votre poste ou à distance dans le dossier courant"><img src="{DIR_IMAGE}/upload.png" alt="Ajout de fichiers" /> Ajout de fichier(s)</a><!-- END action_addfile -->
        <!-- BEGIN action_copy --><a href="{URL_COPY}"><img src="{DIR_IMAGE}/copy.png" alt="Copie" /> Copier</a><!-- END action_copy -->
        <!-- BEGIN action_move --><a href="{URL_MOVE}" title="Déplacer l'objet courant"><img src="{DIR_IMAGE}/move.png" alt="Déplacer" /> Déplacer</a><!-- END action_move -->
        <!-- BEGIN action_rename --><a href="{URL_RENAME}" title="Renommer l'objet courant"><img src="{DIR_IMAGE}/rename.png" alt="Renommer" /> Renommer</a><!-- END action_rename -->
        <!-- BEGIN action_del --><a href="{URL_DEL}" title="Supprimer l'objet courant" onclick="return confirm('Voulez-vous vraiment supprimer l\'objet ?');"><img src="{DIR_IMAGE}/delete.png" alt="Supprime" /> Supprimer</a><!-- END action_del -->
        <!-- BEGIN action_mkdir --><a href="{URL_MKDIR}" title="Créer un nouveau répertoire dans le dossier courant"><img src="{DIR_IMAGE}/mkdir.png" alt="Création de dossier" />  Créer un dossier</a><!-- END action_mkdir -->

        <!-- BEGIN aff_login --><a href="{URL_LOGIN}" title="Identifiez-vous"><img src="{DIR_IMAGE}/login.png" alt="S'authentifier" /> Se connecter</a><!-- END aff_login -->
        <!-- BEGIN aff_user --><a href="{URL_USER}" title="Modifier son profil" id="profile"><img src="{DIR_IMAGE}/users.png" width="24" height="24" alt="Édition du profil" /> Profil (<span id="username">{USER_NAME}</span>)</a><!-- END aff_user -->
        <!-- BEGIN aff_admin --><a href="{URL_ADMIN}" title="Administrer Hyla" id="administration"><img src="{DIR_IMAGE}/administration.png" width="24" height="24" alt="Administrer" /> Administration</a><!-- END aff_admin -->
        <!-- BEGIN aff_logout --><a href="{URL_LOGOUT}" title="Fermer votre session"><img src="{DIR_IMAGE}/logout.png" alt="Se déconnecter" /> Se déconnecter</a><!-- END aff_logout -->

    </fieldset>
</div>

<!-- END toolbar -->
