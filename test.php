<?php

header( 'Content-type: text/html; charset=utf-8' );
include 'inc/API.php';
$api = new WowAPI('us');

$test = $api->Char->getCharInfo('Zangarmarsh','Ulminia','1:2:3:4:5:6:7:8:9:10:11:12:13');

echo'<pre>';
print_r($test);
echo '</pre>';