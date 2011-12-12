
<div class="action">

<!-- BEGIN mkdir -->
    <form method="post" name="form_mkdir" action="{FORM_MKDIR}">
        <fieldset>
            <legend><img src="{DIR_IMAGE}/mkdir.png" align="middle" width="32" height="32" alt="Création de dossier" /> Création d'un dossier </legend>
            {ERROR}
            <p>
                Le dossier sera créé dans &laquo; {OBJECT} &raquo;
            </p>
            <p>
                <label for="mk_name">
                    Nom :
                </label>
                <input type="text" name="mk_name" id="mk_name" size="20" />
            </p>
            <p>
                Une fois créé, être redirigé vers :
            </p>
            <p>
                <label for="mk_redirect_new">
                    Le nouveau dossier :
                </label>
                <input type="radio" name="mk_redirect" id="mk_redirect_new" value="new" checked="checked" />
            </p>
            <!-- BEGIN mkdir_redirect_edit -->
            <p>
                <label for="mk_redirect_edit">
                    L'édition du nouveau dossier créé :
                </label>
                <input type="radio" name="mk_redirect" id="mk_redirect_edit" value="edit" />
            </p>
            <!-- END mkdir_redirect_edit -->
            <p>
                <label for="mk_redirect_parent">
                    Le dossier courant ( {PARENT_DIR} ) :
                </label>
                <input type="radio" name="mk_redirect" id="mk_redirect_parent" value="parent" />
            </p>
            <input type="submit" name="Submit" value="Créer" />
        </fieldset>
    </form>
<!-- END mkdir -->

<!-- BEGIN rename -->
    <form method="post" name="form_rename" action="{FORM_RENAME}">
        <fieldset>
            <legend><img src="{DIR_IMAGE}/rename.png" align="middle" width="32" height="32" alt="Renommage" /> Renommer </legend>
            {ERROR}
            <p>
                <label for="rn_newname">
                    Nouveau nom :
                </label>
                <input type="text" name="rn_newname" id="rn_newname" size="20" value="{CURRENT_NAME}" />
            </p>
            <p>
                <label for="rn_redirect">
                    Être redirigé vers l'objet :
                </label>
                <input type="checkbox" name="rn_redirect" id="rn_redirect" value="1" checked="checked" />
            </p>
            <input type="submit" name="Submit" value="Renommer" />
        </fieldset>
    </form>
<!-- END rename -->

<!-- BEGIN move -->
    <form method="post" name="form_move" action="{FORM_ACTION}">
        <fieldset>
            <legend><img src="{DIR_IMAGE}/move.png" align="middle" width="32" height="32" alt="Déplacer" /> Déplacer </legend>
            {ERROR}
            <p>
                <label for="mv_destination">
                    Choisissez le dossier de destination :
                </label>
                <select name="mv_destination" id="mv_destination">
                    <!-- BEGIN dir_move_occ --><option value="{DIR_NAME}">{DIR_NAME}</option><!-- END dir_move_occ -->
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
<!-- END move -->

<!-- BEGIN copy -->
    <form method="post" name="form_copy" action="{FORM_ACTION}">
        <fieldset>
            <legend><img src="{DIR_IMAGE}/copy.png" align="middle" width="32" height="32" alt="Copier" /> Copier </legend>
            {ERROR}
            <p>
                <label for="cp_destination">
                    Choisissez le dossier de destination :
                </label>
                <select name="cp_destination" id="cp_destination">
                    <!-- BEGIN dir_copy_occ --><option value="{DIR_NAME}">{DIR_NAME}</option><!-- END dir_copy_occ -->
                </select>
            </p>
            <p>
                <label for="cp_redirect">
                    Être redirigé vers le nouvel objet copié :
                </label>
                <input type="checkbox" name="cp_redirect" id="cp_redirect" value="1" checked="checked" />
            </p>
            <input type="submit" name="Submit" value="Copier" />
        </fieldset>
    </form>
<!-- END copy -->

</div>
