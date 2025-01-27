<?php

namespace common\modules\court\modules\spi\entity\lawsuit;

use common\modules\court\modules\spi\Module;
use yii\base\Model;

class NotificationLawsuit extends Model implements LawsuitInterface {

	public int $id;
	public string $signature;
	public int $number;
	public int $year;
	public int $repertoryId;
	public string $courtName;
	public bool $visible;

	public function getId(): int {
		return $this->id;
	}

	public function getSignature(): string {
		return $this->signature;
	}

	public function getNumber(): int {
		return $this->number;
	}

	public function getYear(): int {
		return $this->year;
	}

	public function getCourtName(): string {
		return $this->courtName;
	}

	public function isVisible(): bool {
		return $this->visible;
	}

	public function attributeLabels(): array {
		return [
			'visible' => Module::t('lawsuit', 'Visible'),
			'courtName' => Module::t('lawsuit', 'Court'),
			'signature' => Module::t('lawsuit', 'Signature'),
		];
	}
}
