<?php

namespace elanders;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

class Base
{

	const API_URL				 = 'https://api.pdi.elanders-germany.com/api/v1';
	const API_URL_SANDBOX		 = 'https://sandbox.api.pdi.elanders-germany.com/api/v1';
	const API_AUTH_URL		 = 'https://api.pdi.elanders-germany.com/api/authorize/';
	const API_AUTH_URL_SANDBOX = 'https://sandbox.api.pdi.elanders-germany.com/api/authorize/';

	protected $client;
	protected $sandbox;
	protected $token;
	protected $identifier;
	protected $secret;

	/**
	 * __construct
	 */
	public function __construct ()
	{
		$config			 = [];
		$this->client	 = new \GuzzleHttp\Client($config);
	}

	/**
	 * setMode
	 * 
	 * @param bool $sandbox true|false
	 */
	public function setMode ($sandbox = true)
	{
		$this->sandbox = $sandbox;
	}

	/**
	 * setAuth
	 * 
	 * @param string $user
	 * @param string $password
	 */
	public function setAuth ($token)
	{
		$this->token = $token;
	}

	/**
	 * setCredentials
	 * 
	 * @param string $identifier
	 * @param string $secret
	 */
	public function setCredentials ($identifier, $secret)
	{
		$this->identifier	 = $identifier;
		$this->secret		 = $secret;
	}

	/**
	 * callAPI
	 * 
	 * @param string $method
	 * @param string $request
	 * @param array $post
	 * @return array
	 */
	protected function callAPI ($method, $request, $post = [])
	{
		try
		{
			$url = self::API_URL . $request;

			if ($this->sandbox == true)
			{
				$url = self::API_URL_SANDBOX . $request;
			}

			$options = [
				'headers'	 => [
					'User-Agent'	 => 'elandersEBCApiSdk/1.0',
					'Accept'		 => 'application/json',
					'Authorization'	 => 'Bearer ' . $this->token
				],
				'json'		 => $post,
				'debug'		 => false
			];

			if ($this->sandbox == true)
			{
				$options['debug'] = true;
			}

			$response = $this->client->request($method, $url, $options);

			return json_decode($response->getBody()->getContents());
		}
		catch (RequestException $e)
		{
			$response = $this->statusCodeHandling($e);

			return $response;
		}
	}

	/**
	 * getAuthToken
	 * 
	 * @return string
	 */
	protected function getAuthToken ()
	{
		try
		{
			$url = self::API_AUTH_URL;

			if ($this->sandbox == true)
			{
				$url = self::API_AUTH_URL_SANDBOX;
			}

			$options = [
				'auth'	 => [
					$this->identifier,
					$this->secret
				],
				'debug'	 => false
			];

			if ($this->sandbox == true)
			{
				$options['debug'] = true;
			}

			$response = $this->client->request('GET', $url, $options);

			return json_decode($response->getBody()->getContents());
		}
		catch (RequestException $e)
		{
			$response = $this->statusCodeHandling($e);

			return $response;
		}
	}

	/**
	 * statusCodeHandling
	 * 
	 * @param type $e
	 * @return array
	 */
	protected function statusCodeHandling ($e)
	{
		$result = json_decode($e->getResponse()->getBody(true)->getContents());

		$message = $result->error;
		$code	 = $e->getResponse()->getStatusCode();

		throw new \Exception($message, $code);
	}

}
