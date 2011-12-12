<?php

$ret = search_l10n(dirname(__FILE__));

echo '<pre>';
print_r($ret);

function search_l10n($root_dir, $dir = null) {

    static $tab, $tab_dir = null;
    static $cmpt = 0;

    $cmpt++;

    $hdl = dir($root_dir.$dir);
    if (!$hdl) {
        return null;
    }

    while (false !== ($item = $hdl->read())) {

        // System dir ?
        if ($item == '.' || $item == '..') {
            continue;
        }

        $obj = $dir.'/'.$item;
        if (!is_dir($root_dir.$obj)) {
            continue;
        }

        // Save
        if ($item == 'l10n') {
            $tab_dir[] = $obj;
        }

        search_l10n($root_dir, $obj);
    }

    $hdl->close();

    // Reset static tab when scan is complete !
    if ($cmpt == 1) {
        $tab = $tab_dir;
        $tab_dir = null;
    }

    $cmpt--;

    return $tab;
}


?>
