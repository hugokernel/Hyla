<div class="plugin">
    <div id="vcard">
        <!-- BEGIN vcardnom -->
        <div class="elem">
            <table summary="Contenu de la vcard">
                <tr>
                    <td colspan="3" class="image">
                        <img align="middle" src="{PATH_2_PLUGIN}vcard.gif" alt="Photo d'identité" />
                    </td>
                </tr>
                <tr class="title">
                    <td><img align="middle" src="{PATH_2_PLUGIN}identity.png" alt="Photo d'identité" /></td>
                    <td colspan="2">{TITRE}{FULLNAME}{NOM}{PRENOM}</td>
                </tr>
                <!-- BEGIN vcardphone -->
                <tr>
                    <td><img src="{PATH_2_PLUGIN}phone.png" alt="Téléphone" /></td>
                    <td>T&eacute;l&eacute;phone&nbsp;</td>
                    <td>{TEL01} {TEL02} {PORTABLE}</td>
                </tr>
                <!-- END vcardphone -->
                <!-- BEGIN vcardemail -->
                <tr>
                    <td><img src="{PATH_2_PLUGIN}email.png" alt="Courrier" /></td>
                    <td>Courriel</td>
                    <td>
                        <a href="mailto:{EMAIL01}">{EMAIL01}</a>
                        <br />
                        <a href="mailto:{EMAIL02}">{EMAIL02}</a>
                    </td>
                </tr>
                <!-- END vcardemail -->
                <!-- BEGIN vcardadresse -->
                <tr>
                    <td><img src="{PATH_2_PLUGIN}addresse.png" alt="Adresse" /></td>
                    <td>Adresse</td>
                    <td>{ADDRESSE01} {ADDRESSE02} {VILLE} {COUNTRY} {CODEPOSTAL} {STATE}</td>
                </tr>
                <!-- END vcardadresse -->
                <!-- BEGIN vcardnotes -->
                <tr>
                    <td><img src="{PATH_2_PLUGIN}notes.png" alt="Note" /></td>
                    <td>Divers</td>
                    <td>{NOTES}</td>
                </tr>
                <!-- END vcardnotes -->
                <!-- BEGIN vcardanniversaire -->
                <tr>
                    <td><img src="{PATH_2_PLUGIN}anniv.png" alt="Anniversaire" /></td>
                    <td>Anniversaire</td>
                    <td>{ANNIVERSAIRE}</td>
                </tr>
                <!-- END vcardanniversaire -->
                <!-- BEGIN vcardentreprise -->
                <tr>
                    <td><img src="{PATH_2_PLUGIN}works.png" alt="Works" /></td>
                    <td>Entreprise</td>
                    <td>{ENTREPRISE}</td>
                </tr>
                <!-- END vcardentreprise -->
                <!-- BEGIN vcardphoto -->
                <tr>
                    <td><img src="{PATH_2_PLUGIN}photo.png" alt="Photo" /></td>
                    <td>Photo</td>
                    <td><img src="{URL_CURRENT_OBJ}" alt="Erreur format photo" /></td>
                </tr>
                <!-- END vcardphoto -->
            </table>
        </div>
        <!-- END vcardnom -->
    </div>
</div>
