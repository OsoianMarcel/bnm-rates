<?php

namespace Osoian\BnmRates\Tests;

use Osoian\BnmRates\BnmRates;
use PHPUnit\Framework\TestCase;

/**
 * Class BnmRatesTest
 * This is a functional test
 *
 * @package Osoian\BnmRates\Tests
 */
class BnmRatesTest extends TestCase
{
	/**
	 * @var BnmRates
	 */
	private $instance;

	/**
	 * {@inheritdoc}
	 */
	public function setUp()
	{
		$this->instance = new BnmRates();
	}

	/**
	 * {@inheritdoc}
	 */
	public function tearDown()
	{
		$this->instance = null;
	}

	/**
	 * Test EUR exchange rate and ro locale at specific time
	 */
	public function testEurRateRoLangAtSpecificTime()
	{
		$currency = 'EUR';
		$expectedValue = 20.9869;

		$this->instance->setLocale('ro')
			->setDate(new \DateTime('2017-04-30'));

		$rate = $this->instance->getOne($currency);

		$this->assertSame($currency, $rate->getCode());
		$this->assertSame('Euro', $rate->getName());
		$this->assertSame($expectedValue, $rate->getValue());
		$this->assertSame('978', $rate->getNumber());
		$this->assertSame(1, $rate->getNominal());

		// Test scalar value
		$scalarValue = $this->instance->getOne($currency, true);
		$this->assertSame($expectedValue, $scalarValue);
	}

	/**
	 * Test USD exchange rate and en locale today
	 */
	public function testUsdRateEnLangToday()
	{
		$currency = 'USD';

		$this->instance->setLocale('en')
			->setDate(new \DateTime());

		$rate = $this->instance->getOne($currency);

		$this->assertSame($currency, $rate->getCode());
		$this->assertSame('US Dollar', $rate->getName());
		// Check only type
		$this->assertInternalType('float', $rate->getValue());
		$this->assertSame('840', $rate->getNumber());
		$this->assertSame(1, $rate->getNominal());

		// Test scalar value
		$scalarValue = $this->instance->getOne($currency, true);
		$this->assertInternalType('float', $scalarValue);
	}

	/**
	 * Test nonexistent exchange rate
	 */
	public function testNonexistentRate()
	{
		$this->assertNull($this->instance->getOne('000'));
	}

	/**
	 * Test multiple rates (EUR, USD)
	 */
	public function testMultipleRates()
	{
		$rates = $this->instance->getMultiple(['EUR', 'USD']);

		$this->assertCount(2, $rates);
	}
}