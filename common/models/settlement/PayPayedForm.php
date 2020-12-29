<?php

namespace common\models\settlement;

use common\models\issue\IssuePay;
use yii\base\InvalidConfigException;
use yii\base\Model;

class PayPayedForm extends Model {

	public ?int $id = null;
	public ?string $date = null;
	public int $transfer_type = IssuePay::TRANSFER_TYPE_BANK;

	private IssuePay $pay;

	public function __construct(IssuePay $pay, $config = []) {
		if ($pay->isPayed()) {
			throw new InvalidConfigException('$pay can not be payed.');
		}
		$this->pay = $pay;
		parent::__construct($config);
	}

	public function rules(): array {
		return [
			[['transfer_type', 'date'], 'required'],
			['date', 'date', 'format' => 'Y-m-d'],
			['transfer_type', 'in', 'range' => array_keys(IssuePay::getTransferTypesNames())],
		];
	}

	public function getPay(): IssuePay {
		return $this->pay;
	}

	public function pay(): bool {
		if ($this->validate()) {
			$pay = $this->getPay();
			$pay->pay_at = $this->date;
			$pay->status = null;
			return $pay->save(false);
		}
		return false;
	}
}
