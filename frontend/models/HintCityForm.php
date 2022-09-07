<?php

namespace frontend\models;

use common\models\hint\HintCity;
use Yii;
use yii\base\Model;

class HintCityForm extends Model {

	public $status;
	public $details;

	private HintCity $model;

	public static function getStatusNames(): array {
		return HintCity::getStatusesNames();
	}

	public function rules(): array {
		return [
			['status', 'required'],
			['status', 'in', 'range' => array_keys(static::getStatusNames())],
			['details', 'string'],
			[
				'details', 'required', 'enableClientValidation' => false, 'when' => function (): bool {
				return $this->status === HintCity::STATUS_ABANDONED;
			}, 'message' => Yii::t('hint', 'Details cannot be blank when status is abandoned.'),
			],
		];
	}

	public function __construct(HintCity $model, $config = []) {
		$this->setModel($model);
		parent::__construct($config);
	}

	private function setModel(HintCity $model): void {
		$this->model = $model;
		$this->status = $model->status;
		$this->details = $model->details;
	}

	public function getModel(): HintCity {
		return $this->model;
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}

		$model = $this->model;
		$model->status = $this->status;
		$model->details = $this->details;

		return $model->save();
	}

}
