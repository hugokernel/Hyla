<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">

<head>

<title>{LANG:Authentication}</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="robots" content="noindex,nofollow" />

{STYLESHEET}

<link rel="icon" type="image/png" href="{DIR_ROOT}img/icon.png" />
<link rel="shortcut icon" href="{DIR_ROOT}img/icon.ico"/>

<script language="javascript" type="text/javascript" src="{DIR_TEMPLATE}/js/styleswitcher.js"></script>

</head>

<body onload="document.form_upload.lg_name.focus();">

<div style="margin: 5% 30% 0 30%">

    <form method="post" name="form_upload" action="{PAGE_LOGIN}">
        <fieldset>
            <legend><img src="{DIR_IMAGE}/login.png" align="middle" width="32" height="32" alt="Auth" /> {LANG:Authentication}</legend>
           {ERROR}
            <p>
                <label for="lg_name">{LANG:Name} :</label>
                <input name="lg_name" id="lg_name" size="15" maxlength="32" value="{NAME}" type="text" />
            </p>

            <p>
                <label for="lg_password">{LANG:Password} :</label>
                <input name="lg_password" id="lg_password" size="15" maxlength="32" type="password" />
            </p>

            <input type="submit" name="Submit" value="{LANG:Send}" />

            <blockquote class="info">
                Les cookies sont nécessaires pour vous connecter sur le site, ils permettent d'identifier les données relatives
                à votre session.
            </blockquote>

            <!-- BEGIN register -->
            <a href="{PAGE_REGISTER}">Créer un compte utilisateur</a>
            <!-- END register -->

        </fieldset>
    </form>

    {SUGGESTION}

</div>

</body>

</html>
