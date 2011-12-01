<?php
require_once 'resource/Realm.php';
require_once 'resource/Char.php';
require_once 'resource/Guild.php';
require_once 'resource/Data.php';
require_once 'resource/Team.php';


class WowAPI {
	/**
	 * @var \Realm Realm object instance.
	 */
	public $Realm; // realm object
	public $Char; // char object
	public $Guild; // guild Object
	public $PvP; // team Object
	public $Data; // Blizzard Data Objects..
	public $local;// = ''.strtoupper($region).'';
	public $region;// = ''.strtoupper($region).'';
	
	public function __construct($array) {
		// Check for required extensions
		if (!function_exists('curl_init')) 
		{
			throw new Exception('Curl is required for api usage.');
		}

		if (!function_exists('json_decode')) 
		{
			throw new Exception('JSON PHP extension required for api usage.');
		}
		// get our settings :)
		$this->Settings($array);
		
		$this->region = $array['region'];
		$this->local = $array['local'];
		$this->Realm = new Realm($this->local);
		$this->Char = new character($this->local);
		$this->Guild = new guild($this->local);
		$this->Data = new Data($this->local);
		$this->PvP = new PVP($this->local);
	}
	
	/*
	*	Ok its time to start setting some options....
	*	this will process the construct vars setting the construct recives..
	*/
	public function Settings($array)
	{
		$this->regionsw($array['region']);
		define(API_DEBUG, $array['debug']);
		define(API_pub_key, $array['pub_key']);
		define(API_pri_key, $array['pri_key']);
	
	}
	
	public function regionsw($region)
	{
		switch($region)
		{
			case 'en_US':
			case 'es_MX':
			case 'pt_BR':
			define(API_URI, 'http://us.battle.net/');
			break;

			case 'en_GB':
			case 'es_ES':
			case 'fr_FR':
			case 'ru_RU':
			case 'de_DE':
			define(API_URI, 'http://eu.battle.net/');
			break;

			case 'ko_kr':
			define(API_URI, 'http://kr.battle.net/');
			break;

			case 'zh_TW':
			define(API_URI, 'http://tw.battle.net/');
			break;

			case 'zh_CN':
			define(API_URI, 'http://battlenet.com.cn/');
			break;
		}
	}

}
//eof api.php
?>