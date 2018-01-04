<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$file = 'http://slack.com';
$file_headers = get_headers($file);
var_dump($file_headers);
if(strcmp($file_headers[0], 'HTTP/1.1 404 Not Found') === 0){
    $exists = false;
    echo "NO";
}
else {
    $exists = true;
    echo "SI";
}

