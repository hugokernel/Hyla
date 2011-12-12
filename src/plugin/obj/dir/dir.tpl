
<div id="dir">

    <table width="100%" summary="Liste des fichiers et dossiers de l'objet courant">
    <!-- BEGIN line -->
    <!-- BEGIN line_header -->
        <tr>
            <td colspan="5" class="header">
                {HEADER_VALUE}
                <span class="header-info">{HEADER_INFO_VALUE}</span>
            </td>
        </tr>
    <!-- END line_header -->
    <!-- BEGIN line_content -->
        <tr class="line obj_container">
            <td width="50%">
                <!-- BEGIN line_file -->
                <span class="file drop" id="{FILE_OBJECT_ID}">
                    <img src="{FILE_ICON}" class="icon" align="middle" alt="Infos" />
                    {FILE_PATH}
                </span>
                <!-- END line_file -->
                <!-- BEGIN line_dir -->
                <span class="dir drop" id="{FILE_OBJECT_ID}">
                    <img src="{FILE_ICON}" class="icon" align="middle" alt="Infos" />
                    {FILE_PATH}
                </span>
                <!-- END line_dir -->

                <!-- BEGIN line_comment -->
                <a href="{PATH_INFO}#comment" title="{NBR_COMMENT} commentaire(s)">
                    <img src="{DIR_IMAGE}/comment.png" border="0" align="middle" alt="Commentaires" /><!--<sub>{NBR_COMMENT}</sub>-->
                </a>
                <!-- END line_comment -->
            </td>
            <td width="40%" align="right" class="edit-description description" id="{FILE_OBJECT_ID}" >{FILE_DESCRIPTION}</td>
            <td width="5%" align="right">{FILE_SIZE}</td>
            <td width="5%" align="right"><a href="{PATH_DOWNLOAD}" title="Télécharger"><img src="{DIR_IMAGE}/download.png" border="0" align="middle" alt="Télécharger" /></a></td>
            <td><input type="checkbox" name="obj[]" value="{FILE_OBJECT}" /></td>
        </tr>
    <!-- END line_content -->
    <!-- END line -->
    </table>

</div>
