<?php

namespace common\modules\court\modules\spi\repository;

use common\modules\court\modules\spi\components\SPIApi;
use common\modules\court\modules\spi\entity\NotificationDTO;
use Yii;
use yii\data\ArrayDataProvider;
use yii\data\DataProviderInterface;

class NotificationsRepository {

	private const ROUTE = 'notifications';
	private SPIApi $api;

	public function __construct(SPIApi $api) {
		$this->api = $api;
	}

	public function getUnread(): ?int {
		$url = static::ROUTE . '/unread';
		$response = $this->api
			->get($url);

		if (!$response->isOk) {
			Yii::error($response->getData(), __METHOD__);
			return null;
		}

		return $response->getData();
	}

	public function read(int $id): ?NotificationDTO {
		$url = static::ROUTE . '/read/' . $id;
		$response = $this->api
			->put($url);
		codecept_debug($response->getData());

		if (!$response->isOk) {
			Yii::error($response->getData(), __METHOD__);
			return null;
		}
		return new NotificationDTO($response->getData());
	}

	public function getNotifications(array $params = []): DataProviderInterface {
		$response = $this->api
			->get(static::ROUTE, $params);
		$dataProvider = new ArrayDataProvider([
			'key' => 'id',
		]);
		if (!$response->isOk) {
			Yii::error($response->getData(), __METHOD__);
			return $dataProvider;
		}
		$dataProvider->models = $this->createModels($response->getData());
		$dataProvider->totalCount = $this->api->getTotalCount($response);
		return $dataProvider;
	}

	private function createModels(array $data): array {
		$models = $data;
		foreach ($data as $datum) {
			$models[] = new NotificationDTO($datum);
		}
		return $models;
	}

}
