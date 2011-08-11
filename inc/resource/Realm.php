<?php

require_once 'Resource.php';

/**
 * Realm resource.
 *
 * @throws ResourceException If no methods are defined.
 */
class Realm extends Resource {

	protected $region;
	
	protected $methods_allowed = array(
		'status'
	);

	/**
	 * Get status results for all realms.
	 *
	 * @return array
	 */
	public function getAllRealmStatus() {
		return $this->consume('status');
	}

	/**
	 * Get status results for specified realm(s).
	 *
	 * @param mixed $realms String or array of realm(s)
	 * @return mixed
	 */
	public function getRealmStatus($realms = array()) {
		if (empty($realms)) {
			throw new ResourceException('No realms specified.');
		} elseif (!is_array($realms)) {
			$data = $this->consume('status', array(
				'data' => 'realm='.$realms
			));
		} else {
			$realm_str = '';
			foreach($realms as $key => $realm) {
				$realm_str .= (!$key ? '' : '&') . 'realm=' . urlencode($realm);
			}
			$data = $this->consume('status', array(
				'data' => $realm_str
			));
		}
		return $data;
	}
}
