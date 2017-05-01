<?php

namespace Osoian\BnmRates;

/**
 * Class BnmRates
 *
 * @package Osoian\BnmRates
 */
class BnmRates
{
	const REQUEST_TIMEOUT_SEC = 2;

	/**
	 * @var array[]
	 */
	private $cache = [];

	/**
	 * @var \DateTime
	 */
	private $date;

	/**
	 * @var string
	 */
	private $locale;

	/**
	 * @var bool
	 */
	private $assocResult = false;

	/**
	 * @var \Closure
	 */
	private $assocClosure;

	/**
	 * @var array
	 */
	private $availableLocales = ['en', 'ro', 'ru'];


	/**
	 * BnmRates constructor
	 */
	public function __construct()
	{
		$this->setDate(new \DateTime());
		$this->setLocale($this->availableLocales[0]);

		/**
		 * Set default assocClosure
		 *
		 * @param BnmModel $rate
		 * @return mixed
		 */
		$this->assocClosure = function ($rate) {
			return $rate->getCode();
		};
	}

	/**
	 * Set date that is used to get exchange rates
	 *
	 * @param \DateTime $date
	 * @return $this
	 */
	public function setDate(\DateTime $date)
	{
		$this->date = $date;

		return $this;
	}

	/**
	 * Get date that is used to get exchange rates
	 *
	 * @return \DateTime
	 */
	public function getDate()
	{
		return $this->date;
	}

	/**
	 * Get locale (ISO 639-1)
	 *
	 * @return string
	 */
	public function getLocale()
	{
		return $this->locale;
	}

	/**
	 * Set locale (ISO 639-1)
	 *
	 * @param string $locale Locale name (ISO 639-1) (Example: ro|ru|en)
	 * @param mixed|null $fallback Fallback locale that will be used if locale not exists
	 * @throws \InvalidArgumentException Throws an exception if no one locale is available
	 * @return $this
	 */
	public function setLocale($locale, $fallback = null)
	{
		if (in_array($locale, $this->availableLocales, true)) {
			$this->locale = $locale;
			return $this;
		}

		$locale = $fallback;
		if (in_array($locale, $this->availableLocales, true)) {
			$this->locale = $locale;
			return $this;
		}

		throw new \InvalidArgumentException(sprintf('Locale "%s" is not available', $locale));
	}

	/**
	 * Check if associative result is activated
	 *
	 * @return bool
	 */
	public function isAssocResult()
	{
		return $this->assocResult;
	}

	/**
	 * Activate or deactivate associative result
	 *
	 * @param bool $assocResult
	 * @return $this
	 */
	public function setAssocResult($assocResult)
	{
		$this->assocResult = $assocResult;

		return $this;
	}

	/**
	 * Get assoc closure
	 *
	 * @return \Closure
	 */
	public function getAssocClosure()
	{
		return $this->assocClosure;
	}

	/**
	 * Set assoc closure
	 * The default \Closure return $rate->getCode()
	 *
	 * @param \Closure $assocClosure
	 * @return BnmRates
	 */
	public function setAssocClosure(\Closure $assocClosure)
	{
		$this->assocClosure = $assocClosure;

		return $this;
	}

	/**
	 * Get one exchange rate
	 *
	 * @param string $currencyCode
	 * @param bool $isScalar Set true to get only value
	 * @return float|BnmModel|null
	 */
	public function getOne($currencyCode, $isScalar = false)
	{
		$rates = $this->getExchangeRates();

		if (!isset($rates[$currencyCode])) {
			return null;
		}

		$rate = $rates[$currencyCode];

		if ($isScalar) {
			return $rate->getValue();
		}

		return $rate;
	}

	/**
	 * Get multiple exchange rates
	 *
	 * @param array $currencyCodes
	 * @return BnmModel[]
	 */
	public function getMultiple(array $currencyCodes)
	{
		$results = [];

		$assocClosure = $this->getAssocClosure();

		foreach ($currencyCodes as $code) {
			if ($rate = $this->getOne($code)) {
				if (!$this->isAssocResult()) {
					$results[] = $rate;
				} else {
					$results[$assocClosure($rate)] = $rate;
				}
			}
		}

		return $results;
	}

	/**
	 * Get all exchange rates
	 *
	 * @return BnmModel[]
	 */
	public function getAll()
	{
		if (!$this->isAssocResult()) {
			return array_values($this->getExchangeRates());
		}

		$assocClosure = $this->getAssocClosure();

		$results = [];
		foreach ($this->getExchangeRates() as $rate) {
			$results[$assocClosure($rate)] = $rate;
		}

		return $results;
	}

	/**
	 * Get exchange rates from BNM site
	 *
	 * @return BnmModel[] Collection of BnmModel
	 */
	private function getExchangeRates()
	{
		$date = $this->getDate()->format('d.m.Y');
		$locale = $this->getLocale();
		$cacheKey = $date . '-' . $locale;

		// Check the cache
		if (isset($this->cache[$cacheKey])) {
			return $this->cache[$cacheKey];
		}

		$response = $this->requestRemoteServer($date, $locale);
		if (!$response) {
			return [];
		}

		$rates = $this->parseRatesFromResponse($response);
		if (empty($rates)) {
			return [];
		}

		return $this->cache[$cacheKey] = $rates;
	}

	/**
	 * Parse server response
	 *
	 * @param string $response Server response
	 * @return BnmModel[]
	 */
	private function parseRatesFromResponse($response)
	{
		$results = [];

		$xmlElements = new \SimpleXMLElement($response);
		foreach ($xmlElements as $element) {
			$code = (string)$element->CharCode;

			$model = new BnmModel();
			$model->setCode($code)
				->setValue((float)$element->Value)
				->setName((string)$element->Name)
				->setNumber((string)$element->NumCode)
				->setNominal((int)$element->Nominal);

			$results[$code] = $model;
		}

		return $results;
	}

	/**
	 * Request remote server and get response
	 *
	 * @param string $date Date format "d.m.Y"
	 * @param string $locale ISO 639-1 standard
	 * @return null|string Server response or Null if failure
	 */
	private function requestRemoteServer($date, $locale)
	{
		$response = null;

		try {
			$streamContext = stream_context_create([
				'http' => [
					'timeout' => self::REQUEST_TIMEOUT_SEC
				]
			]);

			$response = file_get_contents(
				'http://www.bnm.md/' . $locale . '/official_exchange_rates?get_xml=1&date=' . $date,
				false,
				$streamContext
			);

			if (!$response) {
				$response = null;
			}
		} catch (\Exception $e) {
			return null;
		}

		return $response;
	}
}