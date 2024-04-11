<?php

namespace common\modules\credit\components;

use common\modules\credit\components\exception\ReferenceRateNBPXmlGetContentException;
use common\modules\credit\components\exception\ReferenceRateNBPXmlLoadException;
use common\modules\credit\models\ReferenceRateNBP;
use Exception;
use SimpleXMLElement;
use Yii;
use yii\base\Component;
use yii\caching\CacheInterface;
use yii\di\Instance;

class ReferenceRateNBPComponent extends Component implements InterestRateInterface {

	public string $archivePath = 'https://static.nbp.pl/dane/stopy/stopy_procentowe_archiwum.xml';

	/**
	 * @var string|array|CacheInterface
	 */
	public $cache = 'cache';

	public string $cacheKey = 'referenceRateNBP';
	public int $cacheDuration = 60 * 60 * 24;

	private array $_models = [];

	public function init() {
		$this->cache = Instance::ensure($this->cache);
	}

	public function getInterestRate(string $date): ?float {
		$model = $this->findModel($date);
		if ($model) {
			return $model->ref;
		}
		return null;
	}

	public function findModel(string $date): ?ReferenceRateNBP {
		$models = $this->getModels();
		foreach ($models as $model) {
			if ($model->isForDate($date)) {
				return $model;
			}
		}
		return null;
	}

	/**
	 * @param bool $refresh
	 * @return ReferenceRateNBP[]
	 */
	public function getModels(bool $refresh = false): array {
		if (!$refresh) {
			$models = $this->cache->get($this->cacheKey);
			if ($models !== false) {
				$this->_models = $models;
			}
		}
		if (empty($this->_models)) {
			$xml = $this->getXMLFromArchive();
			$models = ReferenceRateNBP::createModels($xml);
			$this->cache->set($this->cacheKey, $models, $this->cacheDuration);
			$this->_models = $models;
		}

		return $this->_models;
	}

	public function getXMLFromArchive(): SimpleXMLElement {
		Yii::beginProfile($this->archivePath, __METHOD__);
		try {
			$content = file_get_contents($this->archivePath);
		} catch (Exception $exception) {
			throw new ReferenceRateNBPXmlGetContentException($exception->getMessage());
		}
		Yii::endProfile($this->archivePath, __METHOD__);

		if ($content === false) {
			throw new ReferenceRateNBPXMLGetContentException('Empty file content for path: ' . $this->archivePath);
		}
		Yii::debug($content, __METHOD__);

		$xml = simplexml_load_string($content);
		if ($xml === false) {
			throw new ReferenceRateNBPXmlLoadException('Invalid XML content');
		}
		return $xml;
	}

}
