<?php

require_once 'Resource.php';

/**
 * Realm resource.
 *
 * @throws ResourceException If no methods are defined.
 */
class Data extends Resource {

	protected $region;
	
	protected $methods_allowed = array(
		'races',
		'classes',
		'item',
		'achievement',
		'quests',
		'auction',
		'recipe',
	);

	
	public function getRacesInfo() 
	{
		
			$data = $this->consume('races', array(
			'data' => '',
			'dataa' => 'races',
			'server' => '',
			'name' => '',
			'header'=>"Accept-language: ".$this->region."\r\n"
			));
		return $data;
	}
	public function getQuestInfo($id) 
	{
		
			$data = $this->consume('quests', array(
			'data' => '',
			'dataa' => $id.'-quests',
			'server' => '',
			'name' => $id,
			'header'=>"Accept-language: ".$this->region."\r\n"
			));
		return $data;
	}
	
	public function getClassesInfo() 
	{
		
			$data = $this->consume('classes', array(
			'data' => '',
			'dataa' => '',
			'server' => '',
			'name' => $class,
			'header'=>"Accept-language: ".$this->region."\r\n"
			));

		return $data;
	}
	
	public function getItemInfo($itemID,$gem0=null,$gem1=null,$gem2=null,$enchant=null,$es=false) {
		
		if (empty($itemID))
		{
			throw new ResourceException('No Item ID given Given.');
		} 
		else
		{
			
			$data = $this->consume('item', array(
			'data' => '',
			'dataa' => $itemID.'',
			'server' => '',
			'name' => $itemID,
			'header'=>"Accept-language: ".$this->region."\r\n"
			));
		}
		return $data;
	}

	public function getAchievInfo($achiID) {
		
		if (empty($achiID))
		{
			throw new ResourceException('No achievement ID given Given.');
		} 
		else
		{
			$data = $this->consume('achievement', array(
			'data' => '',
			'dataa' => $achiID.'-achiv',
			'server' => '',
			'name' => $achiID,
			'header'=>"Accept-language: ".$this->region."\r\n"
			));
		}
		return $data;
	}
	
	public function GetAuction($realm,$compress=false)
	{
	
		if (empty($realm)) 
		{
			throw new ResourceException('No realms specified.');
		}
		else 
		{
			$data = $this->consume('auction', array(
			'server' => $realm,
			'type' => 'GET',
			'header'=>"Accept-language: ".$this->region."\r\n.'".($compress ? "Accept-Encoding: gzip" : "Content-Type: text/html; charset=UTF-8").""
			));
		}
		return $data;
		
	}
	
	public function GetRecipe($recipe)
	{
	
		if (empty($realm)) 
		{
			throw new ResourceException('No realms specified.');
		}
		else 
		{
			$data = $this->consume('recipe', array(
			'name' => $recipe,
			'type' => 'GET',
			'header'=>"Accept-language: ".$this->region."\r\n.'".($compress ? "Accept-Encoding: gzip" : "Content-Type: text/html; charset=UTF-8").""
			));
		}
		return $data;
		
	}
	
}
