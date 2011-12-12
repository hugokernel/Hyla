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

/**
 *  Run trigger
 *  @param  string  $name       Trigger name
 *  @param  string  $context    Trigger context
 *  @param  mixed   ...         Parameter
 */
function run_trigger($name, $context = null) {
    $args = func_get_args();
    if ($args) {
        array_shift($args);
        array_shift($args);
    }

    $trig = trigger::getInstance();
    $trig->run($name, $context, $args);
}

class trigger
{
    var $callback;

    /**
     *  Constructor
     */
    function trigger() {
        $this->callback = array();
    }

    /**
     *  Get obj (Singleton...)
     */
    function getInstance() {
        static $obj = null;
        if ($obj == null) {
            $obj = new trigger();
        }
        return $obj;
    }

    /**
     *  Register a trigger
     */
    function register($name, $callback, $context = null) {
        $this->callback[$name][] = array('callback' => $callback, 'context' => $context);
    }

    /**
     *  Run trigger
     *  @param  string  $name       Trigger name
     *  @param  string  $context    Trigger context
     *  @param  mixed   ...         Parameter
     */
    function run($name, $context = null) {

        $args = func_get_args();
        array_shift($args);
        array_shift($args);

        if (!isset($this->callback[$name])) {
            return false;
        }

        foreach ($this->callback[$name] as $f) {
            if ($f['context'] && $f['context'] != $context) {
                continue;
            }

            call_user_func_array($f['callback'], $args);
        }

        return true;
    }
}

define('PRE_PLUGIN_GUI_RUN',    1);
define('POST_PLUGIN_GUI_RUN',   2);

/*
// Trigger declaration
$manifest = array(
    M_NAME          =>  'Dir',
    M_DESCRIPTION   =>  'View dir content.',
    M_AUTHOR        =>  'Hugo',
    M_TRIGGERS      =>  array(
        array(
            M_NAME      =>  PRE_COUCOU,
            M_CONTEXT   =>  'before coucou',
            M_CALLBACK  =>  'test',
        )
    ),
);

// Callback function
function test0($var = null) { echo 'test0 ('.$var.')<br>'; }
function test1($var = null) { echo 'test1 ('.$var.')<br>'; }
function test2() { echo 'test2 ('.print_r(func_get_args(), true).')<br>'; }

// Trigger definition
define('PRE_COUCOU',  1);
define('POST_COUCOU',  2);

// Trigger instanciation
$trig = trigger::getInstance();
$trig->register(PRE_COUCOU, 'test0', 'before couco');
$trig->register(PRE_COUCOU, 'test1');
$trig->register(POST_COUCOU, 'test2');

echo '<pre>';print_r($trig);echo '<br><hr>';


run_trigger(PRE_COUCOU, 'before coucou', 'paf');
echo 'coucou<br>';
run_trigger(POST_COUCOU, 'pouf', 'pif', 'pof', 'puf');
*/

?>
