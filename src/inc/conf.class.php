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

define('CONF_TYPE_CORE',    'core');
define('CONF_TYPE_PLUGIN',  'plugin');
define('CONF_TYPE_USER',    'user');

/**
 *  Get conf
 */
/*
function getConf() {
    $conf = conf::getInstance();
    $args = func_get_args();
    return call_user_func_array(array($conf, 'get'), $args);
}
*/

class conf
{
    // Protected
    var $bdd;
    var $data;
    var $save;

    var $_conf_table;

    /**
     *  Constuctor
     */
    function conf() {
        $this->bdd = plugins::get(PLUGIN_TYPE_DB);

        $this->_conf_table = TABLE_CONF;

        $this->data = array();
        $this->save = array();
    }

    /**
     *  Singleton
     */
    function &getInstance() {
        static $thisInstance = null;
        if (is_null($thisInstance)) {
            $thisInstance = new conf();
        }
        return $thisInstance;
    }

    /**
     *  Load all configuration
     */
    function load() {

        $sql = "SELECT  conf_type, conf_name, conf_usr_id, conf_plugin_context, conf_content_type, conf_content
                FROM    {$this->_conf_table}";
        if (!($var = $this->bdd->execQuery($sql))) {
            system::end('Error while retrieving configuration from database !');
        }

        for ($i = 0; $res = $this->bdd->nextTuple($var); $i++) {
            switch ($res['conf_type']) {
                case CONF_TYPE_CORE:
                    $this->data[$res['conf_type'].'>'.$res['conf_name']] = conf::getContent($res['conf_content_type'], $res['conf_content']);
                    break;
                case CONF_TYPE_PLUGIN:
                    list($tmp, $plugin_type, $plugin_name) = explode(':', $res['conf_plugin_context']);
                    $this->data[$res['conf_type'].':'.$plugin_type.':'.$plugin_name.'>'.$res['conf_name']] = conf::getContent($res['conf_content_type'], $res['conf_content']);
                    break;
                case CONF_TYPE_USER:
                    $this->data[$res['conf_type'].':'.$res['conf_usr_id'].'>'.$res['conf_name']] = conf::getContent($res['conf_content_type'], $res['conf_content']);
                    break;
                default:
                    system::end('Error : Conf type not found !');
            }
        }
    }

    /**
     *  Get content
     *  @param  string  $type       Type
     *  @param  mixed   $content    Content
     *  @access static
     */
    function getContent($type, $content) {
        $ret = null;
        switch ($type) {
            case 'bool':    $ret = (bool)$content;          break;
            case 'int':     $ret = (int)$content;           break;
            case 'float':   $ret = (float)$content;         break;
            case 'string':  $ret = (string)$content;        break;
            case 'array':
            case 'object':  $ret = unserialize($content);   break;
            case 'null':
            default:
        }
        return $content;
    }

    /**
     *  Get element
     *  @param  string  $name   Name
     *  @param  $mixed  $param  Id of user or plugin context
     */
    function get($name, $param = null) {

        if ($param) {
            $type = (is_numeric($param)) ? CONF_TYPE_USER : CONF_TYPE_PLUGIN;
        } else {
            $type = CONF_TYPE_CORE;
        }

        return $this->_get($name, $type, $param);
    }

    /**
     *  Set data
     *  @param  string  $tab    Array (key => value)
     *  @param  $mixed  $param  Id of user or plugin context
     *  @return true if successfull
     */
    function set($tab, $param = null) {

        if ($param) {
            $type = (is_numeric($param)) ? CONF_TYPE_USER : CONF_TYPE_PLUGIN;
        } else {
            $type = CONF_TYPE_CORE;
        }

        list($key, $value) = each($tab);
        return $this->_set($type, $key, $value, $param);
    }
    
    /**
     *  Insert new data or update
     */
    function save() {
        
        foreach ($this->save as $key => $value) {

            list($context, $name) = explode('>', $key);
            $param = explode(':', $context);

            switch ($param[0]) {
                case 'core':
                    $where = " conf_type = 'core' AND conf_name = '$name'";
                    break;
                case 'plugin':
                    $where = " conf_type = 'plugin' AND conf_plugin_context = '$context' AND conf_name = '$name'";
                    break;
                case 'user':
                    $where = " conf_type = 'user' AND conf_usr_id = '{$param[1]}' AND conf_name = '$name'";
                    break;
            }


            $qry = "UPDATE ".$this->_conf_table." SET conf_content = '".$this->bdd->quote($value)."' WHERE $where";
            $this->bdd->execQuery($qry);
        } 
    }

    /*  Test if there are unauthorized character
     *  @param  string  $name   Name
     *  @access static
     */
    function testName($name) {
        return preg_match('/^[A-Z]{1}[A-Z0-9._-]{1,31}$/i', $name);
    }

    /**
     *  Get conf
     *  @param  string  $name   Name
     *  @access protected
     */
    function _get($name, $type, $param = null) {
        $ret = null;
        $key = null;

        switch ($type) {
            case CONF_TYPE_CORE:
                $key = $type.'>'.$name;
                break;
            case CONF_TYPE_PLUGIN:
                list($tmp, $plugin_type, $plugin_name) = explode(':', $param);
                $key = $type.':'.$plugin_type.':'.$plugin_name.'>'.$name;
                break;
            case CONF_TYPE_USER:
                $key = $type.':'.$param.'>'.$name;
                break;
        }

        if (array_key_exists($key, $this->data)) {
            $ret = $this->data[$key];
        }

        return $ret;
    }

    /**
     *  Set conf
     *  @param  string  $type   Type
     *  @param  string  $name   Name
     *  @param  mixed   $value  Value
     *  @access protected
     */
    function _set($type, $name, $value, $param = null) {

        $ret = false;

        // Test value
        if (!conf::testName($name)) {
            return false;
        }

        // If value is the same, skip ! 
        $val = $this->_get($type, $name, $param);
        if ($val == $value) {
            return true;
        }

        switch ($type) {
            case CONF_TYPE_CORE:
                $key = $type.'>'.$name;
                break;
            case CONF_TYPE_PLUGIN:
                list($tmp, $plugin_type, $plugin_name) = explode(':', $param);
                $key = $type.':'.$plugin_type.':'.$plugin_name.'>'.$name;
                break;
            case CONF_TYPE_USER:
                $key = $type.':'.$param.'>'.$name;
                break;
            default:
                return false;
        }

        if (array_key_exists($key, $this->data)) {
            $this->save[$key] = $value;
        }

        return true;
    }
}

/*
$conf = new conf();
$conf->load();

//echo $conf->getCore('webmaster_email');
//echo $conf->getPlugin('plugin:', 'width');

echo $conf->get('webmaster_email');
echo '<br>';
echo $conf->get('email', 1);
echo '<br>';
echo $conf->get('width', 'plugin:obj:zenphoto');

echo '<hr>';

$conf->set(array('webmas>ter_email' => 'hugo@example.net'));
echo $conf->set(array('web>master_email' => 'hugo@example.hop.net'));
echo '<br>';
$conf->set(array('email' => 'hugo@digitalspirit.org'), 1);
echo '<br>';
$conf->set(array('height' => 241), 'plugin:obj:zenphoto');
echo '<br>';

dbug($conf->data);
dbug($conf->save);

$conf->save();

system::end();
*/

?>
