<?php

namespace common\modules\court\modules\spi\repository;

use Closure;
use common\modules\court\modules\spi\components\SPIApi;
use common\modules\court\modules\spi\entity\AppealInterface;
use common\modules\court\modules\spi\helpers\ApiDataProvider;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\caching\CacheInterface;
use yii\data\DataProviderInterface;
use yii\di\Instance;

abstract class BaseRepository extends Component
	implements RepositoryInterface,
	AppealInterface {

	private SPIApi $api;
	public string $modelClass;
	public ?Closure $createModel = null;

	/**
	 * @var string|array|CacheInterface
	 */
	public $cache = 'cache';

	public array $dataProviderConfig = [
		'class' => ApiDataProvider::class,
		'key' => 'id',
	];

	private string $appeal;

	abstract protected function route(): string;

	abstract protected function modelClass(): string;

	public function __construct(SPIApi $api, array $config = []) {
		$this->api = $api;
		parent::__construct($config);
	}

	public function getAppeal(): string {
		return $this->appeal;
	}

	public function setAppeal(string $appeal): void {
		$this->appeal = $appeal;
		$this->api->setAppeal($appeal);
	}

	protected function getApi(): SPIApi {
		return $this->api->setAppeal($this->appeal);
	}

	public function getDataProvider(array $params = []): DataProviderInterface {
		$api = $this->getApi();
		$dataProvider = $this->createDataProvider();
		$dataProvider->api = $api;
		$dataProvider->url = static::route();
		$params = array_merge($params, $dataProvider->params);
		$dataProvider->params = $params;
		return $dataProvider;
	}

	/**
	 * @throws InvalidConfigException
	 */
	public function createDataProvider(): ApiDataProvider {
		$config = $this->dataProviderConfig;
		if (!isset($config['class'])) {
			$config['class'] = ApiDataProvider::class;
		}
		if (!isset($config['modelClass'])) {
			$config['modelClass'] = $this->modelClass();
		}
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return Yii::createObject($config);
	}

	protected function createModel(array $data): Model {
		if ($this->createModel !== null) {
			return call_user_func($this->createModel, $data);
		}
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return Yii::createObject($this->modelClass(), [$data]);
	}

	public function getCacheValue(string $key, bool $decrypt = true, $defaultValue = null) {
		if ($this->getCache() === null) {
			return false;
		}
		$value = $this->getCache()->get($key);
		if ($value === false) {
			return $defaultValue;
		}
		if ($decrypt) {
			$value = $this->decryptCacheValue($value);
		}
		return $value;
	}

	public function setCacheValue(string $key, $value, bool $encrypt = true, $duration = null, $dependency = null): void {
		if ($encrypt) {
			$value = $this->encryptCacheValue($value);
		}
		$this->getCache()->set($key, $value, $duration, $dependency);
	}

	protected function getCache(): ?CacheInterface {
		if (!$this->cache instanceof CacheInterface) {
			$this->cache = Instance::ensure($this->cache, CacheInterface::class);
			Yii::warning($this->cache);
		}
		return $this->cache;
	}

	private function decryptCacheValue($value) {
		return Yii::$app->security->decryptByPassword($value, $this->getCacheSecurityPassword());
	}

	private function encryptCacheValue($value) {
		return Yii::$app->security->encryptByPassword($value, $this->getCacheSecurityPassword());
	}

	private function getCacheSecurityPassword(): string {
		return $this->api->username . ':' . $this->api->password;
	}

}
