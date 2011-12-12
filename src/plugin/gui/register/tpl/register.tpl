
<div style="margin: 5% 20% 0 20%">

    <form method="post" name="form_upload" action="{URL_REGISTER}">
        <fieldset>
            <legend><img src="{DIR_IMAGE}/register.png" align="middle" width="32" height="32" alt="Auth" /> {LANG:Register}</legend>

            {ERROR}

            <p>
                <label for="user_name">{LANG:Name} :</label>
                <input name="user_name" id="user_name" size="15" maxlength="32" value="{NAME}" type="text" />
            </p>

            <p>
                <label for="user_email">{LANG:Email} :</label>
                <input name="user_email" id="user_email" size="30" maxlength="32" value="{EMAIL}" type="text" />
            </p>

            <p>
                <label for="user_password">{LANG:Password} :</label>
                <input name="user_password" id="user_password" size="15" maxlength="32" type="password" />
            </p>

            <p>
                <label for="user_password_bis">{LANG:Password confirmation} :</label>
                <input name="user_password_bis" id="user_password_bis" size="15" maxlength="32" type="password" />
            </p>

            <p>
                <label>&nbsp;</label>
                <input type="submit" name="Submit" value="{LANG:Create}" />
            </p>

            <blockquote class="info">
                Une fois votre compte créé, l'accès aux documents peut nécessiter qu'un administrateur vous attribue des droits particuliers.
                Il est possible que vous ne puissiez pas immédiatement accéder au contenu.
            </blockquote>

        </fieldset>
    </form>

</div>
