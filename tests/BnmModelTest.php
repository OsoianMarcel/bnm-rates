<?php

namespace Osoian\BnmRates\Tests;

use Osoian\BnmRates\BnmModel;
use PHPUnit\Framework\TestCase;

/**
 * Class BnmModelTest
 *
 * @package Tests
 */
class BnmModelTest extends TestCase
{
	const CODE = 'EUR';
	const NAME = 'Euro';
	const VALUE = 20.9869;
	const NUMBER = '978';
	const NOMINAL = 1;

	/**
	 * @var BnmModel
	 */
	private $instance;

	/**
	 * {@inheritdoc}
	 */
	public function setUp()
	{
		$this->instance = new BnmModel();

		$this->instance->setCode(static::CODE)
			->setName(static::NAME)
			->setValue(static::VALUE)
			->setNumber(static::NUMBER)
			->setNominal(static::NOMINAL);
	}

	/**
	 * {@inheritdoc}
	 */
	public function tearDown()
	{
		$this->instance = null;
	}

	/**
	 * Test getters and setters
	 */
	public function testGetters()
	{
		$this->assertSame(static::CODE, $this->instance->getCode());
		$this->assertSame(static::NAME, $this->instance->getName());
		$this->assertSame(static::VALUE, $this->instance->getValue());
		$this->assertSame(static::NUMBER, $this->instance->getNumber());
		$this->assertSame(static::NOMINAL, $this->instance->getNominal());
	}

	/**
	 * Test magic method __toString()
	 */
	public function testToString()
	{
		$expected = sprintf('%.2f %s', static::VALUE, static::CODE);
		$this->assertSame($expected, (string)$this->instance);
	}
}