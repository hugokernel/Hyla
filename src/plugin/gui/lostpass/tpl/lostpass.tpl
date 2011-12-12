
<div style="margin: 5% 30% 0 30%">

    <!-- BEGIN first_step -->
    <form method="post" name="auth" id="auth" action="{URL_LOST_PASS}">
        <fieldset>
            <legend><img src="{PATH_2_PLUGIN}/icon.png" align="middle" width="32" height="32" alt="Auth" /> {LANG:Lost password} (1 / 2)</legend>

            {ERROR}

            <p class="center">
                {LANG:Please enter your username and e-mail address then click on the Send button.}
            </p>

            <p>
                <label for="user_name">{LANG:Name} :</label>
                <input name="user_name" id="user_name" size="15" maxlength="32" value="{NAME}" type="text" />
            </p>

            <p>
                <label for="user_email">{LANG:Email} :</label>
                <input name="user_email" id="user_email" size="30" maxlength="128" type="text" />
            </p>

            <p>
                <label>&nbsp;</label>
                <input type="submit" value="{LANG:Send}" />
            </p>

            <p class="center">
                {LANG:You will receive a new password shortly. Use this new password to access the site.}
            </p>

        </fieldset>
    </form>
    <!-- END first_step -->

    <!-- BEGIN final_step -->
    <form method="post" action="{URL_PASSWORD_MODIFY}">
        <fieldset>
            <legend><img src="{PATH_2_PLUGIN}/icon.png" align="middle" width="32" height="32" alt="Auth" /> {LANG:Lost password} (2 / 2)</legend>

                {ERROR}

                <p class="center">
                    {LANG:Hello} {USER_NAME},
                    <br />
                    {LANG:Please, enter your new password.}
                </p>

                <p>
                    <label for="user_password">{LANG:New password :}</label>
                    <input name="user_password" id="user_password" size="20" maxlength="255" type="password" />
                </p>

                <p>
                    <label for="user_password_bis">{LANG:Confirm password :}</label>
                    <input name="user_password_bis" id="user_password_bis" size="20" maxlength="255" type="password" />
                </p>

                <p>
                    <label>&nbsp;</label>
                    <input type="hidden" name="action" value="modify" />
                    <input type="submit" value="{LANG:Send}" />
                </p>

        </fieldset>
    </form>
    <!-- END final_step -->

</div>

