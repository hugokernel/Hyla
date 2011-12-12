
<div class="plugin" id="archive">

    <b>Contenu de l'archive :</b>

    <table width="100%" summary="Liste des fichiers contenus dans l'archive">
    <!-- BEGIN zipfile -->
        <tr class="line">
            <td width="78%" align="left"><a href="{URL_FILE}"><img src="{FILE_ICON}" class="icon" border="0" align="middle" alt="Infos" /> {FILE_NAME}</a></td>
            <td width="20%" align="right">{FILE_SIZE}</td>
            <td width="2%" align="right"><a href="{URL_DOWNLOAD}" title="Télécharger"><img src="{DIR_IMAGE}/download.png" width="32" height="32" border="0" align="middle" alt="Télécharger" /></a></td>
        </tr>
    <!-- END zipfile -->
    </table>

    <!-- BEGIN act_extract -->
    <p>
        <a href="{URL_EXTRACT}">Extraire dans le dossier parent</a>
    </p>

    <p>
        {RAPPORT}
    </p>
    <!-- END act_extract -->

    <p class="info" style="text-align: left">
        Taille de l'archive : {COMPRESSED_SIZE}<br />
        Taille de l'archive décompressée : {REAL_SIZE}<br />
        Nombre de fichiers : {NBR_FILE}<br />
    </p>

</div>
