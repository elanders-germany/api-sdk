<?php

namespace elanders\Client;

use elanders\ApiRequestException;

class Auth extends \elanders\Base
{

	/**
	 * getToken
	 * 
	 * @return string Token
	 */
	public function getToken ()
	{
		if (trim($this->identifier) == '')
		{
			throw new ApiRequestException("identifier can't be blank. Use setCredentials() first.");
		}

		if (trim($this->secret) == '')
		{
			throw new ApiRequestException("secret can't be blank. Use setCredentials() first.");
		}

		$response = $this->getAuthToken();

		return $response->access_token;
	}

}
