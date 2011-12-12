  
    <!-- BEGIN block_groups -->

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

    <!-- END block_groups -->
