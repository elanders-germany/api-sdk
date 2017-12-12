<?php

namespace elanders;

class ApiRequestException extends \Exception
{

	/**
	 * __construct
	 * 
	 * @param string $message
	 * @param Exception $previous
	 */
	public function __construct ($message, $previous = null)
	{
		parent::__construct($message, 0, $previous);
	}

}
