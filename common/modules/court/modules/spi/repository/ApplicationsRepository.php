<?php

namespace common\modules\court\modules\spi\repository;

use common\modules\court\modules\spi\helpers\ApiDataProvider;
use common\modules\court\modules\spi\models\application\ApplicationDTO;
use common\modules\court\modules\spi\models\application\ApplicationViewDTO;
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

	public function createApplication(string $appeal, ApplicationDTO $model): bool {
		return true;
		$api = $this->api;
		$api->setAppeal($appeal);
		$response = $api->post(static::route(), $model->toArray());
		if ($response->isOk) {
			return true;
		}
		Yii::warning($response->getData(), __METHOD__);
		return false;
	}

	public function checkApplication(string $appeal, ApplicationDTO &$model): bool {
		$url = static::route() . '/' . 'check';
		$api = $this->api;
		$api->setAppeal($appeal);
		$response = $api->post($url, $model->toArray());
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
