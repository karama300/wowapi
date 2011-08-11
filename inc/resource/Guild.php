<?php

require_once 'Resource.php';

/**
 * Guild resource.
 *
 * @throws ResourceException If no methods are defined.
 */
class Guild extends Resource {

	protected $region;
	
	protected $methods_allowed = array(
		'guild',
		'gperks',
		'gachievments',
		'grewards',
	);
	/**
	 * Get status results for specified realm(s).
	 *
	 * @param mixed $realms String or array of realm(s)
	 * @return mixed
	 */
	public function getGuildInfo($rname, $name, $fields)
	{

		if (empty($rname)) {
			throw new ResourceException('No realms specified.');
		} elseif (empty($name)) {
			throw new ResourceException('No guild name specified.');
		} else {
			if ($fields !='')
			{
				$fd ='?fields='.$fields;
			}
			$data = $this->consume('guild', array(
			'data' => $fd,
			'dataa' => $name.'@'.$rname,
			'server' => $rname,
			'name' => $name,
			'header'=>"Accept-language: ".$this->region."\r\n"
			));
		}
		return $data;
	}
	public function getGuildperks($rname, $name, $fields)
	{
		if (empty($rname)) {
			throw new ResourceException('No realms specified.');
		} elseif (empty($name)) {
			throw new ResourceException('No guild name specified.');
		} else {
			
			$data = $this->consume('gperks', array(
			'data' => $fields,
			'dataa' => $name.'@'.$rname.'-perks',
			'server' => $rname,
			'name' => $name,
			'header'=>"Accept-language: ".$this->region."\r\n"
			));
		}
		return $data;
	}
	public function getGuildrewards($rname, $name, $fields)
	{
		if (empty($rname)) {
			throw new ResourceException('No realms specified.');
		} elseif (empty($name)) {
			throw new ResourceException('No guild name specified.');
		} else {
			
			$data = $this->consume('grewards', array(
			'data' => $fields,
			'dataa' => $name.'@'.$rname.'-rewards',
			'server' => $rname,
			'name' => $name,
			'header'=>"Accept-language: ".$this->region."\r\n"
			));
		}
		return $data;
	}
}
