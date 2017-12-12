<?php

namespace elanders\Client;

use elanders\ApiRequestException;

class Orders extends \elanders\Base
{

	/**
	 * createOrder
	 * 
	 * @param array $orderData
	 * @return string Transaction reference
	 */
	public function createOrder (array $orderData)
	{
		if (empty($orderData) === true)
		{
			throw new ApiRequestException("orderData can't be empty");
		}

		$url		 = '/orders';
		$response	 = $this->callAPI('POST', $url, $orderData);

		return $response->transactionReference;
	}

	/**
	 * getOrders
	 * 
	 * @return array Array of order objects
	 */
	public function getOrders ()
	{
		$url		 = '/orders';
		$response	 = $this->callAPI('GET', $url);

		return $response->orders;
	}

	/**
	 * getOrder
	 * 
	 * @param string $transactionReference
	 * @return object Object with order data
	 */
	public function getOrder (string $transactionReference)
	{
		if (trim($transactionReference) == '')
		{
			throw new ApiRequestException("transactionReference can't be blank");
		}

		$url		 = '/orders/' . $transactionReference;
		$response	 = $this->callAPI('GET', $url);

		return $response;
	}

	/**
	 * cancelOrder
	 * 
	 * @param int $transactionReference
	 * @return bool True on success
	 */
	public function cancelOrder (string $transactionReference)
	{
		if (trim($transactionReference) == '')
		{
			throw new ApiRequestException("transactionReference can't be blank");
		}

		$url		 = '/orders/' . $transactionReference;
		$response	 = $this->callAPI('DELETE', $url);

		return true;
	}

	/**
	 * getAddressOfOrder
	 * 
	 * @param string $transactionReference
	 * @return array Array with address data
	 */
	public function getAddressOfOrder ($transactionReference)
	{
		if (trim($transactionReference) == '')
		{
			throw new ApiRequestException("transactionReference can't be blank");
		}

		$url		 = '/orders/' . $transactionReference . '/orderAddress';
		$response	 = $this->callAPI('GET', $url);

		return $response;
	}

	/**
	 * changeAddressOfOrder
	 * 
	 * @param string $transactionReference
	 * @param array $addressData
	 * @return true True on success
	 */
	public function changeAddressOfOrder ($transactionReference, $addressData)
	{
		if (trim($transactionReference) == '')
		{
			throw new ApiRequestException("transactionReference can't be blank");
		}

		if (empty($addressData) === true)
		{
			throw new ApiRequestException("addressData can't be empty");
		}

		$url		 = '/orders/' . $transactionReference . '/orderAddress';
		$response	 = $this->callAPI('PUT', $url, $addressData);

		return true;
	}

}
