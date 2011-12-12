
<h2>{LANG:Account}</h2>

<fieldset>
    <legend>{LANG:Email modification}</legend>

    {MSG}

    <h1>ToDO</h1>

    <form method="post" action="{URL_CHANGE_EMAIL}">
        <p>
            <label for="user_password_current">{LANG:Current password :}</label>
            <input name="user_password_current" id="user_password_current" size="20" maxlength="255" type="password" />
        </p>

        <hr />

        <p>
            <label for="user_email">{LANG:New email :}</label>
            <input name="user_email" id="user_email" size="20" />
        </p>

        <p class="center">
            <input type="submit" value="{LANG:Validate}" />
        </p>

    </form>
</fieldset>

<fieldset>
    <legend>{LANG:Password modification}</legend>

    {MSG}

    <form method="post" action="{URL_CHANGE_PASSWORD}">
        <p>
            <label for="user_password_current">{LANG:Current password :}</label>
            <input name="user_password_current" id="user_password_current" size="20" maxlength="255" type="password" />
        </p>

        <hr />

        <p>
            <label for="user_password">{LANG:New password :}</label>
            <input name="user_password" id="user_password" size="20" maxlength="255" type="password" />
        </p>

        <p>
            <label for="user_password_bis">{LANG:Confirm password :}</label>
            <input name="user_password_bis" id="user_password_bis" size="20" maxlength="255" type="password" />
        </p>

        <p class="center">
            <input type="submit" value="{LANG:Validate}" />
        </p>

    </form>
</fieldset>

