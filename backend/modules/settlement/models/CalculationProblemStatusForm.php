<?php

namespace backend\modules\settlement\models;

use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use yii\base\InvalidConfigException;
use yii\base\Model;

class CalculationProblemStatusForm extends Model {

	public ?int $status = null;

	private IssuePayCalculation $model;

	public function __construct(IssuePayCalculation $model, $config = []) {
		if ($model->isPayed()) {
			throw new InvalidConfigException('Calculation cannot be payed.');
		}
		$this->model = $model;
		$this->status = $model->problem_status;
		parent::__construct($config);
	}

	public function rules(): array {
		return [
			['status', 'required'],
			['status', 'in', 'range' => array_keys(static::getStatusesNames())],
		];
	}

	public static function getStatusesNames(): array {
		return IssuePayCalculation::getProblemStatusesNames();
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		$model = $this->model;
		$model->problem_status = $this->status;
		if (!$model->save(false)) {
			return false;
		}
		$notPayedIds = $model->getNotPayedPays()->getIds();
		if (!empty($notPayedIds)) {
			IssuePay::deleteAll(['id' => $notPayedIds]);
		}
		return true;
	}

	public function getModel(): IssuePayCalculation {
		return $this->model;
	}
}
