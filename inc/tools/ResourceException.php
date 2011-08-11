<?php

/**
 * Resource Exception
 */
class ResourceException extends Exception {
	public function __construct($message=null, $code=500, Exception $previous = null) {
	echo $message['status'].'<br>';
		if (is_array($message) && isset($message['status'], $message['reason'])) {
			$message = $message['status']. ": " . $message['reason'];
		} elseif($message === null) {
			$message = 'Unknown error occurred.';
		}
		parent::__construct($message, $code, $previous);
	}
	
}
