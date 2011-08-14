<?php
require ('info/wowdb.php');
/*

		this si an example talent return array...

*/
$mysql = $wowdb->connect('dbhost', 'username', 'password', 'dbname');
$db = mysql_select_db('dbname');

if(!$mysql) die("Can´t connect to MySql!<br>".mysql_error()." ".mysql_errno());
if(!$db) die("Can´t connect to MySql Database!<br>".mysql_error()." ".mysql_errno());

require('info/talentbuilder.php');
$talents = new talents();

$x = $talents->show_talents('3','0300000000000000000032032113012223122132200000000000000000');

echo '<pre>';
print_r($x);

?>