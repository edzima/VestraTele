<?php

namespace common\modules\court\modules\spi\repository;

use common\modules\court\modules\spi\entity\notification\NotificationDTO;
use common\modules\court\modules\spi\entity\notification\NotificationViewDTO;
use common\modules\court\modules\spi\helpers\ApiDataProvider;
use Yii;
use yii\data\DataProviderInterface;
use yii\helpers\Json;

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
			'defaultOrder' => [
				'date' => SORT_DESC,
			],
		],
	];

	public function findByLawsuit(int $lawsuitId): DataProviderInterface {
		return $this->getDataProvider([
			'lawsuitId.equals' => $lawsuitId,
		]);
	}

	public function findModel(int $id, bool $cache = true): ?NotificationViewDTO {
		$url = static::route() . '/' . $id;
		if ($cache) {
			$data = $this->getCacheValue($url);
			if (!empty($data)) {
				return $this->createModel(Json::decode($data));
			}
		}

		$response = $this->getApi()->get($url);
		if ($response->isOk) {
			$data = $response->getData();
			if ($cache) {
				$this->setCacheValue($url, Json::encode($data));
			}
			return $this->createModel($data);
		}
		return null;
	}

	public function getUnread(bool $cache = true): ?int {

		$url = static::route() . '/unread';
		if ($cache && $this->getCache()) {
			$value = $this->getCacheValue($url, false);
			if (is_int($value)) {
				return $value;
			}
		}
		$response = $this->getApi()
			->get($url);

		if (!$response->isOk) {
			Yii::error($response->getData(), __METHOD__);
			return null;
		}
		$value = (int) $response->getData();
		$this->setCacheValue($url, $value, false, rand(300, 360));
		return $value;
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
