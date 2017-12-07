<?php

namespace elanders\Client;

class Orders extends \elanders\Base
{

	/**
	 * createOrder
	 * 
	 * @param array $orderData
	 * @return int
	 */
	public function createOrder ($orderData)
	{
		try
		{
			$url		 = '/orders';
			$response	 = $this->callAPI('POST', $url, $orderData);

			return $response->transactionReference;
		}
		catch (RequestException $e)
		{
			$response = $this->StatusCodeHandling($e);

			return $response;
		}
	}

	/**
	 * getOrders
	 * 
	 * @return array of order objects
	 */
	public function getOrders ()
	{
		try
		{
			$url		 = '/orders';
			$response	 = $this->callAPI('GET', $url);

			return $response->orders;
		}
		catch (RequestException $e)
		{
			$response = $this->StatusCodeHandling($e);

			return $response;
		}
	}

	/**
	 * cancelOrder
	 * 
	 * @param int $transactionReference
	 * @return array
	 */
	public function cancelOrder ($transactionReference)
	{
		try
		{
			$url		 = '/orders/' . $transactionReference;
			$response	 = $this->callAPI('DELETE', $url);

			return true;
		}
		catch (RequestException $e)
		{
			$response = $this->StatusCodeHandling($e);

			return $response;
		}
	}

	/**
	 * getReceipientAddressOfOrder
	 * 
	 * @param int $transactionReference
	 * @return array
	 */
	public function getReceipientAddressOfOrder ($transactionReference)
	{
		try
		{
			$url		 = '/orders/' . $transactionReference . '/recipientAddress';
			$response	 = $this->callAPI('GET', $url);

			return $response;
		}
		catch (RequestException $e)
		{
			$response = $this->StatusCodeHandling($e);

			return $response;
		}
	}

	/**
	 * changeReceipientAddressOfOrder
	 * 
	 * @param type $transactionReference
	 * @return type
	 */
	public function changeReceipientAddressOfOrder ($transactionReference, $addressData)
	{
		try
		{
			$url		 = '/orders/' . $transactionReference . '/recipientAddress';
			$response	 = $this->callAPI('GET', $url, $addressData);

			return $response;
		}
		catch (RequestException $e)
		{
			$response = $this->StatusCodeHandling($e);

			return $response;
		}
	}

}
