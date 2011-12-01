<?php

require_once 'Resource.php';

/**
 * Realm resource.
 *
 * @throws ResourceException If no methods are defined.
 */
class PVP extends Resource {

	protected $region;
	
	protected $methods_allowed = array(
		'teams',
		'ladder',
	);
	var $x = '';

	public function getTeamInfo($realm, $name, $size) {

		if (empty($realm)) {
			throw new ResourceException('No realms specified.');
		} elseif (empty($name)) {
			throw new ResourceException('No team name specified.');
		}
		
		$data = $this->consume('teams', array(
			'data' => $fd,
			'dataa' => $realm.'/'.$name,
			'server' => $realm,
			'name' => $name,
			'size' => $size,
			'header'=>"Accept-language: ".$this->region."\r\n"
			));
		return $data;
	}
	
	public function getLadderInfo($battlegroup, $size,$limit=null) {

		if (empty($battlegroup)) {
			throw new ResourceException('No battlegroup specified.');
		} elseif (empty($size)) {
			throw new ResourceException('No team size specified.');
		}

			$data = $this->consume('ladder', array(
			'data' => '?size='.$limit,
			'dataa' => $battlegroup.'/'.$size,
			'server' => $battlegroup,
			'size' => $size,
			'header'=>"Accept-language: ".$this->region."\r\n"
			));

		return $data;
	}
	
}
