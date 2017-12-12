<?php

namespace tests;

require('../../../autoload.php');

use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use elanders\Client\Auth;
use elanders\Client\Orders;
use elanders\Client\Products;
use elanders\ApiRequestException;
use elanders\ApiResponseException;

try
{
	$testApi = new testApi();

	$yourIdentifier	 = '<yourIdentifier>';
	$yourToken		 = '<yourToken>';

	$testApi->getToken($yourIdentifier, $yourToken);

	$transactionReference = $testApi->createOrder();

	#$testApi->getOrder($transactionReference);
	#$testApi->cancelOrder($transactionReference);
	#$testApi->getAddressOfOrder($transactionReference);
	#$testApi->changeAddressOfOrder($transactionReference);
}
catch (\elanders\ApiRequestException $ex)
{
	echo "\n";
	echo "--- FAILED ---\n";
	echo "\n";
	echo "ErrorMessage: " . $ex->getMessage() . "\n";
	echo "ErrorCode: " . $ex->getCode() . "\n";
	echo "\n";
}
catch (\elanders\ApiResponseException $ex)
{
	echo "\n";
	echo "--- FAILED ---\n";
	echo "\n";
	echo "httpResultCode: " . $ex->getHttpResultCode() . "\n";
	echo "ErrorMessage: " . $ex->apiErrorMessage() . "\n";
	echo "ErrorCode: " . $ex->apiErrorCode() . "\n";
	echo "Details: " . print_r($ex->apiErrorDetails(), true) . "\n";
	echo "\n";
}

class testApi
{

	protected $token;
	protected $transactionReference;

	/**
	 * __construct 
	 */
	public function __construct ()
	{
		$handler	 = new StreamHandler('php://stdout');
		$formatter	 = new LineFormatter(null, null, true, true);
		$formatter->includeStacktraces(true);
		$handler->setFormatter($formatter);
		$this->log	 = new Logger('elanders-api-sdk');
		$this->log->pushHandler($handler);

		$this->authObj	 = new Auth(true);
		$this->ordersObj = new Orders(true);
		$this->log->debug('Init done');
	}

	/**
	 * getToken 
	 * 
	 * @param string $identifier
	 * @param string $secret
	 * @return string Token
	 */
	public function getToken (string $identifier, string $secret)
	{
		$this->log->debug('Gathering token');

		$this->authObj->setCredentials($identifier, $secret);
		$myToken = $this->authObj->getToken();
		$this->log->debug('Got new token: ' . $myToken);

		$this->ordersObj->setAuth($myToken);

		return $myToken;
	}

	/**
	 * createOrder 
	 * 
	 * @return string transactionReference
	 */
	public function createOrder ()
	{
		$this->log->debug('Create new order');

		$orderData = json_decode(file_get_contents('testApiOrder.json'));
		$this->log->debug('Order JSON file loaded: ' . print_r($orderData, true));

		$transactionReference = $this->ordersObj->createOrder($orderData);
		$this->log->debug('New Order with ID ' . $transactionReference . ' created');

		return $transactionReference;
	}

	/**
	 * getOrders
	 * 
	 * @return array Array with orders objects
	 */
	public function getOrders ()
	{
		$this->log->debug('Requesting list of your orders');
		$orders = $this->ordersObj->getOrders();
		$this->log->debug('Orders: ' . print_r($orders, true));

		return $orders;
	}

	/**
	 * getOrder
	 * 
	 * @return array Array with orders objects
	 */
	public function getOrder (string $transactionReference)
	{
		$this->log->debug('Get data of order' . $transactionReference);
		$order = $this->ordersObj->getOrder($transactionReference);
		$this->log->debug('Orders: ' . print_r($order, true));

		return $order;
	}

	/**
	 * cancelOrder
	 * 
	 * @param string transactionReference
	 */
	public function cancelOrder (string $transactionReference)
	{
		$this->log->debug('Cancelling order ' . $transactionReference);
		$this->ordersObj->cancelOrder($transactionReference);
	}

	/**
	 * getShippingAddressOfOrder
	 * 
	 * @param string transactionReference
	 * @return object address data
	 */
	public function getAddressOfOrder (string $transactionReference)
	{
		$this->log->debug('Gathering address of order ' . $transactionReference);
		$address = $this->ordersObj->getAddressOfOrder($transactionReference);
		$this->log->debug('Address is ' . print_r($address, true));

		return $address;
	}

	/**
	 * changeAddressOfOrder
	 * 
	 * @param string $transactionReference
	 */
	public function changeAddressOfOrder (string $transactionReference)
	{
		$this->log->debug('Change address of order ' . $transactionReference);

		$addressData = json_decode(file_get_contents('testApiAddressChange.json'));
		$this->log->debug('Address JSON file loaded: ' . print_r($addressData, true));

		$addressChangeResult = $this->ordersObj->changeAddressOfOrder($transactionReference, $addressData);
		$this->log->debug('Address changed. New saved address data are: ' . $addressChangeResult);
	}

}
