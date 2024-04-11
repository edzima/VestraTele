<?php

namespace common\modules\credit\components;

use common\modules\credit\components\exception\WiborArchiveException;
use DateTime;
use Yii;
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

	public string $cacheKey;
	public int $cacheDuration = 60 * 60 * 24;

	private array $_data = [];
	public int $startLine = 1;

	public string $dateFormat = 'Y-m-d';

	public function init() {
		$this->cache = Instance::ensure($this->cache);
		$this->cacheKey = $this->archiveCSVPath;
	}

	public function getInterestRate(string $date): ?float {
		$value = $this->getData()[$date] ?? null;
		if ($value) {
			return $value;
		}
		$dateTime = (new DateTime($date))->modify('-1 day');
		$min = new DateTime($this->getMinDate());
		if ($dateTime < $min) {
			return null;
		}
		$max = new DateTime($this->getMaxDate());
		if ($dateTime > $max) {
			$dateTime = $max;
		}
		return $this->getInterestRate($dateTime->format($this->dateFormat));
	}

	public function getMinDate(): string {
		return array_key_first($this->getData());
	}

	public function getMaxDate(): string {
		return array_key_last($this->getData());
	}

	/**
	 * Wibor Values indexed by date (Y-m-d)
	 *
	 * @param bool $cache
	 * @return float[]
	 * @throws WiborArchiveException
	 */
	public function getData(bool $refresh = false): array {
		if (!$refresh) {
			$data = $this->cache->get($this->cacheKey);
			if ($data !== false) {
				$this->_data = $data;
			}
		}
		if (empty($this->_data)) {
			$data = $this->getDataFromFile();
			$this->cache->set($this->cacheKey, $data, $this->cacheDuration);
			$this->_data = $data;
		}

		return $this->_data;
	}

	/**
	 * @throws WiborArchiveException
	 */
	protected function getDataFromFile(): array {
		Yii::beginProfile($this->archiveCSVPath, __METHOD__);

		$values = [];
		$i = 0;
		try {
			if (($open = fopen($this->archiveCSVPath, 'rb')) !== false) {
				while (($data = fgetcsv($open, 1000, $this->separator)) !== false) {
					if ($i >= $this->startLine) {
						$date = $data[$this->dateColumn];
						$value = floatval($data[$this->valueColumn]);
						$values[$date] = $value;
					}

					$i++;
				}
				fclose($open);
			}
		} catch (ErrorException $exception) {
			throw new WiborArchiveException($exception->getMessage());
		}
		if (empty($values)) {
			throw new WiborArchiveException('Empty Wibor data.');
		}
		Yii::endProfile($this->archiveCSVPath, __METHOD__);

		return $values;
	}

}
