
<fieldset>
    <legend>{LANG:Password modification}</legend>

    {MSG}

    <form method="post" name="ad_form" action="{FORM_USER_CHANGER_PASSWORD}">
        <p>
            <label for="user_password">{LANG:Enter new password :}</label>
            <input name="user_password" id="user_password" size="20" maxlength="255" type="password" />
        </p>
        <p>
            <label for="user_password_bis">{LANG:Confirm password :}</label>
            <input name="user_password_bis" id="user_password_bis" size="20" maxlength="255" type="password" />
        </p>
        <input type="submit" name="Submit" value="{LANG:Validate}" />
    </form>
</fieldset>

