<?php

namespace common\modules\court\modules\spi\repository;

use yii\data\DataProviderInterface;

interface RepositoryInterface {

	public function createDataProvider(): DataProviderInterface;

	public function getDataProvider(string $appeal, array $params = []): DataProviderInterface;

}
