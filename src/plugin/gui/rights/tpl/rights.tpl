
    <!-- BEGIN block_rights -->

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

    <!-- END block_rights -->
