<?php

require_once dirname(__FILE__) . '/../tools/Curl.php';
require_once dirname(__FILE__) . '/../tools/url.php';
require_once dirname(__FILE__) . '/../tools/ResourceException.php';
require_once dirname(__FILE__) . '/../tools/HttpException.php';


/**
 * Resource skeleton
 * 
 * @throws ResourceException If no methods are defined.
 */
abstract class Resource {
	/**
	 * API uri for Wow's API
	 */
	var $query=array();
	var $errors=array();

	/**
	 * @var string Serve region(`us` or `eu`)
	 */
	protected $region;
	
	/**
	 * Methods allowed by this resource (or available).
	 *
	 * @var array
	 */
	protected $methods_allowed;

	/**
	 * Curl object instance.
	 *
	 * @var \Curl
	 */
	protected $Curl;

	/**
	 * @throws ResourceException If no methods are allowed
	 * @param string $region Server region(`us` or `eu`)
	 */
	public function __construct($region) 
	{
		if (empty($this->methods_allowed)) 
		{
			throw new ResourceException('No methods defined in this resource.');
		}
		$this->region = $region;
		$this->Curl = new Curl();
		$this->url = new url();
		//$this->reseterrors;
	}
	
	public function __destruct() 
	{
		if (!empty($this->errors))
		{
			echo $this->returnerror($this->errors);
			$this->reseterrors;
		}
	}

	
	function seterrors($errors)
	{
		$this->errors[] = $errors;
	}


	/**
	 * Returns all errors
	 *
	 * @return string
	 */
	function geterrors()
	{
		return implode("\n",$this->errors) . "\n";
	}


	/**
	 * Resets the stored errors
	 *
	 */
	function reseterrors()
	{
		$this->errors = array();
	}
	

	/**
	 * Consumes the resource by method and returns the results of the request.
	 *
	 * @param string $method Request method
	 * @param array $params Parameters
	 * @throws ResourceException If request method is not allowed
	 * @return array Request data
	 */
	public function consume($method, $params=array()) 
	{
		$makecache = false;
		$msg = '';
		if (!in_array($method, $this->methods_allowed)) 
		{
			throw new ResourceException('Method not allowed.', 405);
		}
		// new prity url builder ... much better then befor...
		$ui = API_URI;

		$url = $this->url->BuildUrl($ui,$method,$params['server'],$params['name'],$params,$this->region);

		$data = $this->Curl->makeRequest($url,$params['type'], $params,$url,$method);
		if ($this->Curl->errno !== CURLE_OK) 
		{
			throw new ResourceException($this->Curl->error, $this->Curl->errno);
		}
		//Battle.net returned a HTTP error code
		$x = json_decode($data['response'], true);
		if (isset($data['response_headers']) && $data['response_headers']['http_code'] != '200') 
		{
			$msg = $this->transhttpciode($data['response_headers']['http_code']);
			$this->seterrors(array('type'=>$method,'msg'=>''.$msg.'<br>'.$url.''));
		}

		if (isset($x['reason']))
		{
			$this->seterrors(array('type'=>$method,'msg'=>$x['reason']));
			$this->query['result'] = false; // over ride cache and set to false no data or no url no file lol
		}

		$data = json_decode($data['response'], true);
		$info = $data;

		return $info;
	}
	
	public function returnerror($errors)
	{
		$content = '';
		
		$content .= '
<table class="border_frame" cellpadding="0" cellspacing="1">
	<tr>
		<td class="border_color sgreenborder">
			<div class="header_text sgreenborder">Messages</div>

			<div class="sqlwindow">
				<table cellpadding="0" cellspacing="0">
					<tr>
						<th colspan="3" class="membersHeaderRight">'.(__FILE__).'</th>
					</tr>';
					foreach($errors as $num => $error)
					{
					
					$content .= '<tr>
						<td class="membersRow2">&nbsp;&nbsp;'.$num.'</td>
						<td class="membersRow2">'.$error['type'].'</td>
						<td class="membersRowRight2" style="white-space:normal;">'.$error['msg'].'</td>
					</tr>';
					}
	$content .= '</table>
		</td>
	</tr>
</table>';

		return $content;
	}
	
	public function transhttpciode($code)
	{
		switch ($code)
		{
			case '404':
				return 'A request was made to a resource that doesn\'t exist.';
			break;
			case '500':
				return 'If at first you don\'t succeed, blow it up again';			
			break;
			case '200':
				return 'Access to this API url is Restricted';			
			break;
			case '303':
				return 'Local Cache file used.';			
			break;
			
			default:
			break;
		}	
	}
	
}
