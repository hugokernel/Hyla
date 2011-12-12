
<script language="javascript" type="text/javascript">
//<!--
$(document).ready(function() {
    document.auth.user_name.focus();

    $("#auth").submit(function() {

        $.getJSON($.hyla.ws.url('hyla.user.auth'), {
            'username' : $('#user_name').val(),
            'password' : $('#user_password').val()
        }, function (json) {
            if (!$.hyla.testIfError(json)) {
                document.auth.submit();
            }
        });

        return false;
    });

});
//-->
</script>

<div style="margin: 5% 30% 0 30%">

    <form method="post" name="auth" id="auth" action="{URL_LOGIN}">
        <fieldset>
            <legend><img src="{DIR_IMAGE}/login.png" align="middle" width="32" height="32" alt="Auth" /> {LANG:Authentication}</legend>

            <span id="error_test">
            {ERROR}
            </span>

            <p>
                <label for="user_name">{LANG:Name} :</label>
                <input name="user_name" id="user_name" size="15" maxlength="32" value="{NAME}" type="text" />
            </p>

            <p>
                <label for="user_password">{LANG:Password} :</label>
                <input name="user_password" id="user_password" size="15" maxlength="32" type="password" />
            </p>

            <p>
                <label>&nbsp;</label>
                <input type="submit" value="{LANG:Send}" />
            </p>

            <blockquote class="info">
                {LANG:Cookies are required to log on the site, they allow identifying data about your session.}
            </blockquote>

            <!-- BEGIN register -->
            <p class="center">
                <a href="{PAGE_REGISTER}">{LANG:Create an account}</a>
            </p>
            <!-- END register -->

            <p class="center">
                <a href="{PAGE_LOST_PASSWORD}">{LANG:Lost your password ?}</a>
            </p>

        </fieldset>
    </form>

    {SUGGESTION}

</div>

