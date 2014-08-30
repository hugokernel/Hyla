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

class plugin_obj_csv extends plugin_obj {

    public function __construct($cobj) {
        parent::__construct($cobj);

        $this->tpl->set_root($this->plugin_dir.'csv');
        $this->tpl->set_file('csv', 'csv.tpl');

        $this->tpl->set_block('csv', array(
                'head_line'     =>  'Hdlhead_line',
                'line_int'      =>  'Hdlline_int',
                'line_string'   =>  'Hdlline_string',
                'row'           =>  'Hdlrow',
                ));
    }

    public function aff() {

        $this->addStyleSheet('default.css');

        $sep = array(',', ';', "\t");   // Au choix
        $qut = array('"', "'");         // On enlève aussi les quotes

        // Lecture du fichier
        $f = file($this->real_file);

        // Détection du séparateur
        foreach ($sep as $separator) {
            $p = strpos($f[0], $separator);
            if ($p)
                break;
        }

        $i = 0;
        foreach ($f as $line) {

            $this->tpl->set_var('Hdlline_int');
            $this->tpl->set_var('Hdlline_string');
            $this->tpl->set_var('Hdlhead');

            $r = explode($separator, $line);

            foreach ($r as $row) {
                $row = trim($row);

                // Le contenu possède-t-il des guillemets ?
                foreach ($qut as $quote) {
                    if ($row{0} == $quote && $row{strlen($row) - 1} == $quote) {
                        $row = substr($row, 1, strlen($row) - 2);
                    }
                }

                $this->tpl->set_var('CONTENT', $row);

                // Affichage de l'entête
                if (!$i) {
                    $this->tpl->parse('Hdlhead_line', 'head_line', true);
                } else {
                    if (is_numeric($row)) {
                        $this->tpl->parse('Hdlline_int', 'line_int', true);
                    } else {
                        $this->tpl->parse('Hdlline_string', 'line_string', true);
                    }
                }    
            }
            $this->tpl->parse('Hdlrow', 'row', true);
            $i++;
        }

        return $this->tpl->parse('OutPut', 'csv');
    }
}
