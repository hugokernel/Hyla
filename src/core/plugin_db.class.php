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

class plugin_db //extends plugin    Problem with log & conf class
{
    var $_query_count;
    var $_last_query;

    function plugin_db() {
//        parent::plugin();

        $this->_query_count = 0;
        $this->_last_query = null;
    }

    /**
     *  Load plugin
     */
    function load() {
        $aDsn = plugin_db::loadDsn(DSN);
        if ($aDsn) {
            if (!$this->connect($aDsn['host'], $aDsn['username'], $aDsn['password'])) {
                system::end(__('Couldn\'t connect to sql server !'));
            }

            if (!$this->select($aDsn['database'])) {
                system::end(__('Unable to use database &laquo; %s &raquo;', $aDsn['database']));
            }
        }
    }

    /**
     *  Load dsn and return an array containing data
     *  @param  string  $dsn    Dsn string
     *  @access static
     *
     *  Ex:
     *  backend://username:password@protocol+host:port//usr/db_file.db
     *  backend://username:password@host/database
     *  backend://username:password@host
     *  backend://username@host
     *  backend://host/database
     *  backend://host
     *  backend:///database
     *  backend
     */
    function loadDsn($dsn) {

        $out = array();

        if (strpos($dsn, '://') !== false) {
            list($out['backend'], $param) = explode('://', $dsn);
        } else {
            $param = $dsn;
        }

        // Get user/pass
        if (($pos = strpos($param, '@')) !== false) {
            list($auth, $param) = explode('@', $param);
            if (strpos($auth, ':') !== false) {
                list($out['username'], $out['password']) = explode(':', $auth);
            } else {
                $out['username'] = $auth;
                $out['password'] = null;
            }
        }

        // Get path
        if (strpos($param, '//') !== false) {
            list($param, $out['path']) = explode('//', $param);
            $out['path'] = '/'.$out['path'];
        }

        // Get protocol
        if (strpos($param, '+') !== false) {
            list($out['protocol'], $param) = explode('+', $param);
        }

        // Get host and port
        if (strpos($param, ':') !== false) {
            list($param, $out['port']) = explode(':', $param);
        }

        if ($param) {
            if (substr($param, 0, 1) == '/') {
                $out['database'] = substr($param, 1, strlen($param) - 1);
            } else {
                if (strpos($param, '/') !== false) {
                    list($out['host'], $out['database']) = explode('/', $param);
                } else {
                    $out['host'] = $param;
                }
            }
        }

        return $out;
    }

    /**
     *  Quote string
     *  @param  string  $str    String
     */
    function quote($str) {
        return addslashes($str);
    }

    /**
     *  Get total query count
     */
    function getQueryCount() {
        return $this->_query_count;
    }
}

/*
echo '<pre>';

$tab = array(
'backend://username:password@protocol+host:port//usr/db_file.db',
'backend://username:password@host/database',
'backend://username:password@host',
'backend://username@host',
'backend://host/database',      // <--
'backend://host',
'backend:///database',
'backend'
);

foreach ($tab as $dsn) {
    echo '<h2>'.$dsn.'</h2>';
    $out = plugin_db::loadDSN($dsn);
    print_r($out);
    echo '<hr>';
}
*/

?>
