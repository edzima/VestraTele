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

	public function getUnread(): ?int {
		$url = static::route() . '/unread';
		$response = $this->api
			->get($url);

		if (!$response->isOk) {
			Yii::error($response->getData(), __METHOD__);
			return null;
		}

		return $response->getData();
	}

	public function read(int $id): ?bool {
		$url = static::route() . '/read/' . $id;
		$response = $this->api
			->put($url);

		if ($response->isOk) {
			return $response->getData();
		}
		return null;
	}

}
