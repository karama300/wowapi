<?php

require_once 'Resource.php';

/**
 * Realm resource.
 *
 * @throws ResourceException If no methods are defined.
 */
class character extends Resource {

	protected $region;
	
	protected $methods_allowed = array(
		'character',
	);
	var $x = '';

	function build_fields($data)
	{
		$fds = explode(":",$data);
		
		$this->x='';
		foreach ($fds as $fd => $s)
		{
			switch($s)
			{
				case '1':		$this->x.= ',guild';
								#A summary of the guild that the character belongs to. If the character does not belong to a guild and this field is requested, this field will not be exposed.
				break;
				case '2':		$this->x.= ',stats';
								#A map of character attributes and stats.
				break;
				case '3':		$this->x.= ',talents';
								#A list of talent structures.
				break;
				case '4':		$this->x.= ',items';
								# list of items equipted by the character. Use of this field will also include the average item level and average item level equipped for the character.
				break;
				case '5':		$this->x.= ',reputation';
								#A list of the factions that the character has an associated reputation with.
				break;
				case '6':		$this->x.= ',titles';
								#A list of the titles obtained by the character.
				break;
				case '7':		$this->x.= ',professions';
								#A list of the character's professions. It is important to note that when this information is retrieved, it will also include the known recipes of each of the listed professions.
				break;
				case '8':		$this->x.= ',appearance';
								#A map of values that describes the face, features and helm/cloak display preferences and attributes.
				break;
				case '9':		$this->x.= ',companions';
								#A list of all of the non-combat pets obtained by the character.
				break;
				case '10':		$this->x.= ',mounts';
								#A list of all of the mounts obtained by the character.
				break;
				case '11':		$this->x.= ',pets';
								#A list of all of the combat pets obtained by the character.
				break;
				case '12':		$this->x.= ',achievements';
								#A map of achievement data including completion timestamps and criteria information.
				break;
				case '13':		$this->x.= ',progression';
								#A list of raids and bosses indicating raid progression and completedness.
				break;
				case '14':		$this->x.= ',pvp';
								#A list of battleground vistories and teams.
				break;
				case '15':		$this->x.= ',quests';
								#A list of battleground vistories and teams.
				break;
				default:
								$this->x.= '';
				break;
			}
		
		}
		return $this->x;
	}
	/**
	 * Get status results for specified realm(s).
	 *
	 * @param $char charactername, $realm realm name
	 * @return mixed
	 */
	public function getCharInfo($realm, $char, $fields) {
		if (empty($realm)) {
			throw new ResourceException('No realms specified.');
		} elseif (empty($char)) {
			throw new ResourceException('No char name specified.');
		} else {
			if ($fields !='')
			{
				$fd ='?fields='.$this->build_fields($fields);
			}
			$realm_str = $realm.'/'.$char;

			$data = $this->consume('character', array(
			'data' => $fd,
			'dataa' => $realm.'/'.$char,
			'server' => $realm,
			'name' => $char,
			'header'=>"Accept-language: ".$this->region."\r\nContent-Type: text/html; charset=UTF-8"
			));
		}
		return $data;
	}
}
