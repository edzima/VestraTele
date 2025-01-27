<?php

namespace common\modules\court\modules\spi\repository;

use common\modules\court\modules\spi\entity\lawsuit\LawsuitDetailsDto;
use common\modules\court\modules\spi\entity\lawsuit\LawsuitViewIntegratorDto;
use common\modules\court\modules\spi\helpers\ApiDataProvider;
use Yii;

class LawsuitRepository extends BaseRepository {

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

	public function findBySignature(string $signature): ?LawsuitViewIntegratorDto {
		$dataProvider = $this->getDataProvider([
			'signature.equals' => $signature,
		]);
		if ($dataProvider->getTotalCount()) {
			if ($dataProvider->getTotalCount() > 1) {
				Yii::warning('Find more than one Lawsuit for Signature: '
					. $signature . ' in Appeal: ' . $this->getAppeal());
			}
			$models = $dataProvider->getModels();
			return $models[array_key_first($models)];
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
