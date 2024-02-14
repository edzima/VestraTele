<?php

namespace common\modules\credit\components;

use common\modules\credit\components\exception\WiborArchiveException;
use yii\base\Component;
use yii\base\ErrorException;
use yii\caching\CacheInterface;
use yii\di\Instance;

class WiborArchiveComponent extends Component implements InterestRateInterface {

	public string $archiveCSVPath = 'https://stooq.pl/q/d/l/?s=plopln3m&i=d';
	public string $separator = ',';
	public int $dateColumn = 0;
	public int $valueColumn = 4;

	/**
	 * @var string|array|CacheInterface
	 */
	public $cache = 'cache';

	public string $cacheKey = 'referenceRateNBP';
	public int $cacheDuration = 60 * 60 * 24;

	private array $_data = [];

	public function init() {
		$this->cache = Instance::ensure($this->cache);
	}

	public function getInterestRate(string $date): ?float {
		$date = date('Y-m-d', strtotime($date));
		$value = $this->getData()[$date] ?? null;
		if ($value) {
			return $value;
		}
		return null;
	}

	/**
	 * Wibor Values indexed by date (Y-m-d)
	 *
	 * @param bool $cache
	 * @return float[]
	 * @throws WiborArchiveException
	 */
	public function getData(bool $cache = true): array {
		if (!empty($this->_data) && $cache) {
			return $this->_data;
		}
		$data = [];
		if ($cache) {
			$data = $this->cache->get($this->cacheKey);
		}
		if (empty($data)) {
			$data = $this->getDataFromFile();
			if (!empty($data) && $cache) {
				$this->cache->set($this->cacheKey, $data, $this->cacheDuration);
			}
			$this->_data = $data;
		}
		return $this->_data;
	}

	/**
	 * @throws WiborArchiveException
	 */
	protected function getDataFromFile(): array {
		$values = [];
		try {
			if (($open = fopen($this->archiveCSVPath, 'rb')) !== false) {
				while (($data = fgetcsv($open, 1000, $this->separator)) !== false) {
					$date = $data[$this->dateColumn];
					$value = floatval($data[$this->valueColumn]);
					$values[$date] = $value;
				}
				fclose($open);
			}
		} catch (ErrorException $exception) {
			throw new WiborArchiveException($exception->getMessage());
		}
		if (empty($values)) {
			throw new WiborArchiveException('Empty Wibor data.');
		}
		return $values;
	}

}
