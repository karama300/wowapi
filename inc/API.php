<?php
require_once 'resource/Realm.php';
require_once 'resource/Char.php';
require_once 'resource/Guild.php';
require_once 'resource/Data.php';
require_once 'resource/Team.php';
//require_once 'tools/cache.php';

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
	//now to test and see if we have what we need
	
	public function __construct($region='us') {
		// Check for required extensions
		if (!function_exists('curl_init')) {
			throw new Exception('Curl is required for api usage.');
		}

		if (!function_exists('json_decode')) {
			throw new Exception('JSON PHP extension required for api usage.');
		}
		$this->local = strtoupper($region);
		$this->Realm = new Realm(strtoupper($region));
		$this->Char = new character(strtoupper($region));
		$this->Guild = new guild(strtoupper($region));
		$this->Data = new Data(strtoupper($region));
		$this->PvP = new PVP(strtoupper($region));
	}
	
	
}
//eof api.php
?>