{MSG}

<p>
    Nombre de fichiers à ajouter :
    <select name="sort" onchange="location.href='{URL_UPLOAD}&amp;file=' + this.options[this.selectedIndex].value + '#upload_0'">
        <option value="1" selected="selected">&nbsp;1</option>
        <option value="2">&nbsp;2</option>
        <option value="3">&nbsp;3</option>
        <option value="4">&nbsp;4</option>
        <option value="5">&nbsp;5</option>
        <option value="6">&nbsp;6</option>
        <option value="7">&nbsp;7</option>
        <option value="8">&nbsp;8</option>
        <option value="9">&nbsp;9</option>
        <option value="10">&nbsp;10</option>
    </select>
    (Taille maximale acceptée par le serveur : {MAX_FILESIZE})
</p>


<form enctype="multipart/form-data" method="post" name="form_upload" action="{URL_UPLOAD}">

    {STATUS}

    <!-- BEGIN form_upload -->
    <fieldset>
        <legend><a href="#" onclick="swap_layer('upload_{NUM}');"><img src="{DIR_IMAGE}/upload.png" align="middle" width="32" height="32" alt="Disquette" /> Ajout de fichier <strong>({NUM_HUMAN})</strong></a></legend>

        {ERROR}

        <div id="upload_{NUM}" class="jhidden">
        <p>
            <input type="radio" name="ul_file_method[{NUM}]" value="local" id="ul_url_method_{NUM}" {LOCAL_CHECKED} />
            <label for="ul_url_method_{NUM}">
                <img src="{DIR_IMAGE}/upload-local-file.png" align="middle" width="32" height="32" alt="Un disque dûr" /> Fichier local :
            </label>
            <input type="file" name="ul_file_local[]" size="40" onclick="eval(getID('ul_url_method_{NUM}') + '.checked = true;');" />
        </p>
        <!-- BEGIN from_url -->
        <p>
            <input type="radio" name="ul_file_method[{NUM}]" value="fromurl" id="ul_file_method_{NUM}" {FROM_URL_CHECKED} />
            <label for="ul_file_method_{NUM}">
                <img src="{DIR_IMAGE}/download-url-file.png" align="middle" width="32" height="32" alt="Une planète" /> Fichier distant :
            </label>
            <input type="text" name="ul_file_fromurl[]" size="40" onclick="eval(getID('ul_file_method_{NUM}') + '.checked = true;');" value="{FROM_URL}" />
        </p>
        <!-- END from_url -->
        <p>
            <label for="ul_description_{NUM}">Description :</label>
            <textarea name="ul_description[]" id="ul_description_{NUM}" cols="50" rows="5">{FILE_DESCRIPTION}</textarea>
        </p>
        <p>
            <label for="ul_name_{NUM}">Nom :</label>
            <input type="text" name="ul_new_name[]" id="ul_name_{NUM}" size="20" value="{NEW_NAME}" /> (laissez vide pour garder le nom original)
        </p>
        </div>
    </fieldset>
    <br />
    <!-- END form_upload -->

    <input type="submit" name="Submit" value="Envoyer" />

</form>

<div class="info">
    <p>
        Il est possible d'envoyer plusieurs fichiers en une seule fois en les mettant dans une archive (gzip, tar, zip...), vous n'aurez plus alors qu'à ouvrir
        cette dernière et extraire les fichiers grâce au lien adéquat.
    </p>
</div>
