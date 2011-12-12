<div class="plugin">
 <!-- BEGIN vcardnom -->
    <div style=" TEXT-ALIGN:center; width:400px; border: 1px solid #e3e3e3;">

           <table width="100%" border="1" cellspacing="0" cellpadding="0" summary="Contenu de la vcard" bgcolor="#f4f4f4">
              <tr>
                <td rowspan="8" width="2" height="10" bgcolor="#e3e3e3" ></td>
                  <td colspan="3" bgcolor="#f4f4f4"  style="padding-top: 15px; background: #f4f4f4 url({PATH_2_PLUGIN}vcard.gif) left top no-repeat; ">
                </tr>
                <tr>
                    <td width="50px" colspan="1" bgcolor="#CCCCCC" ><img align="center" src="{PATH_2_PLUGIN}identity.png" alt="identity" /></td>
                <td colspan="2" bgcolor="#CCCCCC" align="center" ><b>{TITRE}{FULLNAME}{NOM}{PRENOM}</b></td>
                </tr>
     <!-- BEGIN vcardphone -->
                <tr>
                    <td width="50px" style="padding-top: 2px; padding-bottom: 2px;"><img src="{PATH_2_PLUGIN}phone.png" alt="phone" /></td>
                    <td  >T&eacute;l&eacute;phone&nbsp;</td>
                    <td  >{TEL01} {TEL02} {PORTABLE}</td>
                </tr>
     <!-- END vcardphone -->
     <!-- BEGIN vcardemail -->
              <tr >
                <td width="50px" style="padding-top: 2px; padding-bottom: 2px;"><img src="{PATH_2_PLUGIN}email.png" alt="Email" /></td>
                    <td >Courriel</td>
                    <td >
                        <a href="mailto:{EMAIL01}">{EMAIL01}</a>
                        <br />
                        <a href="mailto:{EMAIL02}">{EMAIL02}</a>
                    </td>
                </tr>
     <!-- END vcardemail -->
     <!-- BEGIN vcardadresse -->
                <tr >
                    <td width="50px" style="padding-top: 2px; padding-bottom: 2px;"><img src="{PATH_2_PLUGIN}addresse.png" alt="Adresse" /></td>
                    <td >Adresse</td>
                    <td >{ADDRESSE01} {ADDRESSE02} {VILLE} {COUNTRY} {CODEPOSTAL} {STATE}</td>
                </tr>
     <!-- END vcardadresse -->
     <!-- BEGIN vcarddivers -->
                <tr >
                <td width="50px" style="padding-top: 2px; padding-bottom: 2px;"><img src="{PATH_2_PLUGIN}notes.png" alt="Note" /></td>
                    <td >Divers</td>
                    <td >{NOTES}</td>
                </tr>
     <!-- END vcarddivers -->
     <!-- BEGIN vcardanniv -->
                <tr >
                  <td width="50px" style="padding-top: 2px; padding-bottom: 2px;"><img src="{PATH_2_PLUGIN}anniv.png" alt="Anniversaire" /></td>
                    <td >Anniversaire</td>
                    <td >{ANNIVERSAIRE}</td>
                </tr>
     <!-- END vcardanniv -->
     <!-- BEGIN vcardboite -->
                <tr>
                  <td width="50px" style="padding-top: 2px; padding-bottom: 2px;"><img src="{PATH_2_PLUGIN}works.png" alt="Works" /></td>
                    <td  >Entreprise</td>
                    <td >{ENTREPRISE}</td>
                </tr>
     <!-- END vcardboite -->
     <!-- BEGIN vcarphoto -->
                <tr>
                  <td width="50px" style="padding-top: 2px; padding-bottom: 2px;"><img src="{PATH_2_PLUGIN}photo.png" alt="Photo" /></td>
                    <td  >Photo</td>
                    <td><img  src="{URL_CURRENT_OBJ}" alt='Erreur format photo' /></td>
                </tr>
    <!-- END vcarphoto -->

            </table>

          </div>
  <br />
     <!-- END vcardnom -->
</div>
