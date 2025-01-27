<?php

namespace common\modules\court\modules\spi\repository;

use common\modules\court\modules\spi\entity\application\ApplicationDTO;
use common\modules\court\modules\spi\entity\application\ApplicationViewDTO;
use common\modules\court\modules\spi\helpers\ApiDataProvider;
use Yii;

class ApplicationsRepository extends BaseRepository {

	protected function route(): string {
		return 'applications';
	}

	public array $dataProviderConfig = [
		'class' => ApiDataProvider::class,
		'modelClass' => ApplicationViewDTO::class,
	];

	protected function modelClass(): string {
		return ApplicationDTO::class;
	}

	public function createApplication(ApplicationDTO $model): bool {
		return true;
		$api = $this->getApi();
		$response = $api->post(static::route(), $model->toArray());
		if ($response->isOk) {
			return true;
		}
		Yii::warning($response->getData(), __METHOD__);
		return false;
	}

	public function checkApplication(ApplicationDTO &$model): bool {
		$url = static::route() . '/' . 'check';
		$response = $this->getApi()->post($url, $model->toArray());
		if ($response->isOk) {
			$model = ApplicationDTO::createFromResponse($response);
			return true;
		}
		return false;
	}

	public function createModel(array $data): ApplicationDTO {
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return parent::createModel($data);
	}
}
