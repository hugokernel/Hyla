
    <p>
        <a href="http://www.hyla-project.org/"><img src="{DIR_ROOT}/img/hyla.png" alt="Une rainette verte" /></a>
    </p>

    <h3>Hyla {HYLA_VERSION}, copyright (c) 2004-2007 Charles Rincheval</h3>

    <p>
        Il est important de toujours avoir la dernière version d'Hyla pour palier à d'éventuels problèmes de sécurité et bien sûr disposer des dernières nouveautés !

        <!-- BEGIN test_version -->
        <br />
        Testez si vous avez la dernière version en cliquant <a href="{URL_TEST_VERSION}">sur ce lien</a>.
    </p>
    <p>
        <strong>{STATUS_VERSION}</strong>
        <!-- END test_version -->
    </p>

    {MSG_ERROR}

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

