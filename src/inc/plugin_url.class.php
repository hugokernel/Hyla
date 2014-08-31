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

class tUrl {
    public $obj;       // Current object

    public $act;       // Current action
    public $aff;       // Current view

    public $pact;      // Plugin action
    public $paff;      // Plugin view
}


class plugin_url extends plugin
{
    public $absolute;

    public $context_saving;

    public $current;
    
    public $root_url;

    public function __construct() {
        parent::__construct();

        $this->absolute = true;

        $this->context_saving = false;

        $this->current = new tUrl;
        
        $this->root_url = null;
    }
    
    /*  Set root path after server name
        @param  string  $url    Path after servername
     */
    public function setRootUrl($url) {
        $this->root_url = $url;
    }

    /*  Set is absolute or not
        @param  bool    $absolute   Make absolute url
     */
    public function makeAbsolute($absolute) {
        $this->absolute = $absolute;
    }

    /*  Context
        @param  bool    $context_saving Force save context
     */
    public function setContextSaving($context_saving) {
        $ret = $this->context_saving;
        $this->context_saving = $context_saving;
        return $ret;
    }

    /*  Call scan plugin method
     */
    public function load() {
        $this->current = $this->scan($_REQUEST);
    }

    /*  Get url for page
     */
    public function linkToPage($aff, $object = null, $act = null, $pact = null, $paff = null) {
        $aff = is_array($aff) ? $aff : array($aff);
        $aff = array_merge(array('page'), $aff);
        return $this->_get($object, $aff, $act, $pact, $paff, false);
    }

    /*  Get url for page
     */
    public function linkToObj($object, $aff = null, $act = null, $pact = null, $paff = null) {
        return $this->_get($object, $aff, $act, $pact, $paff, true);
    }

    /*  Renvoie l'url correspondante pour la visualisation de l'objet courant

	 *      */
    public function linkToCurrentObj($aff = null, $act = null, $pact = null, $paff = null) {
        global $cobj;
        
        if (isset($cobj->target)) {
            $object = array($cobj->file, $cobj->target);   
        } else {
            $object = $cobj->file ? $cobj->file : '/';   
        }
        
        return $this->linkToObj($object, $aff, $act, $pact, $paff, true);
    }
    
    /*  Wrapper for get
     */
    private function _get($object, $aff = null, $act = null, $pact = null, $paff = null, $b = true) {
        
        if ($act && !is_array($act)) {
            $act = array($act);
        }

        if ($aff) {
            $aff = is_array($aff) ? $aff : array($aff);
            $aff = $b ? array_merge(array('obj'), $aff) : $aff;
        }

        if ($aff) {
            if (!is_array($aff)) {
                $aff = array($aff);
            }
        } else {
            $aff = array('obj');
        }
        
        // Context saving
        $aff = $this->getContextSaving($aff);

        // If object is array, it's an archive !
        if (is_array($object)) {
            $object = $object[0].'!'.$object[1];
        }
        
        return $this->get($object, $aff, $act, $pact, $paff, $b);
    }

    /*  Set obj
        @param string   $value  Value
     */
    public function setParamObj($value) {
        $this->current->obj = $value;
    }

    /*  Get specified element
        @param string   $element    Element
        @param string   $index      Index
     */
    public function getParam($element, $index = 0) {
        $ret = null;
        if (is_array($this->current->$element)) {
            if (array_key_exists($index, $this->current->$element)) {
                $tab = $this->current->$element;
                $ret = $tab[$index];
            }
        } else {
            $ret = $this->current->$element;
        }
        return $ret;
    }

    /*  Set specified element
        @param string   $element    Element
        @param int      $index      Index
        @param string   $type       Value
     */
    public function setParam($element, $index, $value) {
        $tab = &$this->current->$element;
        $tab[$index] = $value;
        return $value;
    }

    /*  Encode opbject
        @param  string  $obj    Object
     */
    public function encode($obj) {
        global $conf;
        $ret = null;

        if ($conf['url_encode']) {
            $a = array();
            $ret = explode('/', $obj);
            foreach ($ret as $occ) {
                $a[] = urlencode($occ);
            }
            $ret = implode('/', $a);
        } else {
            $ret = $obj;
        }
        return $ret;
    }

    /*  Context saving
        @todo   Rewrite this ugly code !!
     */
    public function getContextSaving($aff) {
        $ret = null;
        if ($this->context_saving) {
            if (array_key_exists(1, $this->current->aff) && $this->current->aff[1] == 'export') {
                $arg = null;
                if (array_key_exists(2, $this->current->aff)) {
                    $arg = $this->current->aff[2];
                }

                if ($aff[0] == 'obj') {
                    $ret = array('obj', 'export');
                    if ($arg && $arg != 'start') {
                        $ret[] = $arg;
                        if (array_key_exists(3, $this->current->aff)) {
                            $ret[] = $this->current->aff[3];   
                        }
                    }
                    
                    if (count($aff) == 1) {
                        // Blank intentionnaly
                    } else if (count($aff) >= 2 && $aff[1] == 'start') {
                        if ($aff[2]) {
                            $ret[] = 'start';
                            $ret[] = $aff[2];   
                        }
                    } else {
                        $ret = $aff;
                    }
                }
            }
        } else {
            $ret = $aff;   
        }

        return $ret;
    }
}
