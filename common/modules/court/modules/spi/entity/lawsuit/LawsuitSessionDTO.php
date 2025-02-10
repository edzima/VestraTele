<?php

namespace common\modules\court\modules\spi\entity\lawsuit;

use common\modules\court\modules\spi\Module;
use yii\base\Model;

class LawsuitSessionDTO extends Model {

	public int $id;
	public string $signature;
	public ?int $court;
	public string $date;
	public ?string $room;
	public string $procedure;
	public string $judge;
	public string $subject;
	public ?string $value;
	public ?string $eprotocol;
	public ?string $eprotocolId;
	public ?string $eprotocolVideoPath;
	public ?string $result;
	public ?string $videoArchivizationDate;
	public ?bool $transcriptionFilesPresent;
	public string $createdDate;
	public string $modificationDate;

	public function attributeLabels(): array {
		return [
			'signature' => Module::t('lawsuit', 'Signature'),
			'date' => Module::t('lawsuit', 'Date'),
			'court' => Module::t('lawsuit', 'Court'),
			'room' => Module::t('lawsuit', 'Room'),
			'procedure' => Module::t('lawsuit', 'Procedure'),
			'judge' => Module::t('lawsuit', 'Judge'),
			'createdDate' => Module::t('lawsuit', 'Created Date'),
			'modificationDate' => Module::t('lawsuit', 'Modification Date'),
		];
	}

}
