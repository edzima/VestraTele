<?php

namespace common\modules\court\modules\spi\repository;

use common\modules\court\modules\spi\models\application\ApplicationViewDTO;

class ApplicationsRepository extends BaseRepository {

	protected function route(): string {
		return 'applications';
	}

	protected function modelClass(): string {
		return ApplicationViewDTO::class;
	}
}
