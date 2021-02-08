<?php

namespace common\modules\lead\models;

use common\modules\lead\exceptions\NotSourceException;
use DateTime;
use yii\base\BaseObject;

class LeadEntity extends BaseObject implements LeadInterface {

	public const SOURCE_DATA_ATTRIBUTE = 'source';

	public const PHONE_DATA_ATTRIBUTE = 'phone';
	public const EMAIL_DATA_ATTRIBUTE = 'email';
	public const POSTAL_CODE_DATA_ATTRIBUTE = 'postal_code';

	private DateTime $dateTime;
	private array $data;

	public function __construct(array $data, DateTime $dateTime = null, $config = []) {
		if ($dateTime === null) {
			$dateTime = new DateTime();
		}
		$this->dateTime = $dateTime;
		$this->data = $data;
		parent::__construct($config);
	}

	public function getDateTime(): DateTime {
		return $this->dateTime;
	}

	public function getData(): array {
		return $this->data;
	}

	public function getSource(): string {
		if (!isset($this->getData()[static::SOURCE_DATA_ATTRIBUTE])) {
			throw new NotSourceException();
		}
		return $this->getData()[static::SOURCE_DATA_ATTRIBUTE];
	}

	public function getPhone(): ?string {
		return $this->getData()[static::PHONE_DATA_ATTRIBUTE] ?? null;
	}

	public function getEmail(): ?string {
		return $this->getData()[static::EMAIL_DATA_ATTRIBUTE] ?? null;
	}

	public function getPostalCode(): ?string {
		return $this->getData()[static::POSTAL_CODE_DATA_ATTRIBUTE] ?? null;
	}
}
