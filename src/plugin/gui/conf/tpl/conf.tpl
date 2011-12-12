
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
                <select name="conf_template" id="conf_template" onchange="alert(getID('paf').style.display);">
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
