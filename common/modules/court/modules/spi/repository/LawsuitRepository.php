<?php

namespace common\modules\court\modules\spi\repository;

use common\modules\court\modules\spi\helpers\ApiDataProvider;
use common\modules\court\modules\spi\models\lawsuit\LawsuitDetailsDto;
use common\modules\court\modules\spi\models\lawsuit\LawsuitViewIntegratorDto;
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

	public function findBySignature(string $signature, string $appeal): ?LawsuitViewIntegratorDto {
		$dataProvider = $this->getDataProvider($appeal, [
			'signature.equals' => $signature,
		]);
		if ($dataProvider->getTotalCount()) {
			if ($dataProvider->getTotalCount() > 1) {
				Yii::warning('Find more than one Lawsuit for Signature: '
					. $signature . ' in Appeal: ' . $appeal);
			}
			$models = $dataProvider->getModels();
			return $models[array_key_first($models)];
		}
		return null;
	}

	public function getLawsuit(int $id, string $appeal): ?LawsuitDetailsDto {
		$url = static::route() . '/' . $id;
		$response = $this->api
			->setAppeal($appeal)
			->get($url);
		if (!$response->isOk) {
			Yii::warning($response->getData(), __METHOD__);
			return null;
		}
		return new LawsuitDetailsDto($response->data);
	}

}
