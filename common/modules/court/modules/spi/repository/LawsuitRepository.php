<?php

namespace common\modules\court\modules\spi\repository;

use common\modules\court\modules\spi\entity\lawsuit\LawsuitDetailsDto;
use common\modules\court\modules\spi\entity\lawsuit\LawsuitViewIntegratorDto;
use common\modules\court\modules\spi\helpers\ApiDataProvider;
use Yii;
use yii\helpers\Json;

class LawsuitRepository extends BaseRepository {

	public ?int $cacheDuration = 3600;

	protected function route(): string {
		return 'lawsuits';
	}

	protected function modelClass(): string {
		return LawsuitViewIntegratorDto::class;
	}

	public array $dataProviderConfig = [
		'class' => ApiDataProvider::class,
		'key' => 'id',
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

	public function findBySignature(string $signature, string $courtName, bool $equals = true, bool $cache = true): ?LawsuitViewIntegratorDto {
		if ($cache) {
			$data = $this->getCacheValue($courtName . ':' . $signature . ':' . $equals, true, null);
			if ($data !== null) {
				$data = Json::decode($data);
				return $this->createModel($data);
			}
		}

		$dataProvider = $this->getDataProvider([
			'signature.' . ($equals ? 'equals' : 'contains') => $signature,
			'courtName.' . ($equals ? 'equals' : 'contains') => $courtName,
		]);
		if ($dataProvider->getTotalCount()) {
			if ($dataProvider->getTotalCount() > 1) {
				Yii::warning('Find more than one Lawsuit for Signature: '
					. $signature . ' in Appeal: ' . $this->getAppeal());
			}
			$models = $dataProvider->getModels();
			$model = $models[array_key_first($models)];
			$data = Json::encode($model->toArray());
			$this->setCacheValue($courtName . ':' . $signature . ':' . $equals, $data);
			return $model;
		}
		return null;
	}

	public function getLawsuit(int $id): ?LawsuitDetailsDto {
		$url = static::route() . '/' . $id;
		$response = $this->getApi()
			->get($url);
		if (!$response->isOk) {
			Yii::warning($response->getData(), __METHOD__);
			return null;
		}
		return new LawsuitDetailsDto($response->data);
	}

}
