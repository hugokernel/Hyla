<?php
/*
 * Session Management for PHP3
 *
 * (C) Copyright 1999-2000 NetUSE GmbH
 *                    Kristian Koehntopp
 *
 * $Id: template.inc,v 1.15 2004/07/23 20:36:29 layne_weathers Exp $
 *
 */


/**
 * The template class allows you to keep your HTML code in some external files
 * which are completely free of PHP code, but contain replacement fields.
 * The class provides you with functions which can fill in the replacement fields
 * with arbitrary strings. These strings can become very large, e.g. entire tables.
 *
 * Note: If you think that this is like FastTemplates, read carefully. It isn't.
 *
 */

class Template
{
  var $classname = "Template";

  var $debug    = false;

  var $filename_comments = false;

  var $unknown_regexp = "loose";

  var $root     = ".";

  var $file     = array();

  var $varkeys  = array();

  var $varvals  = array();

  var $unknowns = "remove";

  var $halt_on_error  = "no";

  var $last_error     = "";


	/**
     *  Hyla specific 
	 */
  	var $l10n;
    var $name;
    var $dir;


    function Template($dir, $root = null, $name = null) {

        $conf = conf::getInstance();
        $this->l10n = l10n::getInstance();

        if ($this->debug & 4) {
            echo "<p><b>Template:</b> root = $root, unknowns = $unknowns</p>\n";
        }

        $this->set_root($root);
        $this->dir = $dir;
/*
dbug($dir);
dbug($root);
//        $dir = HYLA_ROOT_URL;
*/
//echo '<h1>'.HYLA_ROOT_URL.'</h1>';
        $this->set_var(array(
                'DIR_TEMPLATE'	=>	HYLA_ROOT_URL.DIR_TEMPLATE,   //.$this->root[0],
                'DIR_IMAGE'     =>  DIR_IMAGE,
                'DIR_ROOT'		=>	HYLA_ROOT_URL,
                'L10N'			=>	$conf->get('lng'),
                ));

        $this->name = $name;
        //echo '<h1>'.$root.'</h1>';
        $this->set_unknowns('remove');
    }

    function set_root($root) {
    /*
    if(ereg('/$', $root)) {
      $root = substr($root, 0, -1);
    }

    if ($this->debug & 4) {
      echo "<p><b>set_root:</b> root = $root</p>\n";
    }

    if (!is_dir($root)) {
      $this->halt("set_root: $root is not a directory.");
      return false;
    }
    */
        $this->root = (is_array($root)) ? $root : array($root);
        return $this->root;
    }


    function filename($filename) {
        if ($this->debug & 4) {
            echo "<p><b>filename:</b> filename = $filename</p>\n";
        }
        /*
        if (substr($filename, 0, 1) != "/" 
        && substr($filename, 0, 1) != "\\"
        && substr($filename, 1, 2) != ":\\"
        && substr($filename, 1, 2) != ":/"
        ) {
        $filename = $this->root."/".$filename;
        }
        */
//        echo '<p>'.$filename.'</p>';
        $filename = $this->get_file($filename);
        if (!file_exists($filename))
            $this->halt("filename: file $filename does not exist.");

        return $filename;
    }

	/*	Récupère le chemin du fichier (hugo, le 21/03/2007)
	 */
	function get_file($filename, $dir = null, $with_root = true) {
/*
        if ($dir) {
            foreach ($dir as $root) {

                // File exists in run path ?
                if (file_exists($root.$this->name.'/'.$filename)) {
//                    echo ' [ok:3] ('.$root.$this->name.'/'.$filename.') <br>';
                    return $root.$this->name.'/'.$filename;
                }

                // File exists in root path of same tpl ?
                if (file_exists($root.'default/'.$filename)) {
//                    echo ' [ok:4] ('.$root.'default/'.$filename.') <br>';
                    return $root.'default/'.$filename;
                }
            }
        }
*/
        foreach ($this->root as $root) {

            $root .= $this->dir;
//echo ' ('.$root.') - ['.$filename.']<br>';


            // File exists in run path ?
            if (file_exists($root.$this->name.'/'.$filename)) {
//                echo ' [ok:1] ('.($with_root ? $root : null).$this->name.'/'.$filename.') <br>';
                return ($with_root ? $root : null).$this->name.'/'.$filename;
            }

            // File exists in root path of same tpl ?
            if (file_exists($root.'default/'.$filename)) {
//                echo ' [ok:2] ('.($with_root ? $root : null).'default/'.$filename.') <br>';
                return ($with_root ? $root : null).'default/'.$filename;
            }
        }

//		return $filename;
        return null;
	}

    function get_special_file($dirs, $base, $filename) {
        foreach ($dirs as $dir) {
            // File exists in run path ?
            if (file_exists($dir.$base.$this->name.'/'.$filename)) {
                return $base.$this->name.'/'.$filename;
            }

            // File exists in root path of same tpl ?
            if (file_exists($dir.$base.'default/'.$filename)) {
                return $base.'default/'.$filename;
            }
        }

        return 'empty';
    }

    function set_unknowns($unknowns = "remove") {
        if ($this->debug & 4) {
            echo "<p><b>unknowns:</b> unknowns = $unknowns</p>\n";
        }
        $this->unknowns = $unknowns;
    }

    function set_file($varname, $filename = "") {
        if (!is_array($varname)) {
            if ($this->debug & 4) {
                echo "<p><b>set_file:</b> (with scalar) varname = $varname, filename = $filename</p>\n";
            }
            if ($filename == "") {
                $this->halt("set_file: For varname $varname filename is empty.");
                return false;
            }
            $this->file[$varname] = $this->filename($filename);
        } else {
            reset($varname);
            while (list($v, $f) = each($varname)) {
                if ($this->debug & 4) {
                    echo "<p><b>set_file:</b> (with array) varname = $v, filename = $f</p>\n";
                }
                if ($f == "") {
                    $this->halt("set_file: For varname $v filename is empty.");
                    return false;
                }
                $this->file[$v] = $this->filename($f);
            }
        }
        return true;
    }

    function set_block($parent, $varname, $name = "") {
        if ($this->debug & 4) {
            echo "<p><b>set_block:</b> parent = $parent, varname = $varname, name = $name</p>\n";
        }
        if (!$this->loadfile($parent)) {
            $this->halt("set_block: unable to load $parent.");
            return false;
        }
        if ($name == "") {
            $name = $varname;
        }

        // hugo, le 08/07/2006, ajou tableau
        if (is_array($varname)) {
            reset($varname);
            while (list($varnamet, $name) = each($varname)) {
                $str = $this->get_var($parent);
                $reg = "/[ \t]*<!--\s+BEGIN $varnamet\s+-->\s*?\n?(\s*.*?\n?)\s*<!--\s+END $varnamet\s+-->\s*?\n?/sm";
                preg_match_all($reg, $str, $m);

                if (!isset($m[1][0])) {
                    $this->halt("set_block: unable to set block $varnamet.");
                }

                $str = preg_replace($reg, "{" . $name . "}", $str);
                $this->set_var($varnamet, $m[1][0]);
                $this->set_var($parent, $str);
            }
        } else {
            $str = $this->get_var($parent);
            $reg = "/[ \t]*<!--\s+BEGIN $varname\s+-->\s*?\n?(\s*.*?\n?)\s*<!--\s+END $varname\s+-->\s*?\n?/sm";
            preg_match_all($reg, $str, $m);

            $str = preg_replace($reg, "{" . $name . "}", $str);
            $this->set_var($varname, $m[1][0]);
            $this->set_var($parent, $str);
        }
        return true;
    }

    function set_var($varname, $value = "", $append = false) {
        if (!is_array($varname)) {
            if (!empty($varname)) {
                if ($this->debug & 1) {
                    printf("<b>set_var:</b> (with scalar) <b>%s</b> = '%s'<br>\n", $varname, htmlentities($value));
                }
                $this->varkeys[$varname] = "/".$this->varname($varname)."/";
                if ($append && isset($this->varvals[$varname])) {
                    $this->varvals[$varname] .= $value;
                } else {
                    $this->varvals[$varname] = $value;
                }
            }
        } else {
            reset($varname);
            while (list($k, $v) = each($varname)) {
                if (!empty($k)) {
                    if ($this->debug & 1) {
                        printf("<b>set_var:</b> (with array) <b>%s</b> = '%s'<br>\n", $k, htmlentities($v));
                    }
                    $this->varkeys[$k] = "/".$this->varname($k)."/";
                    if ($append && isset($this->varvals[$k])) {
                        $this->varvals[$k] .= $v;
                    } else {
                        $this->varvals[$k] = $v;
                    }
                }
            }
        }
    }

    function clear_var($varname) {
        if (!is_array($varname)) {
            if (!empty($varname)) {
                if ($this->debug & 1) {
                    printf("<b>clear_var:</b> (with scalar) <b>%s</b><br>\n", $varname);
                }
                $this->set_var($varname, "");
            }
        } else {
            reset($varname);
            while (list($k, $v) = each($varname)) {
                if (!empty($v)) {
                    if ($this->debug & 1) {
                    printf("<b>clear_var:</b> (with array) <b>%s</b><br>\n", $v);
                    }
                    $this->set_var($v, "");
                }
            }
        }
    }

    function unset_var($varname) {
        if (!is_array($varname)) {
            if (!empty($varname)) {
            if ($this->debug & 1) {
                printf("<b>unset_var:</b> (with scalar) <b>%s</b><br>\n", $varname);
            }
            unset($this->varkeys[$varname]);
            unset($this->varvals[$varname]);
            }
        } else {
            reset($varname);
            while (list($k, $v) = each($varname)) {
                if (!empty($v)) {
                    if ($this->debug & 1) {
                        printf("<b>unset_var:</b> (with array) <b>%s</b><br>\n", $v);
                    }
                    unset($this->varkeys[$v]);
                    unset($this->varvals[$v]);
                }
            }
        }
    }

    function subst($varname) {
        $varvals_quoted = array();
        if ($this->debug & 4) {
            echo "<p><b>subst:</b> varname = $varname</p>\n";
        }
        if (!$this->loadfile($varname)) {
            $this->halt("subst: unable to load $varname.");
            return false;
        }

        // quote the replacement strings to prevent bogus stripping of special chars
        reset($this->varvals);
        while (list($k, $v) = each($this->varvals)) {
            $varvals_quoted[$k] = preg_replace(array('/\\\\/', '/\$/'), array('\\\\\\\\', '\\\\$'), $v);
        }

        $str = $this->get_var($varname);
        $str = preg_replace($this->varkeys, $varvals_quoted, $str);
        return $str;
    }

    function psubst($varname) {
        if ($this->debug & 4) {
            echo "<p><b>psubst:</b> varname = $varname</p>\n";
        }
        print $this->subst($varname);

        return false;
    }

    function parse($target, $varname, $append = false) {
        if (!is_array($varname)) {
            if ($this->debug & 4) {
                echo "<p><b>parse:</b> (with scalar) target = $target, varname = $varname, append = $append</p>\n";
            }
            $str = $this->subst($varname);
            if ($append) {
                $this->set_var($target, $this->get_var($target) . $str);
            } else {
                $this->set_var($target, $str);
            }
        } else {
            reset($varname);
            while (list($i, $v) = each($varname)) {
                if ($this->debug & 4) {
                    echo "<p><b>parse:</b> (with array) target = $target, i = $i, varname = $v, append = $append</p>\n";
                }
                $str = $this->subst($v);
                if ($append) {
                    $this->set_var($target, $this->get_var($target) . $str);
                } else {
                    $this->set_var($target, $str);
                }
            }
        }

        if ($this->debug & 4) {
            echo "<p><b>parse:</b> completed</p>\n";
        }

        $ret = $this->get_var($target);
        return $this->l10n->parse($ret);
    }

    function pparse($target, $varname, $append = false) {
        if ($this->debug & 4) {
            echo "<p><b>pparse:</b> passing parameters to parse...</p>\n";
        }
        print $this->finish($this->parse($target, $varname, $append));
        return false;
    }

    function get_vars() {
        if ($this->debug & 4) {
            echo "<p><b>get_vars:</b> constructing array of vars...</p>\n";
        }
        reset($this->varkeys);
        while (list($k, $v) = each($this->varkeys)) {
            $result[$k] = $this->get_var($k);
        }
        return $result;
    }

    function get_var($varname) {
        if (!is_array($varname)) {
            if (isset($this->varvals[$varname])) {
                $str = $this->varvals[$varname];
            } else {
                $str = "";
            }
            if ($this->debug & 2) {
                printf ("<b>get_var</b> (with scalar) <b>%s</b> = '%s'<br>\n", $varname, htmlentities($str));
            }
            return $str;
        } else {
            reset($varname);
            while (list($k, $v) = each($varname)) {
                if (isset($this->varvals[$v])) {
                    $str = $this->varvals[$v];
                } else {
                    $str = "";
                }
                if ($this->debug & 2) {
                    printf ("<b>get_var:</b> (with array) <b>%s</b> = '%s'<br>\n", $v, htmlentities($str));
                }
                $result[$v] = $str;
            }
            return $result;
        }
    }

    function get_undefined($varname) {
        if ($this->debug & 4) {
            echo "<p><b>get_undefined:</b> varname = $varname</p>\n";
        }
        if (!$this->loadfile($varname)) {
            $this->halt("get_undefined: unable to load $varname.");
            return false;
        }

        preg_match_all((("loose" == $this->unknown_regexp) ? "/{([^ \t\r\n}]+)}/" : "/{([_a-zA-Z]\\w+)}/"), $this->get_var($varname), $m);
        $m = $m[1];
        if (!is_array($m)) {
            return false;
        }

        reset($m);
        while (list($k, $v) = each($m)) {
            if (!isset($this->varkeys[$v])) {
                if ($this->debug & 4) {
                    echo "<p><b>get_undefined:</b> undefined: $v</p>\n";
                }
                $result[$v] = $v;
            }
        }

        return (count($result)) ? $result : false;
    }

    function finish($str) {
        switch ($this->unknowns) {
            case 'keep':
                break;
            case 'remove':
                $str = preg_replace((('loose' == $this->unknown_regexp) ? "/{([^ \t\r\n}]+)}/" : "/{([_a-zA-Z]\\w+)}/"), null, $str);
                break;
            case 'comment':
                $str = preg_replace((('loose' == $this->unknown_regexp) ? "/{([^ \t\r\n}]+)}/" : "/{([_a-zA-Z]\\w+)}/"), "<!-- Template variable \\1 undefined -->", $str);
                break;
        }

        return $str;
    }

    function p($varname) {
        print $this->finish($this->get_var($varname));
    }

    function get($varname) {
        return $this->finish($this->get_var($varname));
    }

    function varname($varname) {
        return preg_quote("{" . $varname . "}");
    }

    function loadfile($varname) {
        if ($this->debug & 4) {
            echo "<p><b>loadfile:</b> varname = $varname</p>\n";
        }

        if (!isset($this->file[$varname])) {
        // $varname does not reference a file so return
            if ($this->debug & 4) {
                echo "<p><b>loadfile:</b> varname $varname does not reference a file</p>\n";
            }
            return true;
        }

        if (isset($this->varvals[$varname])) {
            // will only be unset if varname was created with set_file and has never been loaded
            // $varname has already been loaded so return
            if ($this->debug & 4) {
                echo "<p><b>loadfile:</b> varname $varname is already loaded</p>\n";
            }
            return true;
        }
        $filename = $this->file[$varname];

        /* use @file here to avoid leaking filesystem information if there is an error */
        $str = implode("", @file($filename));
        if (empty($str)) {
            $this->halt("loadfile: While loading $varname, $filename does not exist or is empty.");
            return false;
        }

        if ($this->filename_comments) {
            $str = "<!-- START FILE $filename -->\n$str<!-- END FILE $filename -->\n";
        }

        if ($this->debug & 4) {
            printf("<b>loadfile:</b> loaded $filename into $varname<br>\n");
        }
        $this->set_var($varname, $str);

        return true;
    }

    function halt($msg) {
        $this->last_error = $msg;

        if ($this->halt_on_error != "no") {
        $this->haltmsg($msg);
        }

        if ($this->halt_on_error == "yes") {
        die("<b>Halted.</b>");
        }

        return false;
    }

    function haltmsg($msg) {
        printf("<b>Template Error:</b> %s<br>\n", $msg);
    }
}

?>
