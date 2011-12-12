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

define('L_DEBUG',     1);
define('L_NOTICE',    2);
define('L_INFO',      3);
define('L_WARNING',   4);
define('L_ERROR',     5);
define('L_FATAL',     6);

define('LOG_TYPE_FIREBUG',  1);
define('LOG_TYPE_SYSTEM',   2);
define('LOG_TYPE_OUT',      4);
define('LOG_TYPE_BDD',      8);

$log_event = array(
           L_DEBUG      =>  LOG_TYPE_FIREBUG | LOG_TYPE_OUT,
           L_NOTICE     =>  LOG_TYPE_SYSTEM,
           L_INFO       =>  LOG_TYPE_FIREBUG | LOG_TYPE_SYSTEM | LOG_TYPE_BDD,
           L_WARNING    =>  LOG_TYPE_FIREBUG,
           L_ERROR      =>  LOG_TYPE_FIREBUG | LOG_TYPE_SYSTEM | LOG_TYPE_BDD,
           L_FATAL      =>  LOG_TYPE_FIREBUG | LOG_TYPE_SYSTEM | LOG_TYPE_OUT,
        );

/*  Catch all notice and warning
 */
set_error_handler(array('log', 'errorHandler'), E_NOTICE | E_WARNING);


class log
{
    var $_log_table;

    var $bdd;

    var $_last_msg;

    var $_silent;

    function log($silent = false) {
        $this->bdd = plugins::get(PLUGIN_TYPE_DB);

        $this->_log_table = TABLE_LOG;

        $this->_last_msg = null;

        $this->_silent = $silent;
    }

    /**
     *  Get log (Singleton...)
     */
    function getInstance($silent = false) {
        static $log = null;
        if ($log == null) {
            $log = new log($silent);
        }
        return $log;
    }

    /**
     *  Get string from int
     *  @param  int $num    $Num
     *  @access static
     */
    function getStringLog($num) {
        switch ($num) {
            case L_DEBUG:   $str = 'debug';     break;
            case L_NOTICE:  $str = 'notice';    break;
            case L_INFO:    $str = 'info';      break;
            case L_WARNING: $str = 'warning';   break;
            case L_ERROR:   $str = 'error';     break;
            case L_FATAL:   $str = 'fatal';     break;
        }
        return $str;
    }

    /**
     *  Add
     *  @param  int     $type       Log type (L_*)
     *  @param  string  $msg        Message
     *  @param  string  $context    Context (action...)
     *  @param  string  $username   Username
     *  @param  string  $file       File
     */
    function addInDb($type, $msg, $context, $username = null, $file = null) {
        $this->_last_msg = $msg;
        $sql = "INSERT INTO {$this->_log_table}
                (log_obj_file, log_usr_name, log_context, log_date, log_type, log_msg)
                VALUES
                ('".$this->bdd->quote($file)."', '$username', '$context',  '".time()."', '".log::getStringLog($type)."', '".$this->bdd->quote($msg)."');";
        if (!$var = $this->bdd->execQuery($sql)) {
            system::end($this->bdd->getErrorMsg());
        }
    }

    /**
     *  Error handler
     */
    function errorHandler($number, $msg, $file, $line) {

        switch ($number) {
            case E_NOTICE:  $type = L_NOTICE;   break;
            case E_WARNING: $type = L_WARNING;  break;
        }

        return !(log::add($type, $msg, 'system', $file, $line));
    }

    /**
     *  Add log
     *  @param  string  $type   Log type
     *  @param  string  $msg    Message
     *  @access static
     */
    function add($type, $msg, $context = 'global', $opt0 = null, $opt1 = null) {

        global $cuser, $log_event;

        $logtype = $log_event[$type];

        $str_type = ucfirst(log::getStringLog($type));

        $str = print_r($msg, true);

        $option = ($context == 'system') ? ' ('.$opt0.' @ '.$opt1.')' : null;

        $head = '[Hyla - '.$str_type.'] : ';

        // Log into Firebug
        if ($logtype & LOG_TYPE_FIREBUG) {
            switch ($type) {
                case L_DEBUG:
                case L_NOTICE:
                case L_INFO:    $function = 'info';     break;
                case L_WARNING: $function = 'warn';     break;
                case L_ERROR:
                case L_FATAL:   $function = 'error';    break;
           }

            $str = str_replace(array("\n", '"'), array('', '\\"'), $str);
            log::out('<script>console.'.$function.'("'.$head.$str.$option.'");</script>'."\n");
        }

        // Log into syslog
        if ($logtype & LOG_TYPE_SYSTEM) {
            error_log($head.$str.$option);
        }

        // Log output
        if ($logtype & LOG_TYPE_OUT) {
            if (PHP_SAPI == 'cli') {
                log::out($head.$msg);
            } else {
                $str = htmlspecialchars($str, ENT_QUOTES);
                log::out('<pre><strong>'.$str_type.'</strong> '.print_r($msg, true).$option.'</pre>');
            }
        }

        // Log saving in bdd
        if ($context != 'system' && ($logtype & LOG_TYPE_BDD) && $type != L_DEBUG && $type != L_INFO) {
            $log = log::getInstance();
            $log->addInDb($type, $context, $str, $opt0, $opt1);
        }

        // If fatal, stop !
        if ($type == L_FATAL) {
            system::end();
        }

        return true;
    }

    /**
     *  Print var
     *  @param  string  $var    Data
     */
    function out($var) {
        $log = log::getInstance();
        if (!$log->_silent) {
            echo $var;
        }
    }

    /**
     *  Clear log
     */
    function clear() {
        $sql = "TRUNCATE {$this->_log_table}";
        return $this->bdd->execQuery($sql);
    }

    /**
     *  Get last message
     */
    function getLastMsg() {
        return $this->_last_msg;
    }
}

?>
