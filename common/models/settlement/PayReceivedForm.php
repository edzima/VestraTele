<?php

namespace common\models\settlement;

use common\models\issue\IssuePay;
use common\models\issue\IssuePayInterface;
use yii\base\InvalidConfigException;
use yii\base\Model;

class PayReceivedForm extends Model {

	public $date;

	private int $user_id;
	private IssuePay $pay;

	public function __construct(int $user_id, IssuePay $pay, $config = []) {
		if ($pay->isPayed()) {
			throw new InvalidConfigException('$pay can not be payed.');
		}
		$this->pay = $pay;
		$this->user_id = $user_id;
		parent::__construct($config);
	}

	public function attributeLabels(): array {
		return [
			'date' => 'Kiedy',
		];
	}

	public function rules(): array {
		return [
			['date', 'required'],
			['date', 'date', 'format' => 'Y-m-d'],
		];
	}

	public function getPay(): IssuePayInterface {
		return $this->pay;
	}

	public function received(): bool {
		if (!$this->validate()) {
			return false;
		}
		$pay = $this->pay;
		$pay->pay_at = $this->date;
		$pay->transfer_type = TransferType::TRANSFER_TYPE_CASH;
		$received = new PayReceived();
		$received->user_id = $this->user_id;
		$received->pay_id = $pay->id;
		$received->date_at = $this->date;
		return $pay->save(false) && $received->save();
	}

}
