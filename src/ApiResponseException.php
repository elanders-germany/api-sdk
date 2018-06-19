<?php

namespace elanders;

class ApiResponseException extends \Exception
{

	private $httpResultCode;
	private $errorMessage;
	private $errorCode;
	private $details;

	/**
	 * __construct
	 * 
	 * @param int $httpStatusCode
	 * @param object $resultBody
	 * @param Exception $previous
	 */
	public function __construct ($httpStatusCode, $resultBody, $previous = null)
	{
		$this->httpResultCode	 = $httpStatusCode;
		$this->errorMessage		 = $resultBody->error;
		$this->errorCode		 = $resultBody->code;
		$this->details			 = isset($resultBody->details) ? $resultBody->details : null;

		parent::__construct($this->errorMessage, $this->errorCode, $previous);
	}

	/**
	 * getHttpResultCode
	 * 
	 * @return int http return code of request
	 */
	public function getHttpResultCode ()
	{
		return $this->httpResultCode;
	}

	/**
	 * apiErrorMessage
	 * 
	 * @return string Error Message returned from API
	 */
	public function apiErrorMessage ()
	{
		return $this->errorMessage;
	}

	/**
	 * apiErrorCode
	 * 
	 * @return int error Code of API that gives you more details
	 */
	public function apiErrorCode ()
	{
		return $this->errorCode;
	}

	/**
	 * apiErrorDetails
	 * 
	 * @return array if more details available this will return an array
	 */
	public function apiErrorDetails ()
	{
		return $this->details;
	}

}
