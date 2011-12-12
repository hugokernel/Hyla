
<table id="gallery" summary="Galerie photos">
    <!-- BEGIN gal_line -->
    <tr>
    <!-- BEGIN gallery_col -->
        <td width="20%" valign="top"<!-- BEGIN gallery_colspan --> colspan="{COLSPAN}"<!-- END gallery_colspan -->>

            <!-- BEGIN gallery_col_img -->
            <div class="gal_item">
                <a href="{PATH}"><img src="{FILE_ICON}" width="24" height="24" border="0" align="middle" alt="Infos" /></a> <a href="{PATH}">{FILE_NAME}</a>

                <!-- BEGIN gallery_comment -->
                    <a href="{PATH}#comment" title="{NBR_COMMENT} commentaire(s)"><img src="{DIR_IMAGE}/comment.png" class="gal_comment" width="32" height="32" border="0" align="middle" alt="Commentaires" /></a>
                <!-- END gallery_comment -->

            </div>

            <div class="gal_img">
                <a href="{PATH}"><img src="{OBJECT_MINI}" border="0" align="middle" alt="Image" /></a>
            </div>
            <!-- END gallery_col_img -->

            <!-- BEGIN gallery_col_other -->
            <div class="gal_img">
                <img src="{FILE_ICON}" width="32" height="32" border="0" align="middle" alt="Infos" /> <a href="{PATH}">{FILE_NAME}</a>
            </div>
            <!-- END gallery_col_other -->

        </td>
    <!-- END gallery_col -->
    </tr>
    <!-- END gal_line -->
</table>

