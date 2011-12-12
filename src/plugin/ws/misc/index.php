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

class plugin_ws_misc extends plugin_ws {

    /**
     *  Constructor
     */
    function plugin_ws_misc() {
        parent::plugin_ws();
    }

    /**
     *  Get icon
     */
    function getIcon() {

        $val = null;

		$hdl = dir(DIR_IMG_PERSO);
		if ($hdl) {
			while (false !== ($occ = $hdl->read())) {

                // Skip special dir
				if ($occ{0} == '.') {
					continue;
                }

                $val .= '<img src="'.HYLA_ROOT_URL.'img/perso/'.$occ.'" align="middle" class="plugin_act_misc" alt="Image" />';
			}
		}

        return $val;
    }

    /**
     *  Get gui
     */
    function getGui($name) {
        $gui = plugins::get(PLUGIN_TYPE_GUI, $name);
        if (system::isError($ret)) {
            return $ret;
        }
        return $gui->run();
    }

    /**
     *  Get plugin content
     *  @param  string  $file   File
     *  @param  int     $tree   View tree or not
     */
    function getPluginContent($file, $tree = 2) {

        require HYLA_ROOT_PATH.'src/run_obj.class.php';

/*
global $cobj, $obj;
$GLOBAL['cobj'] = $obj->getInfo($file);
*/
$this->obj->setCurrentObj($file);

//return '##'.print_r($this->obj->getCurrentObj(), true);

        $p = new run_obj(); //$this->obj->getCurrentObj);

        if ($tree != 2) {
            $p->viewTree($tree);
        }

        $p->viewTitle(false);
        $p->viewPagination(true);
        $p->viewComments(false);
        $p->viewHeader(false);
        $p->viewToolbar(false);
        
        $p->onlyPluginContent(true);

        $p->setAction(array(
                'add'       =>  __('Add to current basket'),
                'addnew'    =>  __('Add to new basket')
        ));

        $out = $p->run();

        return $out;
    }
}

?>
