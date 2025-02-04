<?php

namespace common\modules\court\modules\spi\repository;

use yii\data\DataProviderInterface;

interface ByLawsuitRepository {

	public function getByLawsuit(int $id): DataProviderInterface;
}
