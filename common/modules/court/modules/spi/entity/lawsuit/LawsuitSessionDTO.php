<?php

namespace common\modules\court\modules\spi\entity\lawsuit;

use yii\base\Model;

class LawsuitSessionDTO extends Model {

	public int $id;
	public string $signature;
	public ?int $court;
	public string $date;
	public string $room;
	public string $procedure;
	public string $judge;
	public string $subject;
	public string $value;
	public ?string $eprotocol;
	public ?string $eprotocolId;
	public ?string $eprotocolVideoPath;
	public ?string $result;
	public ?string $videoArchivizationDate;
	public ?bool $transcriptionFilesPresent;
	public string $createdDate;
	public string $modificationDate;

}
