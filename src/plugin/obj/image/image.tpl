<div class="plugin">

    <p>
        <a href="{OBJECT_DOWNLOAD}"><img src="{OBJECT_MINI}" border="0" align="middle" alt="Image" /></a>
    </p>

    <!-- BEGIN image_size -->
    <p>
        Différentes tailles possibles :
        <a href="{AFF_SIZE_1_4}">1/4</a> |
        <a href="{AFF_SIZE_1_3}">1/3</a> |
        <a href="{AFF_SIZE_1_2}">1/2</a> |
        <a href="{AFF_SIZE_1_1}">1/1</a> |
        <a href="{AFF_SIZE_DEFAULT}">Taille par défaut</a>
    </p>
    <!-- END image_size -->

    <div class="info" style="text-align: left;">
        Largeur réelle de l'image : {IMAGE_X} px<br />
        Hauteur réelle de l'image : {IMAGE_Y} px

        <!-- BEGIN exif_data -->
        <p>
            <a href="#exif" onclick="swap_layer('exif')"> Données <acronym title="EXchangeable Image File format">EXIF</acronym> de l'image</a>
        </p>

        <table id="exif" class="tab jhidden" summary="Informations EXIF de la photo">
            <tr>
                <td>Marque</td>
                <td>{EXIF_MAKE}</td>
            </tr>
            <tr>
                <td>Modèle</td>
                <td>{EXIF_MODEL}</td>
            </tr>
            <tr>
                <td>Orientation de l'image</td>
                <td>{EXIF_ORIENTATION}</td>
            </tr>
            <tr>
                <td>X Résolution</td>
                <td>{EXIF_XRESOLUTION}</td>
            </tr>
            <tr>
                <td>Y Résolution</td>
                <td>{EXIF_YRESOLUTION}</td>
            </tr>
            <tr>
                <td>Unité de résolution</td>
                <td>{EXIF_RESOLUTIONUNIT}</td>
            </tr>
        </table>

        <!-- END exif_data -->
    </div>

</div>
