<?php

namespace common\modules\court\modules\spi\repository;

use common\modules\court\modules\spi\entity\document\DocumentInnerViewDto;
use yii\data\DataProviderInterface;

class DocumentRepository extends BaseRepository implements ByLawsuitRepository {

	protected function route(): string {
		return 'documents';
	}

	protected function modelClass(): string {
		return DocumentInnerViewDto::class;
	}

	public function download(int $documentId, bool $pdf = false): ?string {
		$url = static::route() . '/download/' . $documentId;
		if ($pdf) {
			$url .= '/pdf';
		}
		$response = $this->getApi()
			->get($url);
		if ($response->isOk) {
			return $response->getContent();
		}
		return null;
	}

	public function getByLawsuit(int $id): DataProviderInterface {
		$params = [
			'lawsuitId.equals' => $id,
		];
		return $this->getDataProvider($params);
	}
}
