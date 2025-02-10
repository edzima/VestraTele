<?php

namespace common\modules\court\modules\spi\entity\lawsuit;

use common\modules\court\modules\spi\Module;
use yii\base\Model;

class LawsuitProceedingDTO extends Model {

	public int $id;
	public string $name;
	public string $date;
	public ?string $sender;
	public ?string $comments;
	public ?int $documentId;
	public ?string $documentName;
	public ?string $documentFile;
	public string $createdDate;
	public string $modificationDate;

	public function attributeLabels(): array {
		return [
			'name' => Module::t('lawsuit', 'Name'),
			'date' => Module::t('lawsuit', 'Date'),
			'comments' => Module::t('lawsuit', 'Comments'),
			'sender' => Module::t('lawsuit', 'Sender'),
			'createdDate' => Module::t('lawsuit', 'Created Date'),
			'modificationDate' => Module::t('lawsuit', 'Modification Date'),
		];
	}

}
