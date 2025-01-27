<?php

namespace common\modules\court\modules\spi\entity\document;

use yii\base\Model;

class DocumentInnerViewDto extends Model {

	public int $id;
	public string $createDate;
	public ?string $publicationDate;
	public string $documentName;
	public string $fileName;

	/**
	 * @deprecated
	 */
	public int $documentType;
	public bool $downloaded;
	public ?string $documentChecksum;
	public string $createdDate;
	public string $modificationDate;
}
