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

class plugin_gui_wsinfo extends plugin_gui {

    /**
     *  Constructor
     */
    function plugin_gui_wsinfo() {
        parent::plugin_gui();
    }

    function act() {
        return false;
    }

    function aff() {
        global $url;

        $this->tpl->set_file('wsinfo', 'tpl/wsinfo.tpl');
        
        $this->tpl->set_block('wsinfo', array(
            'param'     =>  'Hdlparam',
            'method'    =>  'Hdlmethod',
            'group'     =>  'Hdlgroup',
        ));

        require HYLA_ROOT_PATH.'src/inc/ws.class.php';
        $ret = ws::getMethods();

        foreach ($ret as $group => $methods) {

            $this->tpl->set_var(array(
                    'GROUP'     =>  $group,
                    'Hdlmethod' =>  null,
                    ));
            
            // Methods
            foreach ($methods as $method => $params) {
                $this->tpl->set_var(array(
                        'METHOD_NAME'   =>  $method,
                        'Hdlparam' =>  null,
                        ));

                // Parameters
                foreach ($params as $param) {
                    $this->tpl->set_var(array(
                            'PARAM_NAME'   =>  $param[0],
                            'PARAM_TYPE'   =>  $param[2],
                            'PARAM_DESC'   =>  $param[3],
                            ));
                    $this->tpl->parse('Hdlparam', 'param', true);
                }

                $this->tpl->parse('Hdlmethod', 'method', true);
            }
                    
            $this->tpl->parse('Hdlgroup', 'group', true);
        }

        return $this->tpl->parse('OutPut', 'wsinfo');
    }
}

?>
