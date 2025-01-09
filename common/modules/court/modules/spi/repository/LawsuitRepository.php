<?php

namespace common\modules\court\modules\spi\repository;

use common\modules\court\modules\spi\components\SPIApi;
use common\modules\court\modules\spi\models\lawsuit\LawsuitDetailsDto;
use common\modules\court\modules\spi\models\lawsuit\LawsuitViewIntegratorDto;
use Yii;
use yii\data\ArrayDataProvider;
use yii\data\DataProviderInterface;

class LawsuitRepository {

	private const ROUTE = 'lawsuits';
	private SPIApi $api;

	public function __construct(SPIApi $api) {
		$this->api = $api;
	}

	public function findBySignature(string $signature): DataProviderInterface {
		return $this->getLawsuits([
				'signature.equals' => $signature,
			]
		);
	}

	public function getLawsuit(int $id): ?LawsuitDetailsDto {
		$url = self::ROUTE . '/' . $id;
		$response = $this->api->get($url);
		if (!$response->isOk) {
			Yii::warning($response->getData(), __METHOD__);
			return null;
		}
		return new LawsuitDetailsDto($response->data);
	}

	public function getLawsuits(array $params = []): DataProviderInterface {
		$response = $this->api->get(static::ROUTE, $params);
		$dataProvider = new ArrayDataProvider([
			'key' => 'id',
			'modelClass' => LawsuitViewIntegratorDto::class,
		]);
		if (!$response->isOk) {
			Yii::error($response->getData(), __METHOD__);
			return $dataProvider;
		}
		$dataProvider->models = $this->createModels($response->getData());
		$dataProvider->totalCount = $this->api->getTotalCount($response);
		return $dataProvider;
	}

	protected function createModels(array $data): array {
		$models = [];
		foreach ($data as $dto) {
			$models[] = new LawsuitViewIntegratorDto($dto);
		}
		return $models;
	}
}
