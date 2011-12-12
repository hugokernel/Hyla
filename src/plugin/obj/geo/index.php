<?php
/*
    This file is part of Hyla
    Copyright (c) 2004-2007 Charles Rincheval.
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

require 'geo.class.php';

class plugin_obj_geo extends plugin_obj {

    function plugin_obj_geo($cobj) {
        parent::plugin_obj($cobj);

        $this->tpl->set_root($this->plugin_dir.'geo');
        $this->tpl->set_file('geo', 'geo.tpl');
        $this->tpl->set_block('geo', array(
                'kml'       =>  'Hdlkml',
                'json_line' =>  'Hdljson_line',
                'json'      =>  'Hdljson',
                ));
    }

    function aff() {

        $google_key = $this->getConfVar('google_key', $_SERVER['HTTP_HOST']);
        $max_poi = $this->getConfVar('max_poi');
        
        switch ($this->cobj->extension) {
            case 'kml':
                $this->tpl->set_var(array(
                        'OBJECT_DOWNLOAD'   =>  $this->url->linkToCurrentObj('download'),
                        ));
                $this->tpl->parse('Hdlkml', 'kml', true);
                break;
            case 'asc':
            case 'ov2':
                $qut = array('"', "'");

                $data = geo::getData($this->real_file, $this->cobj->extension);
                if ($data) {
                    $i = 0;
                    foreach ($data as $k) {
                        if ($max_poi && ++$i > $max_poi) {
                            break;
                        }

                        $label = $k['label'];

                        // Le contenu possède-t-il des guillemets ?
                        foreach ($qut as $quote) {
                            if ($label{0} == $quote && $label{strlen($label) - 1} == $quote) {
                                $label = substr($label, 1, strlen($label) - 2);
                            }
                        }

                        $label = str_replace('"', '\"', $label);

                        $this->tpl->set_var(array(
                            'LON'       =>  $k['lon'],
                            'LAT'       =>  $k['lat'],
                            'LABEL'     =>  $label,
                        ));
                        $this->tpl->parse('Hdljson_line', 'json_line', true);
                    }
                }
                $this->tpl->parse('Hdljson', 'json', true);
                break;
        }

        $this->tpl->set_var(array(
                'GOOGLE_KEY'        =>  $google_key,
                ));

        return $this->tpl->parse('OutPut', 'geo');
    }
}

?>
