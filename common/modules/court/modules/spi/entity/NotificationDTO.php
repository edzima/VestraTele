<?php

namespace common\modules\court\modules\spi\entity;

use yii\base\Model;

class NotificationDTO extends Model {

	public int $id;
	public string $type;
	public string $content;
	public string $date;
	public ?int $user;
	public string $profileUuid;
	public string $signature;
	public string $courtName;
	public bool $read;
}
