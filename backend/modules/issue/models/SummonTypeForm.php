<?php

namespace backend\modules\issue\models;

use common\models\issue\SummonType;
use common\models\SummonTypeOptions;
use Yii;
use yii\base\Model;

class SummonTypeForm extends Model {

	public string $name = '';
	public string $short_name = '';
	public ?string $calendar_background = null;

	private ?SummonType $model = null;
	private ?SummonTypeOptions $options = null;

	public function rules(): array {
		return [
			[['name', 'short_name'], 'required'],
			[['name', 'short_name', 'calendar_background'], 'string'],
		];
	}

	public function attributeLabels(): array {
		return [
			'name' => Yii::t('common', 'Name'),
			'short_name' => Yii::t('common', 'Short Name'),
		];
	}

	public function load($data, $formName = null) {
		return parent::load($data, $formName) && $this->getOptions()->load($data, $formName);
	}

	public function getOptions(): SummonTypeOptions {
		if ($this->options === null) {
			$this->options = $this->getModel()->getOptions();
		}
		return $this->options;
	}

	public function getModel(): SummonType {
		if ($this->model === null) {
			$this->model = new SummonType();
		}
		return $this->model;
	}

	public function setModel(SummonType $model): void {
		$this->model = $model;
		$this->name = $model->name;
		$this->short_name = $model->short_name;
		$this->calendar_background = $model->calendar_background;
		$this->options = $model->getOptions();
	}

	public function save(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		$model = $this->getModel();
		$model->name = $this->name;
		$model->short_name = $this->short_name;
		$model->calendar_background = $this->calendar_background;
		$model->options = $this->getOptions()->toJson();
		if ($model->save()) {
			return true;
		}

		return false;
	}

	public function validate($attributeNames = null, $clearErrors = true) {
		return parent::validate($attributeNames, $clearErrors) && $this->getOptions()->validate($attributeNames, $clearErrors);
	}

}
