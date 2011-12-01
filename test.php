<?php

header( 'Content-type: text/html; charset=utf-8' );
include 'inc/API.php';
$conf = array(
	'pub_key' => '',
	'pri_key' => '',
	'debug' => true,
	'region' => 'en_US',	// this is for the url you want to make requests from	
	'local' => 'es_MX',		// thsi is the language you want the data translated in to
	);
$api = new WowAPI($conf);

//$test = $api->Char->getCharInfo('Zangarmarsh','Ulminia','1:2:3:4:5:6:7:8:9:10:11:12:13');
$test = $api->Guild->getGuildperks('Zangarmarsh','Eternal Sanctum','perks');
//Eternal Sanctum
//

echo'<pre>';
print_r($test);
echo '</pre>';