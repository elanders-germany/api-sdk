<?php

namespace elanders;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use elanders\ApiRequestException;
use elanders\ApiResponseException;

class Base
{

	const API_URL				 = 'https://api.pdi.elanders-germany.com/api/v1';
	const API_URL_SANDBOX		 = 'https://sandbox.api.pdi.elanders-germany.com/api/v1';
	const API_AUTH_URL		     = 'https://api.pdi.elanders-germany.com/api/authorize';
	const API_AUTH_URL_SANDBOX   = 'https://sandbox.api.pdi.elanders-germany.com/api/authorize';

	protected $client;
	protected $sandbox;
	protected $token;
	protected $identifier;
	protected $secret;
	protected $logger;

	/**
	 * __construct
	 */
	public function __construct (bool $sandbox = true)
	{
		$config			 = [];
		$this->client	 = new \GuzzleHttp\Client($config);

		$this->sandbox = $sandbox;
	}

	/**
	 * setCredentials
	 * 
	 * @param string $identifier
	 * @param string $secret
	 */
	public function setCredentials (string $identifier, string $secret)
	{
		if (trim($identifier) == '')
		{
			throw new ApiRequestException("identifier can't be blank");
		}

		if (trim($secret) == '')
		{
			throw new ApiRequestException("secret can't be blank");
		}

		$this->identifier	 = $identifier;
		$this->secret		 = $secret;
	}

	/**
	 * setAuth
	 * 
	 * @param string $user
	 * @param string $password
	 */
	public function setAuth (string $token)
	{
		if (trim($token) == '')
		{
			throw new ApiRequestException("token can't be blank. Maybe you want to request one first with getToken() ?");
		}

		$this->token = $token;
	}

	/**
	 * callAPI
	 * 
	 * @param string $method
	 * @param string $request
	 * @param array $post
	 * @return array
	 */
	protected function callAPI (string $method, string $request, array $post = [])
	{
		if (trim($this->token) == '')
		{
			throw new ApiRequestException("token can't be blank. Use setAuth() first.");
		}

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
			$this->handleException($e);
		}
	}

	/**
	 * getAuthToken
	 * 
	 * @return array
	 */
	protected function getAuthToken ()
	{
		if (trim($this->identifier) == '')
		{
			throw new ApiRequestException("identifier can't be blank. Use setCredentials() first.");
		}

		if (trim($this->secret) == '')
		{
			throw new ApiRequestException("secret can't be blank. Use setCredentials() first.");
		}

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
			$this->handleException($e);
		}
	}

	/**
	 * statusCodeHandling
	 * 
	 * @param object $e
	 * @throws ApiException
	 */
	protected function handleException (RequestException $e)
	{
		$result	 = json_decode($e->getResponse()->getBody(true)->getContents());
		$code	 = $e->getResponse()->getStatusCode();

		throw new ApiResponseException($code, $result, $e);
	}

}
