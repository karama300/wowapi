<?php
// this classes only purpose is to build the url for the curl function
class url {

	/**********************************************
	*	This function builds the urls for the function calls
	* @params
	* $ui - the url for the api ie us.battle.net/api/wow/
	* $class - the type of data being collected
	* $server - the name of the server the info it comming from
	* $name - iter character guild or team name
	* $fields - extra data used for additional info multiple realm names
	*			character event calls and team size
	**********************************************/
	public function BuildUrl($ui,$class,$server,$name,$fields,$loc)
	{	
	
		$name = str_replace('+' , '%20' , urlencode($name));
		$server = str_replace('+' , '%20' , urlencode($server));
		$local = 'locale='.$loc;

		switch ($class)
		{
			case 'character':
				// /api/wow/character/{realm}/{name}
				$q = 'api/wow/character/'.$server.'/'.$name.$fields['data'].'';
			break;
			case 'status':

				$q = 'api/wow/realm/status?'.$fields['data'].'';
			break;
			case 'guild':
				// /api/wow/guild/{realm}/{name}
				$q = 'api/wow/guild/'.$server.'/'.$name.'/'.$fields['data'].'';
			break;
			case 'team':
				// /api/wow/arena/{realm}/{size}/{name} (size being 2v2, 3v3 or 5v5)
				$q = 'api/wow/arena/'.$field['server'].'/'.$field['size'].'/'.$field['name'].'';
			break;
			
			case 'item':
					#api/wow/data/item/38268
				$q = 'api/wow/item/'.$name.''.$fields['tts'].'';
			break;
			
			case 'gperks':
				
				$q = 'api/wow/data/guild/perks';
			break;
			
			case 'gachievments':
				
				$q = 'api/wow/data/guild/achievements';
			break;
			case 'grewards':
				
				$q = 'api/wow/data/guild/rewards';
			break;
			
			case 'races':
				
				$q = 'api/wow/data/character/races';
			break;

			case 'achievement':
				$q = 'api/wow/data/character/achievements';
			break;
			
			case 'quests':
				$q = '/api/wow/quest/'.$name.'';
			break;
			
			case 'ladder':
				$q = '/api/wow/pvp/arena/'.$field['server'].'/'.$field['size'].$fields['data'];
			break;
			
			default:
			break;
		}
		if (preg_match('/\?/', $q))
		{
			$l = '&'.$local;
		}
		else
		{
			$l = '?'.$local;
		}

		if (API_DEBUG)
		{
			echo $ui.$q.$l.'<br>';
		}
		return $ui.$q.$l;
	}




}