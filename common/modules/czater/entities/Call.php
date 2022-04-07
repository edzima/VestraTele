<?php

namespace common\modules\czater\entities;

use Yii;
use yii\base\Model;

class Call extends Model {

	public const STATUS_ANSWERED = 'answered';
	public const STATUS_NOANSWERED = 'noanswered';
	public const STATUS_CLIENT_FAULT = 'client_fault';
	public const STATUS_REQUESTED = 'requested';
	public const STATUS_BUSYED = 'busyed';

	public int $id;
	public string $clientDirectional;
	public string $clientNumber;
	public ?string $clientName;
	public int $duration;
	public string $dateRequested;
	public string $status;

	public ?string $consultantName;
	public ?string $consultantNumber;
	public ?string $dateStart;
	public ?string $dateFinish;

	public function getClientFullNumber(): string {
		return $this->clientDirectional . ' ' . $this->clientNumber;
	}

	public function getStatusName(): string {
		return static::getStatusesNames()[$this->status];
	}

	public static function getStatusesNames(): array {
		return [
			static::STATUS_ANSWERED => Yii::t('czater', 'Answered'),
			static::STATUS_BUSYED => Yii::t('czater', 'Busyed'),
			static::STATUS_NOANSWERED => Yii::t('czater', 'Noanswered'),
			static::STATUS_CLIENT_FAULT => Yii::t('czater', 'Client fault'),
			static::STATUS_REQUESTED => Yii::t('czater', 'Requested'),
		];
	}
}
