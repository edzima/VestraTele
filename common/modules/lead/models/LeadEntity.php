<?php

namespace common\modules\lead\models;

use common\modules\lead\exceptions\MissingDataAttributeException;
use DateTime;
use yii\base\BaseObject;

class LeadEntity extends BaseObject implements LeadInterface {

	public const SOURCE_DATA_ATTRIBUTE = 'source';
	public const PHONE_DATA_ATTRIBUTE = 'phone';
	public const EMAIL_DATA_ATTRIBUTE = 'email';
	public const POSTAL_CODE_DATA_ATTRIBUTE = 'postal_code';
	public const STATUS_DATA_ATTRIBUTE = 'status_id';
	public const TYPE_DATA_ATTRIBUTE = 'type_id';

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

	public function getStatusId(): int {
		if (!isset($this->getData()[static::STATUS_DATA_ATTRIBUTE])) {
			return LeadStatusInterface::STATUS_NEW;
		}
		return $this->getData()[static::STATUS_DATA_ATTRIBUTE];
	}

	public function getSourceId(): int {
		if (!isset($this->getData()[static::SOURCE_DATA_ATTRIBUTE])) {
			throw new MissingDataAttributeException(static::SOURCE_DATA_ATTRIBUTE);
		}
		return $this->getData()[static::SOURCE_DATA_ATTRIBUTE];
	}

	public function getTypeId(): int {
		if (!isset($this->getData()[static::TYPE_DATA_ATTRIBUTE])) {
			throw new MissingDataAttributeException(static::TYPE_DATA_ATTRIBUTE);
		}
		return $this->getData()[static::TYPE_DATA_ATTRIBUTE];
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
