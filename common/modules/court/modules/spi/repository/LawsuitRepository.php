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

	public function findBySignature(string $signature, string $appeal): ?LawsuitViewIntegratorDto {
		$dataProvider = $this->getLawsuits($appeal, [
			'signature.equals' => $signature,
		]);
		if ($dataProvider->getTotalCount()) {
			if ($dataProvider->getTotalCount() > 1) {
				Yii::warning('Find more than one Lawsuit for Signature: '
					. $signature . ' in Appeal: ' . $appeal);
			}
			return $dataProvider->getModels()[0];
		}
		return null;
	}

	public function getLawsuit(int $id, string $appeal): ?LawsuitDetailsDto {
		$url = self::ROUTE . '/' . $id;
		$response = $this->api
			->setAppeal($appeal)
			->get($url);
		if (!$response->isOk) {
			Yii::warning($response->getData(), __METHOD__);
			return null;
		}
		return new LawsuitDetailsDto($response->data);
	}

	public function getLawsuits(string $appeal, array $params = []): DataProviderInterface {
		$response = $this->api
			->setAppeal($appeal)
			->get(static::ROUTE, $params);
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
