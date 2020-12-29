<?php

namespace frontend\models;

use common\models\issue\IssuePay;
use yii\base\InvalidConfigException;
use yii\base\Model;

class UpdatePayForm extends Model {

	public ?string $deadline_at = null;
	public ?string $status = null;

	private IssuePay $pay;

	public function __construct(IssuePay $pay, $config = []) {
		if ($pay->isPayed()) {
			throw new InvalidConfigException('$pay can not be payed.');
		}
		$this->pay = $pay;
		$this->deadline_at = $pay->deadline_at;
		$this->status = $pay->status;
		parent::__construct($config);
	}

	public function rules(): array {
		return [
			[['deadline_at'], 'required'],
			['status', 'integer'],
			['deadline_at', 'date', 'format' => 'Y-m-d', 'min' => date('Y-m-d')],
		];
	}

	public function attributeLabels() {
		return $this->getPay()->attributeLabels();
	}

	public function getPay(): IssuePay {
		return $this->pay;
	}

	public function save(): bool {
		if ($this->validate()) {
			$pay = $this->getPay();
			$pay->deadline_at = $this->deadline_at;
			$pay->status = $this->status;
			return $pay->save(false);
		}
		return false;
	}

	public static function getStatusNames(): array {
		return IssuePay::getStatusNames();
	}
}
