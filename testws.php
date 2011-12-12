<?php

function ws($method, $param) {

    foreach ($param as $key => $value) {
        $sparam .= '&'.$key.'='.$value;
    }

    return file_get_contents('http://hyla/trunk/ws.php?method='.$method.$sparam);
}

echo '<pre>';

$json = ws('hyla.user.auth', array('username' => 'hugo', 'password' => 'toto', 'format' => 'json'));
$var = json_decode($json);

print_r($var);


$json = ws('hyla.fs.rename', array('session_key' => $var->content));

print_r($json);

?>
