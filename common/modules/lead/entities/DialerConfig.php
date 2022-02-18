<?php

namespace common\modules\lead\entities;

use yii\base\Model;

class DialerConfig extends Model implements DialerConfigInterface {

	public ?int $dailyAttemptsLimit = 3;
	public ?int $globallyAttemptsLimit = 10;
	public int $nextCallInterval = 1200;

	public function getDailyAttemptsLimit(): ?int {
		return $this->dailyAttemptsLimit;
	}

	public function getGloballyAttemptsLimit(): ?int {
		return $this->globallyAttemptsLimit;
	}

	public function getNextCallInterval(): int {
		return $this->nextCallInterval;
	}

	public static function fromConfig(DialerConfigInterface $config): self {
		$model = new static();
		$model->dailyAttemptsLimit = $config->getDailyAttemptsLimit();
		$model->globallyAttemptsLimit = $config->getGloballyAttemptsLimit();
		$model->nextCallInterval = $config->getNextCallInterval();
		return $model;
	}

}
