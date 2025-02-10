<?php

namespace common\modules\court\modules\spi\helpers;

use Closure;
use common\modules\court\modules\spi\components\SPIApi;
use ReflectionClass;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\data\BaseDataProvider;
use yii\httpclient\Response;

class ApiDataProvider extends BaseDataProvider {

	public int $sizeLimitOnPaginationDisable = 200;

	public SPIApi $api;
	public string $url;
	public array $params = [];

	/**
	 * @var string|callable|null the column that is used as the key of the data models.
	 * This can be either a column name, or a callable that returns the key value of a given data model.
	 * If this is not set, the index of the [[models]] array will be used.
	 * @see getKeys()
	 */
	public $key;
	public ?string $modelClass = null;
	public ?Closure $createModel = null;

	private ?Response $response = null;

	public function prepare($forcePrepare = false): void {
		if ($forcePrepare || $this->response === null) {
			$this->response = $this->prepareResponse();
		}
		parent::prepare($forcePrepare);
	}

	protected function prepareResponse(): Response {
		$this->prepareParams();
		$response = $this->api->get($this->url, $this->params);
		if (YII_ENV_TEST) {
			codecept_debug($response->getData());
		}
		return $response;
	}

	protected function prepareModels(): array {
		$models = [];
		if ($this->response->isOk) {
			if (($pagination = $this->getPagination()) !== false) {
				$pagination->totalCount = $this->getTotalCount();
				if ($pagination->totalCount === 0) {
					return [];
				}
			}
			foreach ($this->response->getData() as $data) {
				$models[] = $this->createModel($data);
			}
		}
		return $models;
	}

	protected function prepareTotalCount(): int {
		if ($this->response === null) {
			$this->response = $this->prepareResponse();
		}
		$response = $this->response;
		if (isset($response->getHeaders()['X-Total-Count'])) {
			return (int) $response->getHeaders()['X-Total-Count'];
		}
		return count($response->getData());
	}

	protected function prepareKeys($models): array {
		if ($this->key !== null) {
			$keys = [];
			foreach ($models as $model) {
				if (is_string($this->key)) {
					$keys[] = $model[$this->key];
				} else {
					$keys[] = call_user_func($this->key, $model);
				}
			}

			return $keys;
		}

		return array_keys($models);
	}

	/**
	 * @throws InvalidConfigException
	 */
	protected function createModel(array $data): Model {
		if ($this->createModel !== null) {
			return call_user_func($this->createModel, $data);
		}
		if ($this->modelClass === null) {
			throw new InvalidConfigException('createModel must be closure or modelClass must be set.');
		}
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return Yii::createObject($this->modelClass, [$data]);
	}

	protected function prepareParams(): void {
		$this->preparePaginationParams();
		$this->prepareSortParams();
	}

	protected function preparePaginationParams(): void {
		if (($pagination = $this->getPagination()) !== false) {
			$validatePage = $pagination->validatePage;
			$pagination->validatePage = false;
			$this->params[$this->api::PARAM_PAGE_SIZE] = $pagination->getLimit();
			$this->params[$this->api::PARAM_PAGE] = $pagination->getPage(true);
			$pagination->validatePage = $validatePage;
		} else {
			$this->params[$this->api::PARAM_PAGE_SIZE] = $this->sizeLimitOnPaginationDisable;
		}
	}

	protected function prepareSortParams(): void {
		if (($sort = $this->getSort()) !== false) {
			$sortParams = [];
			foreach ($sort->getOrders() as $name => $order) {
				$nameOrder = $name;
				if ($order === SORT_ASC) {
					$nameOrder .= ',asc';
				}
				if ($order === SORT_DESC) {
					$nameOrder .= ',desc';
				}
				$sortParams[] = $nameOrder;
			}
			if (!empty($sortParams)) {
				$this->params[$this->api::PARAM_SORT] = $sortParams;
			}
		}
	}

	public function refresh(): void {
		$this->response = null;
		parent::refresh();
	}

	public function setSort($value) {
		parent::setSort($value);
		if ($this->modelClass && ($sort = $this->getSort()) !== false) {
			$modelClass = $this->modelClass;
			$reflection = new ReflectionClass($modelClass);
			if ($reflection->isSubclassOf(Model::class)) {
				$model = $modelClass::instance();
				if (empty($sort->attributes)) {
					foreach ($model->attributes() as $attribute) {
						$sort->attributes[$attribute] = [
							'asc' => [$attribute => SORT_ASC],
							'desc' => [$attribute => SORT_DESC],
						];
					}
				}
				if ($sort->modelClass === null) {
					$sort->modelClass = $modelClass;
				}
			}
		}
	}
}
