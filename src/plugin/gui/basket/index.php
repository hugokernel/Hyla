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

class plugin_gui_basket extends plugin_gui {

    /**
     *  Constructor
     */
    function plugin_gui_basket() {
        parent::plugin_gui();

        $this->events['onsuccess'] =    array(
                                            'msg'       =>  __('Objet added to basket !'),
                                            'redirect'  =>  'current'
                                        );
    }

    function act() {

        $ret = false;

if (array_key_exists('act', $_GET) && $_GET['act'] == 'clear') {
    $act = plugins::get(PLUGIN_TYPE_WS, 'basket');
    $act->run('clear');    
}

        if ($_POST && array_key_exists('action', $_POST)) {

            $act = plugins::get(PLUGIN_TYPE_WS, 'basket');

            switch ($_POST['action']) {

                case 'clear':
                    $act->run('clear');    
                    break;

                case 'remove':
                    if (array_key_exists('obj', $_POST)) {
                        foreach ($_POST['obj'] as $object) {
                            $act->run('remove', array('obj' => $object));
                        }
                    }
                    break;

                default:
                    if (array_key_exists('obj', $_POST)) {
                        foreach ($_POST['obj'] as $object) {
                            $act->run('add', array('obj' => $object));
                        }
                        $ret = true;
                    }
                    break;
            }
/*            
dbug($_SESSION);
exit;

            if ($act->status) {
                $this->last_error_msg = $act->status;
            }
*/
        }

        return $ret;
    }

    /**
     *  Callback function
     */
    function getBasket($path) {
        $act = plugins::get(PLUGIN_TYPE_WS, 'basket');
        return $act->run('getList');
    }

    function aff() {
        global $url;

        $currentId = $this->obj->datasource->getCurrent();
       
        $id = $this->obj->datasource->register('BASKET', array('plugin_gui_basket', 'getBasket'));
        $this->obj->datasource->setCurrent($id);
        $this->obj->setCurrentObj('/');

//$toto = $this->obj->getDirContent();
/*
$toto = $this->obj->getCurrentObj();
dbug($toto);
system::end();
*/
//        $url->setContext(array('page', 'basket'));

        $p = new run_obj();
//        $p->viewHeader(true);
        $p->viewHeader(false);
//        $p->viewToolbar(false);
        $p->viewTree(false);
        $p->setTitle(__('Basket content : "%s"', basket::getInstance()->getCurrentName()), HYLA_ROOT_URL.$this->plugin_dir.$this->plugin_name.'/icon.png');
        $p->setAction(  array(  'remove'  => __('Remove from basket'),
                                'clear'   => __('Clear basket'))
                    );

        $out = $p->run();
//$basket = basket::getInstance();
//dbug($basket);
        $this->obj->datasource->setCurrent($currentId);

        return $out;
    }
}

?>
