<?php

namespace PhonePe\payments\v2\models\request;

class StandardCheckoutPayRequest implements \JsonSerializable
{
	private string $merchantOrderId;
	private int $amount;
	private $metaInfo;
	private array $paymentFlow;

	/**
	 * @param string $merchantOrderId
	 * @param int $amount
	 * @param $metaInfo
	 * @param array $paymentFlow
	 */
	public function __construct($merchantOrderId, $amount, $metaInfo, $paymentFlow)
	{
		$this->merchantOrderId = $merchantOrderId;
		$this->amount = $amount;
		$this->metaInfo = $metaInfo;
		$this->paymentFlow = $paymentFlow;
	}

	/**
	 * @return string
	 */
	public function getMerchantOrderId()
	{
		return $this->merchantOrderId;
	}

	/**
	 * @return int
	 */
	public function getAmount()
	{
		return $this->amount;
	}

	/**
	 * @return mixed
	 */
	public function getMetaInfo()
	{
		return $this->metaInfo;
	}

	/**
	 * @return array
	 */
	public function getPaymentFlow()
	{
		return $this->paymentFlow;
	}

	/**
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return get_object_vars($this);
	}


}