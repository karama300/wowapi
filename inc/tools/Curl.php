<?php

class Curl {
	public $errno = CURLE_OK;
	public $error = '';

	/**
	 * Executes a curl request.
	 *
	 * @param string $url URL to make the request
	 * @param string $method Method to make (GET, POST, etc)
	 * @param array $options Various options for the request (including data)
	 * @return array Array containing the 'response' and the 'code'
	 */
	 public function pulldata($url)
	 {
				$file_handle = fopen($url, 'r');
				$response = fread($file_handle, filesize($url));
				fclose($file_handle);
				$headers = array('http_code'=>303);
				return array(
				'response'		    => stripslashes($response),
				'response_headers'  => $headers,
			);
	 }

	public function genauth($url)
	{

		$UrlPath = '/'.$url;

		$keys = array(
			'public' => '',
			'private' => ''
		);
		
		$date = date('D, d M Y G:i:s T');//date(DATE_RFC2822);
		$header = "Date: ". $date."\nAuthorization: BNET ". $keys['public'] .":". base64_encode(hash_hmac('sha1', "GET\n".$date."\n".$UrlPath."\n", $keys['private'], true))."\n";
			
			
		return $header;
	}
	public function makeRequest($url, $method='GET', $options=array(),$uri,$method) 
	{

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_TIMEOUT,		 isset($options['timeout']) ? $options['timeout'] : 10);
		curl_setopt($ch, CURLOPT_VERBOSE,		 isset($options['verbose']) ? $options['verbose'] : false);

		// Prepare data (if applicable)
		if (isset($options['data']) && !empty($options['data'])) {
			if (isset($options['data_type'])) {
				switch($options['data_type']) {
					case 'json':
						$data = json_encode($options['data']);
						break;
				}
			} else {
				if (is_array($options['data'])) {
					$data = '';
					foreach($options['data'] as $key => $value) {
						$data .= $key.'='.$value.'&';
					}
					$data = rtrim($data, '&');
				} else {
					$data = $options['data'];
				}
			}
		}

		if ($method != 'itemtooltip' && $method != 'talents')
		{
		$options['header'] .= $this->genauth($uri);
		}

		if (isset($options['headers'])) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $options['header']);
		}

		// Setup methods
		switch($method) {
			case 'GET':
				if(!empty($data)) {
					curl_setopt($ch, CURLOPT_URL, $url . '?' . $data);
				}
				break;
			case 'POST':
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				break;
			case 'PUT':
				$file_handle = fopen($data, 'r');
				curl_setopt($ch, CURLOPT_PUT, true);
				curl_setopt($ch, CURLOPT_INFILE, $file_handle);
				break;
			case 'DELETE':
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
				break;
		}

		// Execute
		$response	    = curl_exec($ch);
		$headers		= curl_getinfo($ch);
		//Deal with HTTP errors
		$this->errno	= curl_errno($ch);
		$this->error	= curl_error($ch);

		curl_close($ch);
		if ($this->errno) {
			return false;
		}else{
			return array(
				'response'		    => $response,
				'response_headers'  => $headers,
			);
		}
	}
}