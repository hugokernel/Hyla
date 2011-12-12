
<!-- BEGIN error -->
<div class="error">
    <img src="{DIR_IMAGE}/error.png" width="16" height="16" alt="Croix" />&nbsp;{VIEW_ERROR}
</div>
<!-- END error -->

<!-- BEGIN status -->
<div class="status">
    {VIEW_STATUS}
</div>
<!-- END status -->

<!-- BEGIN suggestion -->
<div class="suggestion">
    {SUGGESTION}
</div>
<!-- END suggestion -->

<!-- BEGIN toolbar -->
<div id="toolbar">
    <fieldset>
        <legend>Actions / Affichages</legend>

        <a href="{DIR_ROOT}"><img src="{DIR_ROOT}img/icon.png" alt="{LANG:Return}" /></a>

        <!-- BEGIN toolbar_plugin_page -->
        <a href="{URL_PLUGIN}" title="{PLUGIN_DESCRIPTION}" style="background: orange;"><img src="{PLUGIN_ICON}" alt="{PLUGIN_DESCRIPTION}" /> {PLUGIN_NAME}</a>
        <!-- END toolbar_plugin_page -->

        <!-- BEGIN toolbar_plugin_action -->
        <a href="{URL_PLUGIN}" title="{PLUGIN_DESCRIPTION}" style="background: yellow;"><img src="{PLUGIN_ICON}" alt="{PLUGIN_DESCRIPTION}" /> {PLUGIN_NAME}</a>
        <!-- END toolbar_plugin_action -->

        <!-- BEGIN aff_slideshow -->
        <!--
        <a href="{URL_SLIDESHOW}" title="Lancer un diaporama" onclick="popup(this.href, 'scrollbars=yes,width=900,height=800,left=' + (screen.width / 2 - 450) + ',top=' + (screen.height / 2 - 400)); return false;"><img src="{DIR_IMAGE}/slideshow.png" alt="Stylo" /> Diaporama</a>
        -->
        <!-- END aff_slideshow -->
        <!-- BEGIN aff_download -->
        <!--
        <a href="{URL_DOWNLOAD}" title="Télécharger l'objet courant"><img src="{DIR_IMAGE}/download.png" alt="Télécharger" /> Télécharger</a>
        -->
        <!-- END aff_download -->

        <!-- BEGIN aff_info -->
        <!--<a href="{URL_INFO}" title="Informations sur l'objet"><img src="{DIR_IMAGE}/info.png" alt="Info" /> Info</a>
        -->
        <!-- END aff_info -->


    </fieldset>
</div>

<!-- END toolbar -->

