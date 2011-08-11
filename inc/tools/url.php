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
	public function BuildUrl($ui,$class,$server,$name,$fields)
	{	
	
		$name = str_replace('+' , '%20' , urlencode($name));
		$server = str_replace('+' , '%20' , urlencode($server));

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
				$q = 'api/wow/arena/'.$server.'/'.$field['data'].'/'.$name.'';
			break;
			
			case 'item':
					#api/wow/data/item/38268
				$q = 'api/wow/item/'.$name.''.$fields['tts'].'';
			break;
			
			case 'gperks':
				
				$q = 'api/wow/data/guild/perks';
			break;
			
			case 'gachievments':
				
				$q = 'api/wow/data/guild/perks';
			break;
			case 'grewards':
				
				$q = 'api/wow/data/guild/rewards';
			break;
			
			case 'races':
				
				$q = 'api/wow/data/character/races';
			break;

			case 'achievement':
				$q = 'api/wow/data/achievement/'.$name.'';
			break;
			
			
			default:
			break;
		}
		if ($debug)
		{
			echo $ui.$q.'<br>';
		}
		return $ui.$q;
	}




}