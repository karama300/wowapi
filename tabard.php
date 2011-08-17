<?php
//header( 'Content-type: text/html; charset=utf-8' );
require('inc/api_gd.php');
$api_gd = new RosterGD();
$img_dir = 'tabard/';
//$api_gd->make_image('216','228');

/*
use urlencode(json_encode($row,JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP))
to pass data to this file $row is the array or mysql row that contains emblem data
call usage on phpfile as image
tabard.php?data=' . urlencode(json_encode($row,JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP)) . '


*/
$data = isset($_GET['data']) ? $_GET['data'] : false;

if( !$data || $data == '' )
{
$data['emblem_border'] = '03';
$data['emblem_icon'] = '25';
$data['emblem_icon_color'] = 'ffdfa55a';
$data['emblem_border_color'] = 'fff9cc30';
$data['emblem_bg_color'] = 'ff860f9a';

}
else
{

// Decode and convert data into an array
$data = urldecode($data);
$data = stripslashes($data);
$data = json_decode($data, true);
}


//*


// emblem_icon  emblem_icon_color  emblem_border  emblem_border_color  emblem_bg_color 
$filename1 = $img_dir.'bg_00.png';
$filename2 = $img_dir.'border_'.$data['emblem_border'].'.png';
$filename3 = $img_dir.'emblem_'.$data['emblem_icon'].'.png';
$shadow1 = $img_dir.'shadow_00.png';
$shadow2 = $img_dir.'overlay_00.png';
$f='0';
$faction = array('0'=>''.$img_dir.'ring-alliance.png','1'=>''.$img_dir.'ring-horde.png');
$iconColor=$data['emblem_icon_color'];
$borderColor=$data['emblem_border_color'];
$backgroundColor=$data['emblem_bg_color'];
$savename1 = 'temp1.png';
$savename2 = 'temp2.png';
$savename3 = 'temp3.png';

	function hex2RGB($hexStr, $returnAsString = false, $seperator = ',') 
	{
		$hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
		$rgbArray = array();
		if (strlen($hexStr) == 6)
		{ //If a proper hex code, convert using bitwise operation. No overhead... faster
			$colorVal = hexdec($hexStr);
			$rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
			$rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
			$rgbArray['blue'] = 0xFF & $colorVal;
		}
		elseif (strlen($hexStr) == 3)
		{ //if shorthand notation, need some string manipulations
			$rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
			$rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
			$rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
		} else
		{
			return false; //Invalid hex color code
		}
		return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray; // returns the rgb string or the associative array
	}

	function makeimages($filename,$savename,$backgroundColor)
	{
		global $api_gd;
		$api_gd->make_image('216','216');
		$api_gd->combine_image( $filename , 0 , 0 , 0 ,0 , '' , '' );
		$c1 =hex2RGB($backgroundColor, $returnAsString = false, $seperator = ',');
		//echo '<pre>';
		//print_r($c1);
		imagefilter($api_gd->im, IMG_FILTER_COLORIZE, $c1['red'], $c1['green'], $c1['blue']);
		imagepng($api_gd->im, $savename);
		imagedestroy($api_gd->im);	
	}
$alpha1 = substr($backgroundColor, 0, 2);     
$color1 = substr($backgroundColor, 2);     
makeimages($filename1,$savename1,$color1);
$alpha2 = substr($borderColor, 0, 2);     
$color2 = substr($borderColor, 2);
makeimages($filename2,$savename2,$color2);
$alpha3 = substr($iconColor, 0, 2);     
$color3 = substr($iconColor, 2);
makeimages($filename3,$savename3,$color2);

$api_gd->make_image('216','228');

$api_gd->combine_image( $faction[$f] , 0 , 0 , 0 ,0, '' , '' );
$api_gd->combine_image( $shadow1 , 15 , 18 , 0 ,0, '' , '' );

$api_gd->combine_image( $savename1 , 15 , 18 , 0 ,0, '' , '' );
$api_gd->combine_image( $savename2 , 28 , 31 , 0 ,0 , '' , '' );
$api_gd->combine_image( $savename3 , 34 , 37 , 0 ,0 , '' , '' );
$api_gd->combine_image( $shadow2 , 15 , 18 , 0 ,0, '' , '' );
$api_gd->get_image('png');
$api_gd->finish();
//*/
?>