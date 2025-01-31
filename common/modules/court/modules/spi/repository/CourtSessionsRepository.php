<?php

namespace common\modules\court\modules\spi\repository;

use common\modules\court\modules\spi\entity\lawsuit\LawsuitSessionDTO;
use yii\data\DataProviderInterface;

class CourtSessionsRepository extends BaseRepository {

	public function getLawsuitDataProvider(int $id): DataProviderInterface {
		$url = static::route() . '/lawsuit';
		return $this->getDataProvider([
			'lawsuitId.equals' => $id,
		], $url);
	}

	protected function route(): string {
		return 'court-sessions';
	}

	protected function modelClass(): string {
		return LawsuitSessionDTO::class;
	}
}
