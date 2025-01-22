<?php

namespace common\modules\court\modules\spi\repository;

use Closure;
use common\modules\court\modules\spi\components\SPIApi;
use common\modules\court\modules\spi\helpers\ApiDataProvider;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\data\DataProviderInterface;

abstract class BaseRepository extends Component implements RepositoryInterface {

	protected SPIApi $api;
	public string $modelClass;
	public ?Closure $createModel = null;

	public array $dataProviderConfig = [
		'class' => ApiDataProvider::class,
	];

	abstract protected function route(): string;

	abstract protected function modelClass(): string;

	public function __construct(SPIApi $api, array $config = []) {
		$this->api = $api;
		parent::__construct($config);
	}

	public function getDataProvider(string $appeal, array $params = []): DataProviderInterface {
		$api = $this->api->setAppeal($appeal);
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

}
