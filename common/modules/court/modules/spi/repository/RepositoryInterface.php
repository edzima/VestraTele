<?php

namespace common\modules\court\modules\spi\repository;

use yii\data\DataProviderInterface;

interface RepositoryInterface {

	public function createDataProvider(): DataProviderInterface;

	public function setAppeal(string $appeal): void;

	public function getDataProvider(array $params = []): DataProviderInterface;

}
