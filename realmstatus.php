<?php
/**
 * WoWRoster.net WoWRoster
 *
 * Realmstatus Image generator
 *
 * @copyright  2002-2011 WoWRoster.net
 * @license	http://www.gnu.org/licenses/gpl.html   Licensed under the GNU General Public License v3.
 * @version	SVN: $Id: realmstatus.php 2327 2011-06-14 21:55:57Z ulminia@gmail.com $
 * @link	   http://www.wowroster.net
 * @since	  File available since Release 1.03
 * @package	WoWRoster
 * @subpackage RealmStatus
 */

// WOW Server Status
// Version 3.2
// Copyright 2005 Nick Schaffner
// http://53x11.com

// EDITED BY: http://www.wowroster.net for use in wowroster
// Most other changes by Zanix

function statussw($status)
{
	switch ($status)
	{
		case '0':
			$q = 'error';
		break;
		case '1':
			$q = 'up';
		break;
		case '2':
			$q = 'down';
		break;
		case 'false':
			$q = 'error';
		break;
		default:
		break;
	}
		
	return $q;
}
//==========[ GET FROM CONF.PHP ]====================================================
$config = array(
	'rs_top' => '',
	'rs_wide' => '',
	'rs_left' => '',
	'rs_middle' => '',
	'rs_right' => '',
	'rs_display'=> 'image',
	'rs_timer'=> '10',
	'rs_font_server'=> 'GREY.TTF',
	'rs_size_server'=> '20',
	'rs_color_server'=> '#FFFFFF',
	'rs_color_shadow'=> '#000000',
	'rs_font_type'=> 'visitor.ttf',
	'rs_size_type'=> '10',
	'rs_color_rppvp'=> '#EBDBA2',
	'rs_color_pve'=> '#EBDBA2',
	'rs_color_pvp'=> '#CC3333',
	'rs_color_rp'=> '#33CC33',
	'rs_color_unknown'=> '#860D02',
	'rs_font_pop'=> 'visitor.ttf',
	'rs_size_pop'=> '10',
	'rs_color_low'=> '#33CC33',
	'rs_color_medium'=> '#EBDBA2',
	'rs_color_high'=> '#CC3333',
	'rs_color_max'=> '#CC3333',
	'rs_color_error'=> '#646464',
	'rs_color_offline'=> '#646464',
	'rs_color_full'=> '#CC3333',
	'rs_color_recommended'=> '#33CC33',
'rs' => array(
	'ERROR' => 'Error',
	'NOSTATUS' => 'No Status',
	'UNKNOWN' => 'Unknown',
	'RPPVP' => 'RP-PvP',
	'PVE' => 'Normal',
	'PVP' => 'PvP',
	'RP' => 'RP',
	'OFFLINE' => 'Offline',
	'LOW' => 'Low',
	'MEDIUM' => 'Medium',
	'HIGH' => 'High',
	'MAX' => 'Max',
	'RECOMMENDED' => 'Recommended',
	'FULL' => 'Full')
);
if( isset($_GET['r']) )
{
	list($region, $realmname) = explode('-', urldecode(trim(stripslashes($_GET['r']))), 2);
	$region = strtoupper($region);
}
elseif( isset($realmname) )
{
	list($region, $realmname) = explode('-', trim(stripslashes($realmname)), 2);
	$region = strtoupper($region);
}
else
{
	$realmname = '';
}

if( isset($_GET['d']) )
{
	$generate_image = ($_GET['d'] == '0' ? false : true);
}
elseif( isset($config['rs_display']) )
{
	$generate_image = ($config['rs_display'] == 'image' ? true : false);
}
else
{
	$generate_image = true;
}
include 'inc/API.php';
$api = new WowAPI($region);

$source = 'http://' . $region . '.battle.net/api/wow/realm/status?realm=' . $realmname . '';
//echo '=='.$source.'==<br>';
$r = $api->Realm->getRealmStatus($realmname);
//print_r($r);
	$d = $r['realms']['0'];
	//print_r($d);
//==========[ OTHER SETTINGS ]=========================================================

// Path to image folder
$image_path = 'realmstatus/';

	$realmData['server_name']   = $realmname;
	$realmData['server_region'] = '';
	$realmData['servertype']	= '';
	$realmData['serverstatus']  = '';
	$realmData['serverpop']	 = '';
	$realmData['timestamp']	 = '0';


//==========[ STATUS GENERATION CODE ]=================================================


// Check timestamp, update when ready
$current_time = time();

	if( $d !== false )
	{
		$realmType = str_replace('(', '',$d['type']);
		$realmType = str_replace(')', '',$realmType);

		$realmData['server_region'] = $region;
		$realmData['servertype']    = strtoupper($realmType);
		$realmData['serverstatus']  = strtoupper(statussw($d['status']));
		$realmData['serverpop']     = strtoupper($d['population']);

		$err = 0;
	}
	else
	{
		$err = 1;
	}

//==========[ "NOW, WHAT TO DO NEXT?" ]================================================


// Error control
if( $realmData['serverstatus'] == 'DOWN' || $realmData['serverstatus'] == 'MAINTENANCE' || $realmData['serverpop'] == 'N/A')
{
	$realmData['serverstatus'] = 'DOWN';
	$realmData['serverpop'] = 'OFFLINE';
	$realmData['serverpopcolor'] = $config['rs_color_error'];
}

// Check to see if data from the DB is non-existant
if( empty($realmData['serverstatus']) || empty($realmData['serverpop']) )
{
	$err = 1;
}
else
{
	$err = 0;
}

// Set to Unknown values upon error
if( $err )
{
	$realmData['servertype'] = 'UNKNOWN';
	$realmData['serverstatus'] = 'UNKNOWN';
	$realmData['serverpop'] = 'NOSTATUS';
	$realmData['serverpopcolor'] = $config['rs_color_error'];
	$realmData['servertypecolor'] = $config['rs_color_error'];
	$realmData['servertype'] = ($realmData['servertype'] != '' ? $realmData['servertype'] : '');
}
else
{
	if( $realmData['serverpop'] == ' ' )
	{
		$realmData['serverpopcolor'] = $config['rs_color_low'];
	}
	else
	{
		$realmData['serverpopcolor'] = $config['rs_color_' . strtolower($realmData['serverpop'])];
	}
	$realmData['servertypecolor'] = $config['rs_color_' . strtolower($realmData['servertype'])];
	$realmData['serverpop'] = $realmData['serverpop'];
	$realmData['servertype'] = ($realmData['servertype'] != '' ? $realmData['servertype'] : '');
}

// Generate image or text?
if( $generate_image )
{
	img_output($realmData, $err, $image_path);
}
else
{
	echo text_output($realmData);
}

return;

//==========[ TEXT OUTPUT MODE ]=======================================================
function text_output( $realmData )
{
	global $config;

	// If there is no data, then we want to output blank text
	$realmData['servertype'] = $realmData['servertype'] != '' ? $config['rs'][$realmData['servertype']] : $realmData['servertype'];

	$outtext = '
<div style="position:relative;width:272px;height:35px;font-family:arial;font-weight:bold;background:transparent url(realmstatus/background.png) no-repeat;">
	<div style="position:absolute;width:272px;height:35px;background:transparent url(realmstatus/' . strtolower($realmData['serverpop']) . '.png) no-repeat;">
		<div style="position:absolute;bottom:-1px;left:30px;color:' . $config['rs_color_server'] . ';' . ($config['rs_color_shadow'] ? 'text-shadow:' . $config['rs_color_shadow'] . ' 1px 1px 0;' : '') . 'font-size:24px;">' . $realmData['server_name'] . '</div>
		<div style="position:absolute;bottom:3px;right:5px;color:' . $realmData['serverpopcolor'] . ';' . ($config['rs_color_shadow'] ? 'text-shadow:' . $config['rs_color_shadow'] . ' 1px 1px 0;' : '') . 'font-size:10px;">' . $config['rs'][$realmData['serverpop']] . '</div>
		<div style="position:absolute;bottom:22px;left:35px;color:' . $realmData['servertypecolor'] . ';' . ($config['rs_color_shadow'] ? 'text-shadow:' . $config['rs_color_shadow'] . ' 1px 1px 0;' : '') . 'font-size:10px;">' . $realmData['servertype'] . '</div>
		<div style="position:absolute;bottom:2px;left:1px;width:32px;height:32px;background:transparent url(realmstatus/' . strtolower($realmData['serverstatus']) . '-icon.png) no-repeat;"></div>
	</div>
</div>
';
	return $outtext;
}

//==========[ IMAGE GENERATOR ]========================================================


function img_output( $realmData , $err , $image_path )
{
	global $config;

	$vadj = 0;

	$serverfont = $config['rs_font_server'];
	$typefont = $config['rs_font_type'];
	$serverpopfont = $config['rs_font_pop'];

	require('inc/api_gd.php');
	$api_gd = new RosterGD();

	$shadow = array('color' => $config['rs_color_shadow'], 'distance' => 1, 'direction' => 45, 'spread' => 1);

	$bkg_img = 'realmstatus/background.png';
	$pop_img = 'realmstatus/' . strtolower($realmData['serverpop']) . '.png';
	$arrow_img = 'realmstatus/' . strtolower($realmData['serverstatus']) . '-icon.png';

	$bkg_img_info = getimagesize($bkg_img);
	$api_gd->make_image($bkg_img_info[0], $bkg_img_info[1]);
	$api_gd->combine_image($bkg_img, 0, 0);

	// If there is no data, then we want to output blank text
	$realmData['servertype'] = $realmData['servertype'] != '' ? $config['rs'][$realmData['servertype']] : $realmData['servertype'];

	$api_gd->write_text($config['rs_size_type'], 0, 30, 8, $realmData['servertypecolor'], 0, $typefont, $realmData['servertype'], 'left', array(), $shadow);
	$api_gd->write_text($config['rs_size_pop'], 0, 267, 30, $realmData['serverpopcolor'], 0, $serverpopfont, $config['rs'][$realmData['serverpop']], 'right', array(), $shadow);
	$api_gd->write_text($config['rs_size_server'], 0, 30, 30, $config['rs_color_server'], 0, $serverfont, $realmData['server_name'], 'left', array(), $shadow);

	$api_gd->combine_image($pop_img, 0, 0);
	$api_gd->combine_image($arrow_img, 1, 1);

	$api_gd->get_image('png');
	$api_gd->finish();

}
