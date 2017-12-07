<?php

namespace elanders\Client;

class Auth extends \elanders\Base
{

	/**
	 * getToken
	 * 
	 * @param type $orderId
	 * @return type
	 */
	public function getToken ()
	{
		try
		{
			$response = $this->getAuthToken();

			return $response->access_token;
		}
		catch (RequestException $e)
		{
			$response = $this->StatusCodeHandling($e);

			return $response;
		}
	}

}
