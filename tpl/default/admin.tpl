
    <h2>Administration</h2>

    <div id="sidebar">
        <a href="{ADMIN_PAGE}"><img src="{DIR_IMAGE}/home.png" align="middle" width="32" height="32" alt="Maison" /> Accueil</a>
        <a href="{ADMIN_PAGE_CONF}" title="Configuration générale de Hyla"><img src="{DIR_IMAGE}/config-gen.png" align="middle" width="32" height="32" alt="Outils" /> Configuration</a>
        <a href="{ADMIN_PAGE_USERS}" title="Ajout, modification, suppression des utilisateurs"><img src="{DIR_IMAGE}/users.png" align="middle" width="32" height="32" alt="Silhouettes" /> Utilisateurs</a>
        <a href="{ADMIN_PAGE_GROUPS}" title="Ajout, modification, suppression des groupes"><img src="{DIR_IMAGE}/groups.png" align="middle" width="32" height="32" alt="Valises" /> Groupes</a>
        <a href="{ADMIN_PAGE_RIGHTS}" title="Gestion des droits"><img src="{DIR_IMAGE}/rights.png" align="middle" width="32" height="32" alt="Les droits" /> Droits</a>
        <a href="{ADMIN_PAGE_COMMENT}" title="Visualisation des commentaires"><img src="{DIR_IMAGE}/comment.png" align="middle" width="32" height="32" alt="Commentaires" /> Commentaires</a>
        <a href="{ADMIN_PAGE_ANON}" title="Gestion des fichiers envoyés de manière anonymes"><img src="{DIR_IMAGE}/anonymous-file.png" align="middle" width="32" height="32" alt="Poubelle" />&nbsp;Fichiers&nbsp;anonymes</a>
        <a href="{ADMIN_PAGE_MAINTENANCE}" title="Maintenance générale de Hyla"><img src="{DIR_IMAGE}/maintenance.png" align="middle" width="32" height="32" alt="Truelle" /> Maintenance</a>
    </div>

    <!-- BEGIN aff_home -->
    <p>
        <a href="http://www.hyla-project.org/"><img src="{DIR_ROOT}img/hyla.png" alt="Une rainette verte" /></a>
    </p>

    <h3>Hyla {HYLA_VERSION}, copyright (c) 2004-2012 Charles Rincheval</h3>

    <p>
        Il est important de toujours avoir la dernière version d'Hyla pour palier d'éventuels problèmes de sécurité et bien sûr disposer des dernières nouveautés !

        <!-- BEGIN test_version -->
        <br />
        Testez si vous avez la dernière version en cliquant <a href="{TEST_VERSION}">sur ce lien</a>.
    </p>
    <p>
        <strong>{STATUS_VERSION}</strong>
        <!-- END test_version -->
    </p>

    <fieldset>
        <legend><a href="#configuration" onclick="swap_layer('configuration');">Récapitulatif de la configuration</a></legend>

        <!-- BEGIN test_ok --><span class="ok">Ok</span><!-- END test_ok -->
        <!-- BEGIN test_no --><span class="no">Non</span><!-- END test_no -->

        <div id="configuration" class="jhidden">

            <p>
                Dossier de partage ( <em>FOLDER_ROOT</em> ) du fichier de configuration {CONFIG_FILE} : <strong>{FOLDER_ROOT}</strong>
            </p>
            <ul>
                <li>Lecture : {FOLDER_ROOT_READING}</li>
                <li>Écriture : {FOLDER_ROOT_WRITING}</li>
            </ul>

            {FOLDER_ROOT_ERROR_MSG}

            <p>
                <em>
                    Pour information, le chemin vers le dossier d'installation de Hyla est : <strong>{PATH_TO_SCRIPT}</strong>
                </em>
            </p>

            <hr />
            
            <p>
                {WEBMASTER_MAIL}
            </p>

            <hr />

            <p>
                Configuration Php :
            </p>
            <ul>
                <li>Téléchargement de fichiers distants ( <em>allow_url_fopen</em> ) : {CONFIG_ALLOW_URL_FOPEN}</li>
                <li>Autorise ou non le téléchargement de fichier sur le serveur ( <em>file_uploads</em> ) : {CONFIG_FILE_UPLOADS}</li>
                <li>Taille maximale acceptée d'un fichier envoyé sur le serveur ( <em>upload_max_filesize</em> ) : {CONFIG_UPLOAD_MAX_FILESIZE}</li>
            </ul>
            <p>
                Extensions :
            </p>
            <ul>
                <li>Bibliothèque GD (pour manipuler les images) : {EXTENSION_GD}</li>
                <li>Bibliothèques EXIF (pour lire les données EXIF contenues dans certaine image) : {EXTENSION_EXIF}</li>
            </ul>
            <p>
                Droits en écriture :
            </p>
            <ul>
                <li>Fichier configuration ( &laquo; {FILE_INI} &raquo; ) : {ACCESS_FILE_INI}</li>
                <li>Cache ( &laquo; {DIR_CACHE} &raquo; ) : {ACCESS_DIR_CACHE}</li>
                <li>Fichiers anonymes ( &laquo; {DIR_ANON} &raquo; ) : {ACCESS_DIR_ANON}</li>
            </ul>
        </div>
    </fieldset>

    <p>
        Liens utiles :
    </p>

    <ul>
        <li><a href="{DIR_ROOT}doc/{L10N}/index.htm" onclick="popup(this.href); return false;" title="Aide contextuelle">La documentation</a></li>
        <li><a href="http://www.hyla-project.org/">Le site officiel de Hyla</a></li>
        <li><a href="http://www.hyla-project.org/doc">La documentation en ligne</a></li>
        <li><a href="http://www.hyla-project.org/forums/">Les forums de discussions</a></li>
        <li><a href="http://www.digitalspirit.org/blog/index.php">Le blog de développement de Hyla</a></li>
    </ul>

    <!-- END aff_home -->

    <!-- BEGIN aff_conf -->
    <h3>Édition du fichier de configuration ( {FILE_INI} )</h3>

    <blockquote class="info">
        Les modifications effectuées ici peuvent aussi être faites en éditant le fichier {FILE_INI}.
    </blockquote>

    <p>
        <a href="{DIR_ROOT}doc/{L10N}/administration.htm#conf" onclick="popup(this.href); return false;" title="Aide contextuelle"><img src="{DIR_IMAGE}/help.png" alt="Point d'interrogation" /></a>
    </p>

    {ERROR}
    {STATUS}

    <form method="post" name="ad_form" action="{ADMIN_PAGE_SAVECONF}">

        <fieldset>
            <legend><a href="#display" onclick="swap_layer('display');">Affichage</a></legend>
            <div id="display" class="jhidden">
                <label for="conf_title">
                    Titre navigateur :
                </label>
                <input type="text" name="conf_title" id="conf_title" size="25" value="{TITLE}" />
                <p class="help">
                    Il s'agit du texte mis entre les balises "title" qui apparaîtra sur la barre de titre du navigateur.
                </p>

                <label for="conf_template">
                    Le modèle par défaut :
                </label>
                <select name="conf_template" id="conf_template">
                    <!-- BEGIN aff_conf_template -->
                    <optgroup label="Modèle : {TEMPLATE_NAME}">
                        <!-- BEGIN aff_conf_template_style -->
                        <option value="{TEMPLATE_NAME}|{STYLE_FILE}" {CONF_TEMPLATE_NAME}>{STYLE_FILE} &laquo; {STYLE_NAME} &raquo; </option>
                        <!-- END aff_conf_template_style -->
                    </optgroup>
                    <!-- END aff_conf_template -->
                </select>
                <p class="help">
                    Choisissez ici le modèle voulu avec sa feuille de style appropriée.
                </p>

                <label for="conf_view_toolbar">
                    Faut-il toujours voir les actions :
                </label>
                <select name="conf_view_toolbar" id="conf_view_toolbar">
                    <option value="false" {CONF_VIEW_TOOLBAR_0}>Non</option>
                    <option value="true" {CONF_VIEW_TOOLBAR_1}>Oui</option>
                </select>
                <p class="help">
                    Permet d'afficher les liens vers les actions de la barre d'outils même lorsque l'on n'est pas connecté.
                </p>

                <label for="conf_view_tree">
                    Faut-il afficher l'arborescence des dossiers :
                </label>
                <select name="conf_view_tree" id="conf_view_tree">
                    <option value="0" {CONF_VIEW_TREE_0}>Jamais</option>
                    <option value="1" {CONF_VIEW_TREE_1}>Uniquement lorsque je suis dans un dossier</option>
                    <option value="2" {CONF_VIEW_TREE_2}>Toujours</option>
                </select>
                <p class="help">
                    Ceci permet d'afficher tous les dossiers sous forme d'arborescence.
                </p>

                <label for="conf_view_hidden_file">
                    Faut-il afficher les fichiers cachés :
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
            <legend><a href="#add" onclick="swap_layer('add');">Ajout de fichiers et de dossiers</a></legend>
            <div id="add" class="jhidden">
                <label for="conf_file_chmod">
                    Droits à attribuer aux fichiers uploadés :
                </label>
                <input type="text" name="conf_file_chmod" id="conf_file_chmod" size="8" value="{FILE_CHMOD}" />
                <p class="help">
                    Il est fortement recommandé pour des raisons de sécurité évidentes de laisser la valeur par défaut (765) !
                </p>

                <label for="conf_dir_chmod">
                    Droits à attribuer aux dossiers créés :
                </label>
                <input type="text" name="conf_dir_chmod" id="conf_dir_chmod" size="8" value="{DIR_CHMOD}" />
                <p class="help">
                    Il est fortement recommandé pour des raisons de sécurité évidentes de laisser la valeur par défaut (775) !
                </p>

                <label for="conf_anon_file_send">
                    Action à réaliser lors de l'envoi d'un fichier anonyme :
                </label>
                <select name="conf_anon_file_send" id="conf_anon_file_send">
                    <option value="0" {CONF_ANON_FILE_SEND_0}>Ne rien faire</option>
                    <option value="1" {CONF_ANON_FILE_SEND_1}>Envoyer un courriel</option>
                </select>
                <p class="help">
                    Pour chaque fichier anonyme envoyé, il est possible de recevoir un courriel vous avertissant de l'ajout d'un nouveau fichier à valider.
                </p>
            </div>
        </fieldset>
        <br />
        <fieldset>
            <legend><a href="#list" onclick="swap_layer('list');">Listage de dossiers</a></legend>
            <div id="list" class="jhidden">
                <label for="conf_sort">
                    Tri par défaut :
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
                    Ce tri définit l'ordre d'affichage des fichiers et des dossiers.
                </p>

                <label for="conf_folder_first">
                    Faut-il mettre les dossiers en premier :
                </label>
                <select name="conf_folder_first" id="conf_folder_first">
                    <option value="false" {CONF_FOLDER_FIRST_0}>Non</option>
                    <option value="true" {CONF_FOLDER_FIRST_1}>Oui</option>
                </select>
                <p class="help">
                    Affiche les dossiers en premier.
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
                    suivis des fichiers commençant par la lettre B, et ainsi de suite...et cela de manière dynamique, par rapport au critère de tri.
                </p>

                <label for="conf_nbr_obj">
                    Nombre d'objets par page :
                </label>
                <input type="text" name="conf_nbr_obj" id="conf_nbr_obj" size="3" value="{NBR_OBJ}" />
                <p class="help">
                    Pour afficher tous les fichiers et dossiers, indiquez 0 mais cela est FORTEMENT non recommandé pour des raisons de charge serveur
                    dans le cas de génération de galeries, vous êtes prévenu !!!
                </p>

                <label for="conf_plugin_default_dir">
                    Plugin des dossiers par défaut :
                </label>
                <select name="conf_plugin_default_dir" id="conf_plugin_default_dir">
                    <!-- BEGIN aff_conf_plugin -->
                    <option value="{PLUGIN_NAME}" {CONF_PLUGIN_NAME}>{PLUGIN_NAME}</option>
                    <!-- END aff_conf_plugin -->
                </select>
                <p class="help">
                    Spécifiez ici le plugin par défaut à utiliser, ainsi, si vous désirez générer uniquement des galeries photos avec le plugin zenphoto, choisissez "zenphoto".
                </p>
            </div>
        </fieldset>
        <br />
        <fieldset>
            <legend><a href="#misc" onclick="swap_layer('misc');">Divers</a></legend>
            <div id="misc" class="jhidden">
                <label for="conf_webmaster_mail">
                    Courriel du webmestre :
                </label>
                <input type="text" name="conf_webmaster_mail" id="conf_webmaster_mail" size="25" value="{WEBMASTER_MAIL}" />
                <p class="help">
                    Les notifications (ajout de fichiers anonymes...) seront envoyées à cette adresse.
                </p>

                <label for="conf_lng">
                    Langue :
                </label>
                <input type="text" name="conf_lng" id="conf_lng" size="8" value="{LNG}" />
                <p class="help">
                    Assurez-vous qu'un dossier contenant les fichiers adéquates se trouvent bien dans le dossier l10n/ .
                </p>

                <label for="conf_download_counter">
                    Faut-il activer le compteur de téléchargement :
                </label>
                <select name="conf_download_counter" id="conf_download_counter">
                    <option value="no" {CONF_DOWNLOAD_COUNTER_0}>Non</option>
                    <option value="yes" {CONF_DOWNLOAD_COUNTER_1}>Oui</option>
                </select>
                <p class="help">
                     Cette option vous permet de compter les téléchargements et non les visualisations.
                </p>

                <label for="conf_time_of_redirection">
                    Le temps de redirection :
                </label>
                <input type="text" name="conf_time_of_redirection" id="conf_time_of_redirection" size="2" value="{TIME_OF_REDIRECTION}" />
                <p class="help">
                     Paramétrez ici le temps de redirection entre les pages exprimé en seconde, la valeur minimale est de 1 seconde.
                </p>

                <label for="conf_download_dir">
                    Faut-il autoriser le téléchargement de dossier :
                </label>
                <select name="conf_download_dir" id="conf_download_dir">
                    <option value="yes" {CONF_DOWNLOAD_DIR_1}>Oui</option>
                    <option value="no" {CONF_DOWNLOAD_DIR_0}>Non</option>
                </select>
                <p class="help">
                    Cette option vous permet de désactiver le téléchargement de dossier qui consiste à envoyer une archive contenant les fichiers de ce dernier.
                </p>

                <label for="conf_fs_charset_is_utf8">
                    Le système de fichier est-il encodé en Utf8 ? :
                </label>
                <select name="conf_fs_charset_is_utf8" id="conf_fs_charset_is_utf8">
                    <option value="yes" {CONF_FS_CHARSET_IS_UTF8_1}>Oui</option>
                    <option value="no" {CONF_FS_CHARSET_IS_UTF8_0}>Non</option>
                </select>
                <p class="help">
                    Si lors d'opérations (renommage, création de dossier...) sur le système de fichiers par le biais de Hyla,
                    apparaissent des caractères bizarres dans le nom des fichiers ou dossiers, modifiez cette clef
                    de manière à ce qu'elle soit à Oui pour un système de fichiers en Utf8 ou à non pour les autres types (ISO...), si
                    vous êtes utilisateur de Windows, cette clef devrait être à Non.
                </p>

                <label for="conf_register_user">
                    Autoriser les utilisateurs à créer des comptes ? :
                </label>
                <select name="conf_register_user" id="conf_register_user">
                    <option value="yes" {CONF_REGISTER_USER_1}>Oui</option>
                    <option value="no" {CONF_REGISTER_USER_0}>Non</option>
                </select>
                <p class="help">
                    Un utilisateur n'ayant pas de compte pourra alors se créer un compte tout seul.
                </p>
            </div>
        </fieldset>

        <p>
            <input type="submit" name="Submit" value="Sauvegarder" />
        </p>

    </form>
    <!-- END aff_conf -->

    <!-- BEGIN comment -->

    <h3>Les commentaires</h3>

    <p>
        <a href="{DIR_ROOT}doc/{L10N}/administration.htm#comments" onclick="popup(this.href); return false;" title="Aide contextuelle"><img src="{DIR_IMAGE}/help.png" alt="Point d'interrogation" /></a>
    </p>

    <div id="comment">

        <p>
            {MSG}
        </p>

        <form method="post" name="form_move" action="{FORM_COMMENT_DEL}">

            <!-- BEGIN comment_line -->
            <p class="comment_info">
                <input type="checkbox" value="{COMMENT_ID}" name="comment_id[]" />
                <a href="{ADMIN_DEL_COMMENT}" onclick="return confirm('Voulez-vous vraiment supprimer définitivement le commentaire ?');" title="Suppression du commentaire"><img src="{DIR_IMAGE}/delete.png" width="32" height="32" border="0" align="middle" alt="Icone" /></a>
                <a href="{PATH_INFO}"><img src="{FILE_ICON}" width="32" height="32" border="0" align="middle" alt="Infos" /></a> {PATH_FORMAT}
                {DATE} - <a href="{MAIL}">{AUTHOR}</a> <a href="{URL}">{URL}</a>
            </p>
            <blockquote class="comment_content">
                <p>
                    {COMMENT}
                </p>
            </blockquote>
            <!-- END comment_line -->

            <p>
                <input type="submit" value="Supprimer la sélection" onclick="return confirm('Voulez-vous vraiment supprimer le(s) commentaire(s) ?');" />
            </p>

        </form>
    </div>
    <!-- END comment -->

    <!-- BEGIN anon -->
    <h3>Les fichier anonymes</h3>

    <p>
        <a href="{DIR_ROOT}doc/{L10N}/administration.htm#anon-file" onclick="popup(this.href); return false;" title="Aide contextuelle"><img src="{DIR_IMAGE}/help.png" alt="Point d'interrogation" /></a>
    </p>

    <p>
        {MSG}
    </p>

    <!-- BEGIN anon_list -->
    <table class="tab" width="100%" summary="Liste des fichiers anonymes">
        <thead>
            <tr>
                <th width="4%" colspan="2">Actions</th>
                <th width="20%">Nom du fichier</th>
                <th width="18%">Destination</th>
                <th width="41%">Description</th>
                <th width="5%">Taille</th>
                <th width="10%">Date</th>
                <th width="2%">Télécharger</th>
            </tr>
        </thead>
        <tbody>
    <!-- BEGIN anon_line -->
            <tr>
                <td><a href="{ADMIN_ANON_DEL}" title="Supprimer le fichier" onclick="return confirm('Voulez-vous vraiment supprimer définitivement le fichier ?');"><img src="{DIR_IMAGE}/delete.png" width="32" height="32" border="0" align="middle" alt="Corbeille" /></a> </td>
                <td><a href="{ADMIN_ANON_MOVE}" title="Accepter le fichier"><img src="{DIR_IMAGE}/move.png" width="32" height="32" border="0" align="middle" alt="Déplacer" /></a> </td>
                <td><img src="{FILE_ICON}" class="icon" alt="Icone" /> {FILE_NAME}</td>
                <td align="left">{FILE_PATH}</td>
                <td align="right" class="description">{FILE_DESCRIPTION}</td>
                <td align="right">{FILE_SIZE}</td>
                <td align="center">{FILE_DATE}</td>
                <td align="right"><a href="{PATH_DOWNLOAD}" title="Télécharger"><img src="{DIR_IMAGE}/download.png" width="32" height="32" border="0" align="middle" alt="Télécharger" /></a></td>
            </tr>
    <!-- END anon_line -->
        </tbody>
    </table>
    <!-- END anon_list -->

    <!-- BEGIN anon_move -->
    <form method="post" name="form_move" action="{FORM_ANON_MOVE}">
        <fieldset>
            <legend><img src="{DIR_IMAGE}/move.png" align="middle" width="32" height="32" alt="Déplacer" /> Déplacer le fichier &laquo; {FILE} &raquo;  </legend>
            {ERROR}
            <p>
                <label for="mv_destination">
                    Choisissez le dossier de destination :
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

    <!-- END anon -->

    <!-- BEGIN users -->

    <h3>Les utilisateurs</h3>

    <blockquote class="info">
        Hyla vous permet de gérer plusieurs utilisateurs, ainsi, il est possible de restreindre l'accès à certain utilisateurs, ou encore autoriser des utilisateurs à ajouter, éditer, supprimer...
    </blockquote>

    <p>
        <a href="{DIR_ROOT}doc/{L10N}/administration.htm#users" onclick="popup(this.href); return false;" title="Aide contextuelle"><img src="{DIR_IMAGE}/help.png" alt="Point d'interrogation" /></a>
    </p>

    <p>
        <a href="{ADMIN_USER_ADD}">Ajouter un utilisateur</a> - <a href="{ADMIN_USER_LIST}">Liste des utilisateurs</a>
    </p>

    <!-- BEGIN users_list -->
    <table class="tab sortable" summary="Liste des utilisateurs">
        <thead>
            <tr>
                <!--<th width="5%">Id</th>-->
                <th width="60%">Nom</th>
                <th width="20%">Type</th>
                <th width="20%">Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- BEGIN users_line -->
            <tr>
                <!--<td>{USER_ID}</td>-->
                <td>{USER_NAME}</td>
                <td>{USER_TYPE}</td>
                <td align="center">
                    <a href="{ADMIN_USER_EDIT}">Éditer</a>
                    <!-- BEGIN users_line_del --> - <a href="{ADMIN_USER_DEL}" onclick="return confirm('Voulez-vous vraiment supprimer l\'utilisateur ?');">Supprimer</a><!-- END users_line_del -->
                </td>
            </tr>
            <!-- END users_line -->
        </tbody>
    </table>
    <!-- END users_list -->

    <!-- BEGIN user_edit -->
    <div id="form_misc">
        <fieldset>
            {MSG}
            <legend>Édition de l'utilisateur &laquo; {USER_NAME} &raquo; </legend>
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

            <!-- BEGIN user_edit_type -->
            <hr />

            <form method="post" name="ad_form" action="{FORM_USER_EDIT_TYPE}">

                <p>
                    <label for="ad_type">
                        Choisissez le niveau de l'utilisateur :
                    </label>
                    <select name="ad_type" id="ad_type">
                        <option value="0" {SELECT_TYPE_STANDARD}>Utilisateur standard</option>
                        <option value="1" {SELECT_TYPE_SUPERVISOR}>Superviseur</option>
                        <option value="2" {SELECT_TYPE_ADMIN}>Administrateur</option>
                    </select>
                </p>

                <p class="help">
                    Un utilisateur standard n'a aucun droit particulier ormis ceux qui lui sont attribué dans l'arborescence,
                    un superviseur possède tous les droits sauf celui d'accéder à l'administration, et, bien sûr, un administrateur possède tous les droits.
                </p>
                <input type="submit" name="Submit" value="Modifier les droits" />
            </form>
            <!-- END user_edit_type -->

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
                </p>
                <p class="help">
                    Toutes les lettres de l'alphabet sont acceptées ainsi que les chiffres,
                    le trait d'union (-) et le tiret bas (_), attention tout de même,
                    le nom doit commencer par une lettre et est limité à 32 caractères.
                </p>

                <p>
                    <label for="ad_password">Mot de passe :</label>
                    <input name="ad_password" id="ad_password" size="20" maxlength="255" value="" type="password" />
                </p>
                <p>
                    <label for="ad_password_bis">Confirmation du mot de passe :</label>
                    <input name="ad_password_bis" id="ad_password_bis" size="20" maxlength="255" value="" type="password" />
                </p>

                <hr />

                <p>
                    <label for="ad_type">
                        Choisissez le niveau de l'utilisateur :
                    </label>
                    <select name="ad_type" id="ad_type">
                        <option value="0" {SELECT_TYPE_STANDARD}>Utilisateur standard</option>
                        <option value="1" {SELECT_TYPE_SUPERVISOR}>Superviseur</option>
                        <option value="2" {SELECT_TYPE_ADMIN}>Administrateur</option>
                    </select>
                </p>

                <p class="help">
                    Un utilisateur standard n'a aucun droit particulier ormis ceux qui lui sont attribué dans l'arborescence,
                    un superviseur possède tous les droits sauf celui d'accéder à l'administration, et, bien sûr, un administrateur possède tous les droits.
                </p>

                <input type="submit" name="Submit" value="Ajouter" />
            </fieldset>
        </form>
    </div>
    <!-- END user_add -->

    <!-- END users -->

    <!-- BEGIN groups -->

    <h3>Les groupes</h3>

    <blockquote class="info">
        Afin de rendre la gestion des utilisateurs plus simple, Hyla vous permet de créer des groupes d'utilisateurs permettant ainsi une manipulation des droits beaucoup plus simple.
    </blockquote>

    <p>
        <a href="{DIR_ROOT}doc/{L10N}/administration.htm#groups" onclick="popup(this.href); return false;" title="Aide contextuelle"><img src="{DIR_IMAGE}/help.png" alt="Point d'interrogation" /></a>
    </p>

    <p>
        <a href="{ADMIN_GROUP_ADD}">Ajouter un groupe</a>
    </p>

    <p>
        {MSG}
    </p>

    <!-- BEGIN groups_list -->
    <table class="tab sortable" summary="Liste des groupes">
        <thead>
            <tr>
                <!--<th width="5%">Id</th>-->
                <th width="20%">Nom</th>
                <th width="55%">Utilisateurs appartenant au groupe</th>
                <th width="25%">Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- BEGIN groups_line -->
            <tr>
                <!--<td>{GROUP_ID}</td>-->
                <td>{GROUP_NAME}</td>
                <td>{GROUP_CONTENT}</td>
                <td align="center">
                    <a href="{ADMIN_GROUP_EDIT}">Édition d'utilisateur(s)</a> -
                    <a href="{ADMIN_GROUP_DEL}" onclick="return confirm('Voulez-vous vraiment supprimer le groupe ?');">Supprimer le groupe</a>
                </td>
            </tr>
            <!-- END groups_line -->
        </tbody>
    </table>
    <!-- END groups_list -->

    <!-- BEGIN group_add -->
    <div id="form_misc">
        <form method="post" name="ad_form" action="{FORM_GROUP_SAVE}">
            <fieldset>
                {ERROR}
                <legend>Ajout d'un groupe</legend>
                <p>
                    <label for="ad_login">Nom :</label>
                    <input name="ad_login" id="ad_login" size="20" maxlength="32" value="{NAME}" type="text" />
                </p>
                <p class="help">
                    Toutes les lettres de l'alphabet sont acceptées ainsi que les chiffres,
                    le trait d'union (-) et l'underscore (_), attention tout de même,
                    le nom doit commencer par une lettre et est limité à 32 caractères.
                </p>
                <input type="submit" name="Submit" value="Ajouter" />
            </fieldset>
        </form>
    </div>
    <!-- END group_add -->

    <!-- BEGIN group_edit -->
    <div id="form_misc">
        <fieldset>
            <legend>Édition du groupe &laquo; {GROUP_NAME} &raquo; </legend>
            <!-- BEGIN groupe_edit_view -->
            <form method="post" name="ad_form_edit" action="{FORM_GROUP_EDIT_DEL}">
                <p>
                    Utilisateur(s) présent dans le groupe :
                </p>

                <table class="tab" summary="Liste des utilisateurs présent dans le groupe courant">
                    <tr>
                        <th>Nom</th>
                        <th width="20%">Sélection</th>
                    </tr>
                    <!-- BEGIN group_edit_line -->
                    <tr>
                        <td>{GROUP_NAME}</td>
                        <td align="center"><input type="checkbox" name="ad_del_users[]" id="ad_del_users[]" value="{GROUP_ID}" /></td>
                    </tr>
                    <!-- END group_edit_line -->
                </table>
                <p>
                    <input type="submit" name="Submit" onclick="return confirm('Voulez-vous vraiment supprimer le ou les utilisateur(s) du groupe ?');" value="Supprimer la sélection" />
                </p>
            </form>
            <hr />
            <!-- END groupe_edit_view -->

            <form method="post" name="ad_form_edit" action="{FORM_GROUP_EDIT_ADD}">
                <p>
                    Choisir un ou plusieurs utilisateurs afin de les ajouter :
                </p>
                <select name="ad_add_users[]" multiple="multiple">
                    <!-- BEGIN group_edit_add_user -->
                    <option value="{USER_ID}">{USER_NAME}</option>
                    <!-- END group_edit_add_user -->
                </select>
                <p>
                    <input type="submit" name="Submit" value="Ajouter la sélection" />
                </p>
            </form>

        </fieldset>
    </div>
    <!-- END group_edit -->

    <!-- END groups -->

    <!-- BEGIN rights -->

    <h3>Les droits</h3>

    <blockquote class="info">
        Les droits vont vous permettre de restreindre l'accès à certains dossiers ou actions au sein de dossiers.
    </blockquote>

    <p>
        <a href="{DIR_ROOT}doc/{L10N}/administration.htm#rights" onclick="popup(this.href); return false;" title="Aide contextuelle"><img src="{DIR_IMAGE}/help.png" alt="Point d'interrogation" /></a>
    </p>

    <p>
        <a href="{URL_RIGHTS_ADD}">Ajouter des droits</a> - <a href="{URL_RIGHTS_LIST}">Liste des droits par utilisateur</a>
    </p>

    <p>
        {MSG}
    </p>

    <!-- BEGIN rights_error -->
    <p class="error">
        Des erreurs ont été trouvées dans les droits, les utilisateurs apparaissant en rouge ne pourront visualiser les dossiers.
        Hyla peut corriger les erreurs automatiquement pour vous si vous <a href="{URL_RIGHTS_REPAIR}">cliquez ici</a>.
    </p>
    <!-- END rights_error -->

    <!-- BEGIN rights_list_tab -->
    <form method="post" name="form_move" action="{FORM_RIGHT_DEL}">
        <table class="tab" summary="Liste des droits">
            <tr>
                <th>Dossiers</th>
                <th>Utilisateurs / Groupes</th>
                <th>Droits</th>
                <th>Actions</th>
                <th>Sélection</th>
            </tr>
            <!-- BEGIN rights_list -->
            <!-- BEGIN rights_list_header -->
            <tr>
                <td colspan="3" class="header"><a href="{URL_OBJ}">{OBJ_NAME}</a></td>
                <td class="header" width="5%" align="center">
                    <a href="{URL_RIGHTS_ADD}">Ajouter</a>
                </td>
                <td class="header" align="center"> </td>
            </tr>
            <!-- END rights_list_header -->
            <!-- BEGIN rights_list_line -->
            <tr<!-- BEGIN rights_list_line_error --> style="color: red;"<!-- END rights_list_line_error -->>
                <td style="border-bottom: 0px; border-top: 0px;" width="15%">&nbsp;</td>
                <td style="border-bottom: 0px; border-top: 0px;" width="13%">{USER_NAME}</td>
                <td style="border-bottom: 0px; border-top: 0px; font-size: 80%;">{RIGHTS}</td>
                <td style="border-bottom: 0px; border-top: 0px;" width="5%" align="center">
                    <a href="{URL_RIGHTS_EDIT}">Éditer</a>
                    <!--<a href="{URL_RIGHTS_DEL}" onclick="return confirm('Voulez-vous vraiment supprimer le droit ?');">Supprimer</a>-->
                </td>
                <td style="border-bottom: 0px; border-top: 0px;" width="2%" align="center"><input type="checkbox" value="{RIGHT_ID}" name="right_id[]" /></td>
            </tr>
            <!-- END rights_list_line -->
            <!-- END rights_list -->
        </table>

        <p>
            <input type="submit" value="Supprimer la sélection" onclick="return confirm('Voulez-vous vraiment supprimer le(s) droit(s) ?');" />
        </p>

    </form>
    <!-- END rights_list_tab -->

    <!-- BEGIN rights_add_1 -->
    <form method="post" name="ad_form" action="{FORM_RIGHTS_ADD}">
        <fieldset>
            {ERROR}
            <legend>Ajout d'un droit (étape 1 / 2)</legend>
            <p>
                <label for="rgt_dir">
                    Choisissez le dossier concerné :
                </label>
                <br />
                <select name="rgt_dir" id="rgt_dir">
                    <option value="/">/ (la racine)</option>
                    <!-- BEGIN rights_add_1_dir_occ --><option value="{DIR_NAME}">{DIR_NAME}</option><!-- END rights_add_1_dir_occ -->
                </select>
            </p>
            <input type="submit" name="Submit" value="Étape suivante" />
        </fieldset>
    </form>
    <!-- END rights_add_1 -->

    <!-- BEGIN rights_add_2 -->
    <form method="post" name="ad_form" action="{FORM_RIGHTS_ADD}">
        <fieldset>
            {ERROR}
            <legend>Ajout d'un droit dans le dossier &laquo; {OBJECT} &raquo;  (étape 2 / 2)</legend>
            <p>
                Sélectionnez le ou les utilisateur(s) ou groupe(s) :
            </p>
            <p class="info">
                Les groupes apparaissent entre crochets ( [groupe] ).
                <br />
                Sélectionnez plusieurs utilisateurs / groupes en maintenant la touche [CTRL] appuyé, vous pouvez aussi cliquer sur un élément
                et glisser vers le haut ou le bas.
            </p>
            <select name="rgt_users[]" multiple="multiple">
                <!-- BEGIN rights_add_2_user -->
                <option value="{USER_ID}">{USER_NAME}</option>
                <!-- END rights_add_2_user -->
            </select>
            <p>
                Et attribuez lui les droits voulus :
            </p>
            <p class="info">
                Si le droit de "Visualisation" est décoché, il n'est pas possible d'accéder au contenu du dossier donc impossible d'accéder aux actions (édition, copie...).
            </p>
            <p>
                <input type="checkbox" name="rgt_value[]" value="1" id="rgt_value_view" checked="checked" onclick="test('rgt_value_view', 'rgt_value_multiple');" />
                <label for="rgt_value_view">
                    Visualisation <span class="help">(si ce droit n'est pas présent, le dossier et son contenu seront cachés)</span>
                </label>
            </p>
            <select name="rgt_value[]" id="rgt_value_multiple" size="11" multiple="multiple">
                <!-- BEGIN rights_add_2_right -->
                <option value="{RIGHT_VALUE}">{RIGHT_NAME}</option>
                <!-- END rights_add_2_right -->
            </select>
            <p>
                <input type="hidden" name="rgt_dir" value="{OBJECT}" />
                <input type="submit" name="Submit" value="Ajouter" />
            </p>
        </fieldset>
    </form>
    <!-- END rights_add_2 -->

    <!-- BEGIN rights_edit -->
    <form method="post" name="ad_form" action="{FORM_RIGHTS_EDIT}">

        <fieldset>
            <legend>Édition</legend>

            <p>
                Édition des droits de l'utilisateur &laquo; <b>{USER_NAME}</b> &raquo; pour le dossier &laquo; <b>{CURRENT_DIR}</b> &raquo; :
            </p>
            <p>
                <input type="checkbox" name="rgt_value[]" value="1" id="rgt_value_view" <!-- BEGIN rights_edit_selected_view --> checked="checked"<!-- END rights_edit_selected_view --> onclick="test('rgt_value_view', 'rgt_value_multiple');" />
                <label for="rgt_value_view">
                    Visualisation <span class="help">(si ce droit n'est pas présent, le dossier et son contenu seront cachés)</span>
                </label>
            </p>

            <select name="rgt_value[]" id="rgt_value_multiple" size="11" multiple="multiple" <!-- BEGIN rights_edit_disabled_multiple -->disabled="disabled"<!-- END rights_edit_disabled_multiple -->>
                <!-- BEGIN rights_edit_right -->
                <option value="{RIGHT_VALUE}" <!-- BEGIN rights_edit_right_selected --> selected="selected"<!-- END rights_edit_right_selected -->>{RIGHT_NAME}</option>
                <!-- END rights_edit_right -->
            </select>
            <p>
                <input type="submit" name="Submit" value="Modifier" />
            </p>
            <p class="info">
                Les droits que vous allez attribuer à l'utilisateur dans le dossier courant seront récursifs.
            </p>

        </fieldset>
    </form>
    <!-- END rights_edit -->

    <!-- END rights -->

    <!-- BEGIN maintenance -->
    <h3>Maintenance</h3>

    <p>
        <a href="{DIR_ROOT}doc/{L10N}/administration.htm#maintenance" onclick="popup(this.href); return false;" title="Aide contextuelle"><img src="{DIR_IMAGE}/help.png" alt="Point d'interrogation" /></a>
    </p>

    <div id="form_misc">
        <ul>
            <li>
                <a href="{ADMIN_PAGE_MAINTENANCE_PURGE}">Vider le cache</a>
                <p class="help">
                    Supprime tous les fichiers "cache", utile si des erreurs de lecture d'archives se produisent.
                </p>
                <p>
                    {PURGE_RAPPORT}
                </p>
            </li>
            <li>
                <a href="{ADMIN_PAGE_MAINTENANCE_SYNC}">Lancer une synchronisation</a>
                <p class="help">
                    Si vous supprimez des fichiers ou des dossiers sans passer par l'interface de Hyla (ftp...), il peut être utile d'effectuer une synchronisation
                    de la base de données avec le système de fichiers.
                </p>
                <p>
                    {SYNC_RAPPORT}
                </p>
            </li>
        </ul>

    </div>
    <!-- END maintenance -->
