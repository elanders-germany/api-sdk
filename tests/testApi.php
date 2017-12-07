<?php

namespace test;

require('../../../autoload.php');

use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use elanders\Client\Auth;
use elanders\Client\Orders;
use elanders\Client\Products;

try
{
	$yourIdentifier	 = '<yourIdentifier>';
	$yourToken		 = '<yourToken>';

	$handler	 = new StreamHandler('php://stdout');
	$formatter	 = new LineFormatter(null, null, true, true);
	$formatter->includeStacktraces(true);
	$handler->setFormatter($formatter);
	$log		 = new Logger('elanders-api-sdk');
	$log->pushHandler($handler);

	$test = new test($log);
	$test->getToken($yourIdentifier, $yourToken);

	$transactionReference = $test->createOrder();

	$test->getOrders();

	$test->cancelOrder($transactionReference);
	$test->getShippingAddressOfOrder($transactionReference);
}
catch (\Exception $ex)
{
	$message = "Error: " . $ex->getMessage() . ' - Code: ' . $ex->getCode() . "\n";
	$log->error($message);
}

class test
{

	protected $token;
	protected $transactionReference;

	/**
	 * __construct 
	 */
	public function __construct ($log)
	{
		$this->log = $log;

		$this->authObj = new Auth();
		$this->authObj->setMode(true);

		$this->ordersObj = new Orders();
		$this->ordersObj->setMode(true);

		$this->log->debug('Init done');
	}

	/**
	 * getToken 
	 * 
	 * @param type $identifier
	 * @param type $secret
	 * @return type
	 */
	public function getToken ($identifier, $secret)
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
	 * @return array of orders
	 */
	public function getOrders ()
	{
		$this->log->debug('Requesting list of your orders');
		$orders = $this->ordersObj->getOrders();
		$this->log->debug('Orders: ' . print_r($orders, true));
		return $orders;
	}

	/**
	 * cancelOrder
	 * 
	 * @param  $transactionReference
	 */
	public function cancelOrder ($transactionReference)
	{
		$this->log->debug('Cancelling order ' . $transactionReference);
		$this->ordersObj->cancelOrder($transactionReference);
	}

	/**
	 * getShippingAddressOfOrder
	 * 
	 * @param type $transactionReference
	 */
	public function getShippingAddressOfOrder ($transactionReference)
	{
		$this->log->debug('Gathering address of order ' . $transactionReference);
		$address = $this->ordersObj->getReceipientAddressOfOrder($transactionReference);
		$this->log->debug('Address is ' . print_r($address, true));
		return $address;
	}

}
