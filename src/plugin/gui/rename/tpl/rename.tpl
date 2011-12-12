
<div class="action">

    <form method="post" name="form_rename" action="{FORM_RENAME}">
        <fieldset>
            <legend><img src="{DIR_IMAGE}/rename.png" align="middle" width="32" height="32" alt="Renommage" /> Renommer </legend>
            {ERROR}
            <p>
                <label for="new_name">
                    Nouveau nom :
                </label>
                <input type="text" name="new_name" id="new_name" size="20" value="{CURRENT_NAME}" />
            </p>
            <p>
                <label for="rn_redirect">
                    Être redirigé vers l'objet :
                </label>
                <input type="checkbox" name="redirect" id="rn_redirect" value="1" checked="checked" />
            </p>
            <input type="submit" value="Renommer" />
        </fieldset>
    </form>

</div>
