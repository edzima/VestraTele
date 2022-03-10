<?php

namespace common\modules\lead\entities;

use Yii;
use yii\base\BaseObject;
use function str_replace;

abstract class Dialer extends BaseObject implements DialerInterface {

	public const STATUS_NEW = 1;

	public const STATUS_SHOULD_CALL = 199;
	public const STATUS_CALLING = 200;
	public const STATUS_ESTABLISHED = 202;
	public const STATUS_UNESTABLISHED = 204;

	public static function getStatusesNames(): array {
		return [
			static::STATUS_NEW => Yii::t('lead', 'New'),
			static::STATUS_SHOULD_CALL => Yii::t('lead', 'Should Call'),
			static::STATUS_CALLING => Yii::t('lead', 'Calling'),
			static::STATUS_UNESTABLISHED => Yii::t('lead', 'Unestablished'),
			static::STATUS_ESTABLISHED => Yii::t('lead', 'Established'),
		];
	}

	public function shouldCall(): bool {
		return $this->getStatusId() === static::STATUS_SHOULD_CALL;
	}

	protected function parsePhone(string $phone): string {
		return str_replace([' ', '+'], ['', '00'], $phone);
	}

}
