
<p>
    <a href="{DIR_ROOT}doc/{L10N}/info.htm" onclick="popup(this.href); return false;" title="Aide contextuelle"><img src="{DIR_IMAGE}/help.png" alt="Point d'interrogation" /> </a>
</p>

<fieldset>
    <legend>{LANG:General}</legend>

    <img src="{ICON}" alt="Icone" />

        <p>
            Nom : {NAME}
        </p>
        <!-- BEGIN aff_size -->
        <p>
            Taille : {SIZE}
        </p>
        <!-- END aff_size -->
        <p>
            Type : {TYPE}
        </p>
        <!-- BEGIN aff_gen_file -->
        <!-- BEGIN aff_mime -->
        <p>
            Type mime : {MIME}
        </p>
        <!-- END aff_mime -->
        <!-- BEGIN aff_md5 -->
        <p>
            Md5 (<a href="{URL_MD5_CALCULATE}">Calculer</a>) {MD5}
        </p>
        <!-- END aff_md5 -->
        <!-- END aff_gen_file -->

        <p>
            Télécharger :
            <a href="{URL_DOWNLOAD_DIRECT}">Direct</a>
            <!-- BEGIN aff_download_type -->
            <a href="{URL_DOWNLOAD_TYPE}">{DOWNLOAD_TYPE_NAME}</a>
            <!-- END aff_download_type -->
        </p>

</fieldset>

<fieldset>
    <legend>{LANG:Syndication}</legend>

    <!-- BEGIN aff_file -->
    <ul>
        <li><a href="{URL_RSS_FILE_COMMENT}" title="Rss 1.0"><img src="{DIR_ROOT}img/rss.png" alt="rss" /> {LANG:Rss feed of comments of this file.}</a></li>
    </ul>
    <!-- END aff_file -->

    <!-- BEGIN aff_dir -->
    <ul>
        <li><a href="{URL_RSS_DIR_OBJECT}" title="Rss 1.0"><img src="{DIR_ROOT}img/rss.png" alt="rss" /> {LANG:Rss feed of file in this dir.}</a></li>
        <li><a href="{URL_RSS_DIR_COMMENT}" title="Rss 1.0"><img src="{DIR_ROOT}img/rss.png" alt="rss" /> {LANG:Rss feed of comment in this dir.}</a></li>
    </ul>
    <!-- END aff_dir -->

    <p class="info">
        {LANG:Remain permanently connected to activity of the site with rss.}
    </p>

</fieldset>

<fieldset>
    <legend>{LANG:Export}</legend>

    {MSG_INFO}

    <p>
        {LANG:Javascript exportation} :
    </p>
    <textarea rows="1" onclick="this.focus();this.select();">{URL_OBJ_EXPORT_JS}</textarea>

    <p class="info">
        {LANG:With jQuery, you can include Hyla plugin directly in your html dom, try this : $("#div_name").load("http://EXPORTED_URL")}
    </p>

    <a href="#" onclick="$('#testing #destination').load('{URL_OBJ_EXPORT_JS}'); $('#testing').css('display', 'block');">{LANG:Test}</a>

    <div id="testing" style="display: none; width: 600px; margin: 20%; background: white; border: 1px solid #CCC; margin: 5px; padding: 5px; position: fixed; top: 1px;">
        <a href="#" onclick="$('#testing').css('display', 'none');">{LANG:Close}</a>
        <div id="destination">
        </div>
    </div>

    <hr />

    <p>
        {LANG:Iframe exportation} :
    </p>
    <textarea rows="1" onclick="this.focus();this.select();">&lt;iframe src="{URL_OBJ_EXPORT_IFRAME}"&gt;&lt;/iframe&gt;</textarea>

    <a href="#export-test-iframe" onclick="swap_layer('export-test-iframe');">{LANG:Test}</a>
    <div id="export-test-iframe" class="jhidden">
       <iframe src="{URL_OBJ_EXPORT_IFRAME}" width="100%" height="500px" border="0"></iframe>
    </div>

    <p class="info">
        {LANG:Export content plugin in your own site, blog !}
    </p>

</fieldset>
