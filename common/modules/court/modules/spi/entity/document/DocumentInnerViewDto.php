<?php

namespace common\modules\court\modules\spi\entity\document;

use common\modules\court\modules\spi\Module;
use yii\base\Model;

class DocumentInnerViewDto extends Model {

	public int $id;
	public string $createDate;
	public ?string $publicationDate;
	public string $documentName;
	public ?string $fileName;

	/**
	 * @deprecated
	 */
	public int $documentType;
	public bool $downloaded;
	public ?string $documentChecksum;
	public string $createdDate;
	public string $modificationDate;

	public function attributeLabels(): array {
		return [
			'createDate' => Module::t('document', 'Created Date'),
			'documentName' => Module::t('document', 'Document Name'),
			'fileName' => Module::t('document', 'File Name'),
			'documentType' => Module::t('document', 'Document Type'),
			'downloaded' => Module::t('document', 'Downloaded'),
			'documentChecksum' => Module::t('document', 'Document Checksum'),
			'createdDate' => Module::t('document', 'Created Date'),
			'modificationDate' => Module::t('document', 'Modification Date'),
		];
	}
}
