<?php

namespace common\modules\court\modules\spi\repository;

use common\modules\court\modules\spi\entity\NotificationDTO;
use common\modules\court\modules\spi\entity\NotificationViewDTO;
use common\modules\court\modules\spi\helpers\ApiDataProvider;
use Yii;

class NotificationsRepository extends BaseRepository {

	protected function route(): string {
		return 'notifications';
	}

	protected function modelClass(): string {
		return NotificationViewDTO::class;
	}

	public array $dataProviderConfig = [
		'class' => ApiDataProvider::class,
		'key' => 'id',
		'modelClass' => NotificationDTO::class,
		'pagination' => [
			'pageSize' => 50,
		],
		'sort' => [
			'attributes' => [
				'type',
				'content',
				'date',
				'signature',
				'read',
			],
			'enableMultiSort' => true,
		],
	];

	public function findModel(int $id, string $appeal): ?NotificationViewDTO {
		$url = static::route() . '/' . $id;
		$this->api->setAppeal($appeal);
		$response = $this->api->get($url);
		if ($response->isOk) {
			return $this->createModel($response->getData());
		}
		return null;
	}

	public function getUnread(string $appeal, bool $cache = true): ?int {
		if ($cache) {
			return (int) $this->getCacheValue($appeal . ':unread', false);
		}
		$url = static::route() . '/unread';
		$api = $this->api;
		$api->setAppeal($appeal);
		$response = $api
			->get($url);

		if (!$response->isOk) {
			Yii::error($response->getData(), __METHOD__);
			return null;
		}

		$this->setCacheValue($appeal . ':unread', (int) $response->getData(), false);
		return $response->getData();
	}

	public function read(int $id, string $appeal): ?bool {
		$api = $this->api;
		$api->setAppeal($appeal);
		$url = static::route() . '/read/' . $id;
		$response = $api
			->put($url);

		if ($response->isOk) {
			return $response->getData();
		}
		return null;
	}

}
