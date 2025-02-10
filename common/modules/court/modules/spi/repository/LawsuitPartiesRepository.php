<?php

namespace common\modules\court\modules\spi\repository;

use common\modules\court\modules\spi\entity\lawsuit\LawsuitPartyDTO;
use yii\data\DataProviderInterface;

class LawsuitPartiesRepository extends BaseRepository implements ByLawsuitRepository {

	protected function route(): string {
		return 'parties/lawsuit';
	}

	protected function modelClass(): string {
		return LawsuitPartyDTO::class;
	}

	public function getByLawsuit(int $id): DataProviderInterface {
		return $this->getDataProvider([
			'lawsuitId.equals' => $id,
		]);
	}
}
