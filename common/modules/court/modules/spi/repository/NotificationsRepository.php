<?php

namespace common\modules\court\modules\spi\repository;

use common\modules\court\modules\spi\entity\NotificationDTO;
use common\modules\court\modules\spi\helpers\ApiDataProvider;
use Yii;

class NotificationsRepository extends BaseRepository {

	protected function route(): string {
		return 'notifications';
	}

	protected function modelClass(): string {
		return NotificationDTO::class;
	}

	public array $dataProviderConfig = [
		'class' => ApiDataProvider::class,
		'key' => 'id',
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

	public function read(int $id): ?NotificationDTO {
		$url = static::route() . '/read/' . $id;
		$response = $this->api
			->put($url);

		if (!$response->isOk) {
			Yii::error($response->getData(), __METHOD__);
			return null;
		}
		return $this->createModel($response->getData());
	}

	protected function createModel(array $data): NotificationDTO {
		return new NotificationDTO($data);
	}

}
