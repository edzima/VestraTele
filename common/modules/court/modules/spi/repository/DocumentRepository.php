<?php

namespace common\modules\court\modules\spi\repository;

use common\modules\court\modules\spi\entity\document\DocumentInnerViewDto;
use yii\data\DataProviderInterface;

class DocumentRepository extends BaseRepository {

	protected function route(): string {
		return 'documents';
	}

	protected function modelClass(): string {
		return DocumentInnerViewDto::class;
	}

	public function download(int $documentId): ?string {
		$url = static::route() . '/download/' . $documentId;
		$response = $this->getApi()
			->get($url);
		if ($response->isOk) {
			return $response->getContent();
		}
		return null;
	}

	public function getLawsuitDocuments(int $lawsuitId): DataProviderInterface {
		$params = [
			'lawsuitId.equals' => $lawsuitId,
		];
		return $this->getDataProvider($params);
	}
}
