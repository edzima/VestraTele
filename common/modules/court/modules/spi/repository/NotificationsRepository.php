<?php

namespace common\modules\court\modules\spi\repository;

use common\modules\court\modules\spi\entity\notification\NotificationDTO;
use common\modules\court\modules\spi\entity\notification\NotificationViewDTO;
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
	];

	public function findModel(int $id): ?NotificationViewDTO {
		$url = static::route() . '/' . $id;
		$response = $this->getApi()->get($url);
		if ($response->isOk) {
			return $this->createModel($response->getData());
		}
		return null;
	}

	public function getUnread(bool $cache = true): ?int {
		if ($cache && $this->getCache()) {
			return (int) $this->getCacheValue($this->getAppeal() . ':unread', false);
		}
		$url = static::route() . '/unread';
		$response = $this->getApi()
			->get($url);

		if (!$response->isOk) {
			Yii::error($response->getData(), __METHOD__);
			return null;
		}

		$this->setCacheValue($this->getAppeal() . ':unread', (int) $response->getData(), false);
		return $response->getData();
	}

	public function read(int $id): ?bool {
		$api = $this->getApi();
		$url = static::route() . '/read/' . $id;
		$response = $api
			->put($url);

		if ($response->isOk) {
			return $response->getData();
		}
		return null;
	}

}
