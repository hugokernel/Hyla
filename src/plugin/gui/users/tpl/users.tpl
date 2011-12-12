

    <!-- BEGIN block_users -->
 <div id="tabs_gui_content_container">
    <h3>Les utilisateurs</h3>

    <blockquote class="info">
        Hyla vous permet de gérer plusieurs utilisateurs, ainsi, il est possible de restreindre l'accès à certain utilisateurs, ou encore autoriser des utilisateurs à ajouter, éditer, supprimer...
    </blockquote>

    <p>
        <a href="{DIR_ROOT}doc/{L10N}/administration.htm#users" onclick="popup(this.href); return false;" title="Aide contextuelle"><img src="{DIR_IMAGE}/help.png" alt="Point d'interrogation" /></a>
    </p>

 <ul class="tabs_gui_content">
        <li><a href="{ADMIN_USER_ADD}">{LANG:Add User}</a></li> 
        <li><a href="{ADMIN_USER_LIST}">{LANG:User List}</a></li>
 </ul>
    <!-- BEGIN users_list -->
    <table class="tab sortable" summary="{LANG:User List}">
        <thead>
            <tr>
                <!--<th width="5%">Id</th>-->
                <th width="50%">{LANG:Name}</th>
                <th width="20%">{LANG:Email}</th>
                <th width="15%">{LANG:Type}</th>
                <th width="15%">{LANG:Action}</th>
            </tr>
        </thead>
        <tbody>
            <!-- BEGIN users_line -->
            <tr>
                <!--<td>{USER_ID}</td>-->
                <td>{USER_NAME}</td>
                <td>{USER_EMAIL}</td>
                <td>{USER_TYPE}</td>
                <td align="center">
                    <a href="{ADMIN_USER_EDIT}">{LANG:Edit}</a>
                    <!-- BEGIN users_line_del --> - <a href="{ADMIN_USER_DEL}" onclick="return confirm('Voulez-vous vraiment supprimer l\'utilisateur ?');">{LANG:Delete}</a><!-- END users_line_del -->
                </td>
            </tr>
            <!-- END users_line -->
        </tbody>
    </table>
    <!-- END users_list -->

    <!-- BEGIN user_edit -->
    <div id="form_misc">
        <fieldset>
            
            <legend align="right" >{LANG:Modify User Properties} &laquo; {USER_NAME} &raquo; </legend>
            {MSG}
            <!-- BEGIN user_edit_password -->
            <form method="post" name="ad_form" action="{FORM_USER_EDIT_PASSWORD}">
                <p>
                    <label for="ad_password">{LANG:Password} :</label>
                    <input name="ad_password" id="ad_password" size="20" maxlength="255" value="{PASSWORD}" type="password" />
                </p>
                <p>
                    <label for="ad_password_bis">{LANG:Verify password} :</label>
                    <input name="ad_password_bis" id="ad_password_bis" size="20" maxlength="255" value="{PASSWORD_BIS}" type="password" />
                </p>
                <input type="submit" name="Submit" value="{LANG:Change Password}" />
            </form>
            <!-- END user_edit_password -->
            
            <!-- BEGIN user_edit_email -->
            <hr />
            <form method="post" name="ad_form" action="{FORM_USER_EDIT_EMAIL}">
                <p>
                    <label for="ad_email">{LANG:Modify email address} :</label>
                    <input name="ad_email" id="ad_email" size="20" maxlength="255" value="{EMAIL}" type="text" />
                </p>
                <input type="submit" name="Submit" value="{LANG:Change email}" />
            </form>
            <!-- END user_edit_email -->
            
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
                <input type="submit" name="Submit" value="{LANG:Change right}" />
            </form>
            <!-- END user_edit_type -->

        </fieldset>
    </div>
    <!-- END user_edit -->

    <!-- BEGIN user_add -->
    <div id="form_misc">
        <form method="post" name="ad_form" action="{FORM_USER_SAVE}">
            <fieldset>
                <legend align="right" >{LANG:Add new user}</legend>
                 {ERROR}
                <p class="help">
                    Toutes les lettres de l'alphabet sont acceptées ainsi que les chiffres,
                    le trait d'union (-) et le tiret bas (_), attention tout de même,
                    le nom doit commencer par une lettre et est limité à 32 caractères.
                </p>
                <p>
                    <label for="ad_login">{LANG:Name} :</label>
                    <input name="ad_login" id="ad_login" size="20" maxlength="32" value="{NAME}" type="text" />
                </p>
                
                <p>
                    <label for="ad_email">{LANG:Email} :</label>
                    <input name="ad_email" id="ad_email" size="20" maxlength="255" value="{EMAIL}" type="text" />
                </p>

                <p>
                    <label for="ad_password">{LANG:Password} :</label>
                    <input name="ad_password" id="ad_password" size="20" maxlength="255" value="" type="password" />
                </p>
                <p>
                    <label for="ad_password_bis">{LANG:Verify password} :</label>
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

                <input type="submit" name="Submit" value="{LANG:Add}" />
            </fieldset>
        </form>
    </div>
    <!-- END user_add -->
</div>
    <!-- END block_users -->
