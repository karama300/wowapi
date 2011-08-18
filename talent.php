<?php
require ('info/wowdb.php');
/*

		this si an example talent return array...

*/
$mysql = $wowdb->connect('localhost', 'root', '', 'wowapi');
$db = mysql_select_db('wowapi');

if(!$mysql) die("Can´t connect to MySql!<br>".mysql_error()." ".mysql_errno());
if(!$db) die("Can´t connect to MySql Database!<br>".mysql_error()." ".mysql_errno());

require('info/talentbuilder.php');
$talents = new talents();

$x = $talents->show_talents('3','0300000000000000000032032113012223122132200000000000000000');
echo '<style> 
table{color:FFFFFF} 
</style>';
echo '<table ><tr>';
foreach ($x['talent']['trees'] as $tree => $tre)
{
	//print_r($tre);
	echo '<td width="200"><table background="http://wowroster.net/Interface/TalentFrame/'.$tre['ICON'].'.png" width="100%"><tr><td colspan=4>'.$tre['NAME'].'</td></tr>';

	//for ($start;$start<=$stop;$start++)
	for( $r = 1; $r < 7 + 1; $r++ )
	{
		echo '<tr>';
		for( $c = 1; $c < 4 + 1; $c++ )
		{
			echo '<td width="15" height="15">';
			if (isset($tre['talents'][$r][$c]['RANK']))
			{
				echo ''.$tre['talents'][$r][$c]['RANK'].'<img src="http://wowroster.net/Interface/Icons/'.$tre['talents'][$r][$c]['ICON'].'.png" width="32" height="32"></a>';
			}
			echo '</td>';
		}
		echo '</tr>';
	}
	echo '</table></td>';
}
echo '</table>';


echo '<pre>';
print_r($x);

?>