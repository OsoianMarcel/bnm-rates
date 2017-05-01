<?php

namespace Osoian\BnmRates;

/**
 * Class BnmModel
 *
 * @package Osoian\BnmRates
 */
class BnmModel
{
	/**
	 * @var string
	 */
	private $code;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var float
	 */
	private $value;

	/**
	 * @var string
	 */
	private $number;

	/**
	 * @var int
	 */
	private $nominal;


	/**
	 * @return string
	 */
	public function getCode()
	{
		return $this->code;
	}

	/**
	 * @param string $code
	 * @return $this
	 */
	public function setCode($code)
	{
		$this->code = $code;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return $this
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}

	/**
	 * @return float
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @param float $value
	 * @return $this
	 */
	public function setValue($value)
	{
		$this->value = $value;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getNumber()
	{
		return $this->number;
	}

	/**
	 * @param string $number
	 * @return $this
	 */
	public function setNumber($number)
	{
		$this->number = $number;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getNominal()
	{
		return $this->nominal;
	}

	/**
	 * @param int $nominal
	 * @return $this
	 */
	public function setNominal($nominal)
	{
		$this->nominal = $nominal;

		return $this;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return sprintf('%.2f %s', $this->getValue(), $this->getCode());
	}
}