<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">

<head>

<title>{LANG:Register}</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="robots" content="noindex,nofollow" />

{STYLESHEET}

<link rel="icon" type="image/png" href="{DIR_ROOT}img/icon.png" />
<link rel="shortcut icon" href="{DIR_ROOT}img/icon.ico"/>

<script language="javascript" type="text/javascript" src="{DIR_TEMPLATE}/styleswitcher.js"></script>

</head>

<body>

<div style="margin: 5% 30% 0 30%">

    <form method="post" name="form_upload" action="{PAGE_REGISTER}">
        <fieldset>
            <legend><img src="{DIR_IMAGE}/register.png" align="middle" width="32" height="32" alt="Auth" /> {LANG:Register}</legend>
            {ERROR}
            <p>
                <label for="reg_name">{LANG:Name} :</label>
                <input name="reg_name" id="reg_name" size="15" maxlength="32" value="{NAME}" type="text" />
            </p>

            <p>
                <label for="reg_password">{LANG:Password} :</label>
                <input name="reg_password" id="reg_password" size="15" maxlength="32" type="password" />
            </p>

            <p>
                <label for="reg_password_confirm">{LANG:Password Confirmation} :</label>
                <input name="reg_password_confirm" id="reg_password_confirm" size="15" maxlength="32" type="password" />
            </p>

            <input type="submit" name="Submit" value="{LANG:Send}" />

            <blockquote class="info">
                Une fois votre compte créé, l'accès aux documents peut nécessiter qu'un administrateur vous attribue des droits particuliers.
                Il est possible que vous ne puissiez pas immédiatement accéder au contenu.
            </blockquote>

        </fieldset>
    </form>

</div>

</body>

</html>
