<?php

namespace common\modules\court\modules\spi\models\search;

use common\modules\court\modules\spi\models\AppealInterface;
use common\modules\court\modules\spi\repository\RepositoryInterface;
use Yii;
use yii\base\Model;
use yii\data\DataProviderInterface;

abstract class SearchModel extends Model implements AppealInterface {

	protected RepositoryInterface $repository;

	protected string $appeal;

	abstract public function getApiParams(): array;

	public function __construct(RepositoryInterface $repository, string $appeal, array $config = []) {
		$this->repository = $repository;
		$this->appeal = $appeal;
		parent::__construct($config);
	}

	public function search(array $params = []): DataProviderInterface {
		$this->load($params);
		if (!$this->validate()) {
			Yii::warning($this->errors, __METHOD__);
			return $this->repository->createDataProvider();
		}

		$params = $this->getFilterApiParams();
		return $this->repository->getDataProvider($this->appeal, $params);
	}

	protected function getFilterApiParams(): array {
		$params = $this->getApiParams();
		return array_filter($params, function ($value) {
			return !empty($value);
		});
	}

	public function getAppeal(): string {
		return $this->appeal;
	}

}
