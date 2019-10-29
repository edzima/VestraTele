<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-04-08
 * Time: 11:58
 */

namespace backend\modules\issue\models;

use common\models\issue\IssuePay;
use yii\base\InvalidConfigException;
use yii\base\Model;

/**
 * Class PayForm.
 *
 * @property IssuePay $pay
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 *
 */
class PayForm extends Model {

	public $deadline_at;
	public $pay_at;
	public $value;
	public $type;
	public $transferType;
	public $last = true;

	/** @var IssuePay */
	private $pay;

	public function __construct(IssuePay $pay, array $config = []) {
		if ($pay->issue === null) {
			throw  new InvalidConfigException('Issue must exist');
		}
		$this->setPay($pay);
		if ($this->pay_at === null) {
			$this->pay_at = date('Y-m-d');
		}

		parent::__construct($config);
	}

	public function rules() {
		return [
			[['date', 'value', 'type'], 'required'],
			['last', 'boolean'],
			['value', 'number', 'min' => 1, 'numberPattern' => '/^\s*[-+]?[0-9]*[.,]?[0-9]+([eE][-+]?[0-9]+)?\s*$/'],
			[['type'], 'in', 'range' => array_keys(IssuePay::getTypesNames())],
			[['transferType'], 'in', 'range' => array_keys(IssuePay::getTransferTypesNames())],
			[['pay_at', 'deadline_at'], 'date', 'format' => DATE_ATOM],
			['date', 'default', 'value' => date(DATE_ATOM)],
		];
	}

	public function attributeLabels(): array {
		return array_merge(['last' => 'Ostateczna'],
			$this->pay->attributeLabels());
	}

	private function setPay(IssuePay $pay): void {
		$this->pay = $pay;
		$this->pay_at = $pay->pay_at;
		$this->deadline_at = $pay->deadline_at;
		$this->value = $pay->value;
	}

	public function getPay(): IssuePay {
		return $this->pay;
	}

	public function save(): bool {
		if ($this->validate()) {
			$model = $this->getPay();
			$model->pay_at = $this->pay_at;
			$model->transfer_type = $this->transferType;
			$model->deadline_at = $this->deadline_at;
			$model->value = str_replace(',', '.', $this->value);
			$model->type = $this->type;
			$savePay = $model->save();
			if ($savePay) {
				if ($this->last) {
					$model->issue->markAsPayed();
				} else {
					$model->issue->unmarkAsPayed();
				}
			} else {
				$this->addErrors($model->getErrors());
			}
			return $savePay;
		}

		return false;
	}

}