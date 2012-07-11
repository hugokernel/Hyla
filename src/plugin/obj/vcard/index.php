<?php
/*
    This file is part of Hyla
    Copyright (c) 2004-2012 Charles Rincheval.
    All rights reserved

    Hyla is free software; you can redistribute it and/or modify it
    under the terms of the GNU General Public License as published
    by the Free Software Foundation; either version 2 of the License,
    or (at your option) any later version.

    Hyla is distributed in the hope that it will be useful, but
    WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Hyla; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */


require 'Contact_Vcard_Parse.php';

class plugin_obj_vcard extends plugin_obj {

    function plugin_obj_vcard($cobj) {
        parent::plugin_obj($cobj);

        $this->tpl->set_root($this->plugin_dir.'vcard');
        $this->tpl->set_file('vcard', 'vcard.tpl');

        $this->tpl->set_block('vcard', array(
            'vcardphoto'        =>  'Hdlvcardphoto',
            'vcardentreprise'   =>  'Hdlvcardentreprise',
            'vcardanniversaire' =>  'Hdlvcardanniversaire',
            'vcardnotes'        =>  'Hdlvcardnotes',
            'vcardadresse'      =>  'Hdlvcardadresse',
            'vcardemail'        =>  'Hdlvcardemail',
            'vcardphone'        =>  'Hdlvcardphone',
            'vcardnom'          =>  'Hdlvcardnom',
        ));
    }

    function aff($paff) {

        $this->addStyleSheet('default.css');

        $parse = new Contact_Vcard_Parse();
        $data = $parse->fromFile($this->real_file);

        foreach ($data as $card) {

            // Chargement des Données du fichier dans les variables

            // Nom et Prenom et Nom complet
            $Vcardnom          = $card['N'][0]['value'][0][0];
            $Vcardprenom       = $card['N'][0]['value'][1][0];
            $VcardFullName     = $card['FN'][0]['value'][0][0];

            // Titres  ex: Dr Jr etc ..
            $Vcardtitle        = $card['N'][0]['value'][3][0];

            // Telephones
            $Vcardphone        = $card['TEL'][0]['value'][0][0];
            $Vcardphone2       = $card['TEL'][1]['value'][0][0];
            $Vcardmobile     =  $card['TEL'][2]['value'][0][0];

            // Anniversaire
            $Vcardanniversaire = $card['BDAY'][0]['value'][0][0];

            // Entreprise
            $Vcardentreprise = $card['ORG'][0]['value'][0][0];
            $Vcardtype = $card['N'][0]['value'][2][0];

            // Adresses Email
            $Vcardemail = $card['EMAIL'][0]['value'][0][0];
            $Vcardemail2 = $card['EMAIL'][1]['value'][0][0];

            // Adresse postal
            $Vcardaddress1 = $card['ADR'][0]['value'][2][0];
            $Vcardaddress2 = $card['ADR'][0]['value'][1][0].',';
            $Vcardville = $card['ADR'][0]['value'][3][0];
            $Vcardstate = $card['ADR'][0]['value'][4][0];
            $Vcardcodepostal = $card['ADR'][0]['value'][5][0];
            $Vcardcountry = $card['ADR'][0]['value'][6][0];

            // Notes
            $Vcardnotes = $card['NOTE'][0]['value'][0][0];

            // Photo ... Gestion des Photos dans les vcard
            // et des problematiques lier au non respect de la norme par apple pour sont IPOD ...
            // Exemple : ENCODING=BASE64 ou BASE64 pour les vcard's generer sur MAC
            $VcardphotoEncoding = $card['PHOTO'][0]['param']['ENCODING'][0];

            // Exemple : TYPE=JPEG ou TYPE=PNG ou TYPE=GIF ou VIDE pour les Vcard's generer sur MAC
            $VcardphotoType = $card['PHOTO'][0]['param']['TYPE'][0];

            // Exemple : 11EEFFAAA= etc ... ou Vide
            $Vcardphoto = $card['PHOTO'][0]['value'][0][0];

            // Photo ... Gestion du binaire des photos
            if ($paff == 'vcardimage') {

                /*  Test s'il y a une photo dans le fichier vcard.
                    si pas d'image je renvoie un fichier GIF de 1 pixel transparent
                */
                if ($Vcardphoto == null) {
                    $Vcardphoto = "R0lGODlhAQABAIAAAFRdSQAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==";
                    $VcardphotoType = 'gif';
                }

                //Supprime les CR et LF et les espaces pouvant etre contenue dans le Base64
                $img = str_replace(array("\n", "\r", ' '), null, $Vcardphoto);

                // decode le base64
                $img = base64_decode($img);

                // mets tout en minuscule le type d'image
                $VcardphotoType = strtolower($VcardphotoType);

                // Recherche du type d'image
                $retourType = null;

                // Si le type est vide on le force a jpeg
                if (ereg('png', $VcardphotoType)) {
                    $retourType = 'png';
                } else if (ereg('gif', $VcardphotoType)) {
                    $retourType = 'gif';
                } else {
                    $retourType = 'jpeg';   // si le type n'est pas connu on le force a JPEG
                }

                header('Content-Type: image/'.$retourType);
                header('Content-Length: '.strlen($img));

                print($img);
                system::end();
            }

            // --------------------------------------------------------------
            // Controle des données afin de formater au mieux l'affichage.
            // --------------------------------------------------------------

            // --- Gestion des telephones du contact :
            if($Vcardphone != null || $Vcardphone2 != null || $Vcardmobile != null) {
                $this->tpl->set_var(array(
                'TEL01'     =>  $Vcardphone,
                'TEL02'     =>  $Vcardphone2,
                'PORTABLE'  =>  $Vcardmobile));
                $this->tpl->parse('Hdlvcardphone', 'vcardphone', true);
            }

            // --- Gestion de l'anniversaire  :
            // --- Gestion des notes  :
            // --- Gestion du nom de l'entreprise  :
            $tabtest = array(
                    array('ANNIVERSAIRE', $Vcardanniversaire),
                    array('ENTREPRISE', $Vcardentreprise),
                    array('NOTES', $Vcardnotes),
            );

            foreach ($tabtest as $t) {
                if ($t[1] != null) {
                    $this->tpl->set_var(strtoupper($t[0]), $t[1]);
                    $this->tpl->parse('Hdlvcard'.strtolower($t[0]), 'vcard'.strtolower($t[0]), true);
                }
            }

            // --- Gestion des adresses :
            if ($Vcardaddress1 != null) {
                $this->tpl->set_var(array(
                        'ADDRESSE01'    =>  $Vcardaddress1,
                        'ADDRESSE02'    =>  $Vcardaddress2,
                        'VILLE'         =>  $Vcardville,
                        'STATE'         =>  $Vcardstate,
                        'CODEPOSTAL'    =>  $Vcardcodepostal ,
                        'COUNTRY'       =>  $Vcardcountry,
                        ));
                $this->tpl->parse('Hdlvcardadresse','vcardadresse',true);
            }

            // --- Gestion des Emails  :
            if ($Vcardemail != null || $Vcardemail2 != null) {
                $this->tpl->set_var(array(
                        'EMAIL01'   =>  $Vcardemail,
                        'EMAIL02'   =>  $Vcardemail2,
                        ));
                $this->tpl->parse('Hdlvcardemail','vcardemail',true);
            }

            // --- Gestion de la photo  :
            if ($Vcardphoto != null)  {
                $this->tpl->parse('Hdlvcardphoto', 'vcardphoto', true);
            }

            // --- Gestion du nom du contact :
            if ($VcardFullName != null) {
                $this->tpl->set_var(array(
                        'NOM'       =>  null,
                        'PRENOM'    =>  null,
                        'FULLNAME'  =>  $VcardFullName,
                        'TITRE'     =>  $Vcardtitle,
                        ));
                $this->tpl->parse('Hdlvcardnom', 'vcardnom', true);
            } else {
                $this->tpl->set_var(array(
                        'NOM'       =>  $Vcardnom,
                        'PRENOM'    =>  $Vcardprenom,
                        'FULLNAME'  =>  null,
                        'TITRE'     =>  $Vcardtitle,
                        ));
                $this->tpl->parse('Hdlvcardnom', 'vcardnom', true);
            }

            // --- Vidage des variables .... sinon Gros Bug de repetition ...
            $tabtest = array(   'Hdlvcardphone', 'Hdlvcardanniversaire', 'Hdlvcardadresse',
                                'Hdlvcardemail', 'Hdlvcardnotes', 'Hdlvcardentreprise',
                                'Hdlvcardphoto'
                            );
            foreach ($tabtest as $t) {
                $this->tpl->set_var($t);
            }
        }

        $this->tpl->set_var('PATH_2_PLUGIN',$this->_url_2_plugin);
        $this->tpl->set_var('URL_CURRENT_OBJ', $this->url->linkToCurrentObj(null, null, null, 'vcardimage'));

        return $this->tpl->parse('OutPut', 'vcard');
    }
}

?>
