
<div class="plugin">
    <p class="info">
        Aucun plugin de visualisation n'a été trouvé pour ce type de fichier !
    </p>
    <p>
        <select name="plugin" id="plugin" onchange="location.href='{OBJECT}&amp;act=force-' + this.options[this.selectedIndex].value">
            <option>Sélectionnez un plugin pour visualiser le fichier...</option>
            <!-- BEGIN plugin_choice --><option value="{PLUGIN_NAME}">{PLUGIN_NAME} ( {PLUGIN_DESCRIPTION} ) </option><!-- END plugin_choice -->
        </select>
    </p>

    <p>
        <a href="{URL_OBJ_DOWNLOAD}">Téléchargez le fichier directement</a>
    </p>
</div>
