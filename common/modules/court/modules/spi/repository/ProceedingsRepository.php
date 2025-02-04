<?php

namespace common\modules\court\modules\spi\repository;

use common\modules\court\modules\spi\entity\lawsuit\LawsuitProceedingDTO;
use yii\data\DataProviderInterface;

class ProceedingsRepository extends BaseRepository implements ByLawsuitRepository {

	protected function route(): string {
		return 'proceedings';
	}

	protected function modelClass(): string {
		return LawsuitProceedingDTO::class;
	}

	public function getByLawsuit(int $id): DataProviderInterface {
		$url = static::route() . '/lawsuit';
		$params = [
			'lawsuitId.equals' => $id,
		];
		return $this->getDataProvider($params, $url);
	}
}
