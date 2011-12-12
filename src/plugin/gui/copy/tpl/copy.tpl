
<div class="action">

    <form method="post" action="{FORM_COPY}">
        <fieldset>
            <legend><img src="{DIR_IMAGE}/copy.png" align="middle" width="32" height="32" alt="Copie" /> Copie </legend>
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
            <input type="submit" value="Copier" />
        </fieldset>
    </form>

</div>
