
    <p>
        <a href="{DIR_ROOT}doc/{L10N}/administration.htm#anon-file" onclick="popup(this.href); return false;" title="Aide contextuelle"><img src="{DIR_IMAGE}/help.png" alt="Point d'interrogation" /></a>
    </p>

    <p>
        {MSG}
    </p>

    <div style="margin: 0 5% 0 5%;">
        {CONTENT}
    </div>

    <!-- BEGIN anon_list -->
    <table class="tab" width="100%" summary="Liste des fichiers anonymes">
        <tr>
            <th width="4%" colspan="2">Actions</th>
            <th width="20%">Nom du fichier</th>
            <th width="18%">Destination</th>
            <th width="41%">Description</th>
            <th width="5%">Taille</th>
            <th width="10%">Date</th>
            <th width="2%">Télécharger</th>
        </tr>
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

