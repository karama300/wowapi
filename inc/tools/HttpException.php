<?php
/**
 * HTTP Resource Exception
 */
class HttpException extends Exception {
	public function __construct($message=null, $code=500, Exception $previous = null) {
		if (is_array($message) && isset($message['status'], $message['reason'])) {
			$message = $message['status']. ": " . $message['reason'];
		} elseif($message === null) {
			$message = 'Unknown error occurred.';
		}
		parent::__construct($message, $code, $previous);
	}
		public function exception_handler($exception) { 
ob_start(); 
print_r($GLOBALS); 
print_r($exception); 
  file_put_contents('exceptions.txt', ob_get_clean(). "\n",FILE_APPEND); 
} 

}
