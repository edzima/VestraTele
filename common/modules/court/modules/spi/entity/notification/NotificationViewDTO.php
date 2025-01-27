<?php

namespace common\modules\court\modules\spi\entity\notification;

use common\modules\court\modules\spi\entity\lawsuit\NotificationLawsuit;
use common\modules\court\modules\spi\Module;
use yii\base\Model;

class NotificationViewDTO extends Model {

	public int $id;
	public string $type;
	public string $content;
	public string $date;
	public ?int $user;
	public string $profileUuid;
	public bool $read;
	public ?string $mobileContent;
	private NotificationLawsuit $lawsuit;
	public ?string $tmpProfil;

	public function setLawsuit($value): void {
		if (is_array($value)) {
			$model = new NotificationLawsuit($value);
			$value = $model;
		}
		$this->lawsuit = $value;
	}

	public function getLawsuit(): NotificationLawsuit {
		return $this->lawsuit;
	}

	public function attributeLabels(): array {
		return [
			'type' => Module::t('notification', 'Type'),
			'content' => Module::t('notification', 'Content'),
			'date' => Module::t('notification', 'Date'),
			'courtName' => Module::t('notification', 'Court Name'),
			'read' => Module::t('notification', 'Read'),
			'signature' => Module::t('notification', 'Signature'),
		];
	}
}
