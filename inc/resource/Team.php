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
	);
	var $x = '';

	/**
	 * Get status results for specified realm(s).
	 *
	 * @param $char charactername, $realm realm name
	 * @return mixed
	 */
	public function getTeamInfo($realm, $name, $fields) {

		if (empty($realm)) {
			throw new ResourceException('No realms specified.');
		} elseif (empty($name)) {
			throw new ResourceException('No char name specified.');
		} else {
			if ($fields !='')
			{
				$fd ='?fields='.$fields;
			}

			$data = $this->consume('teams', array(
			'data' => $fd,
			'dataa' => $realm.'/'.$char,
			'server' => $realm,
			'name' => $char,
			'header'=>"Accept-language: ".$this->region."\r\n"
			));
		}
		return $data;
	}
}
