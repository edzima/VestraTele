<?php

namespace backend\modules\settlement\models;

use common\models\settlement\CostType;
use common\models\settlement\CostTypeOptions;
use yii\base\Model;
use yii\db\QueryInterface;

class CostTypeForm extends Model {

	public $name;
	public $is_active = true;
	public $is_for_settlement = false;

	private ?CostType $model = null;
	private ?CostTypeOptions $options = null;

	public function rules(): array {
		return [
			[['name', 'is_active'], 'required'],
			[['is_active', 'is_for_settlement'], 'boolean'],
			[
				'name', 'unique',
				'targetClass' => CostType::class,
				'filter' => function (QueryInterface $query) {
					if (!$this->getModel()->isNewRecord) {
						$query->andWhere(['not', ['id' => $this->getModel()->id]]);
					}
				},
			],
		];
	}

	public function attributeLabels(): array {
		return array_merge(CostType::instance()->attributeLabels(), [
		]);
	}

	public function setModel(CostType $model): void {
		$this->model = $model;
		$this->name = $model->name;
		$this->is_active = $model->is_active;
		$this->is_for_settlement = $model->is_for_settlement;
	}

	public function validate($attributeNames = null, $clearErrors = true) {
		return parent::validate($attributeNames, $clearErrors)
			&& $this->getOptions()->validate($attributeNames, $clearErrors);
	}

	public function load($data, $formName = null) {
		return parent::load($data, $formName)
			&& $this->getOptions()->load($data, $formName);
	}

	public function save(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		$model = $this->getModel();
		$model->name = $this->name;
		$model->is_active = $this->is_active;
		$model->is_for_settlement = $this->is_for_settlement;
		$model->setTypeOptions($this->getOptions());
		$model->options = $this->getOptions()->toJson();
		if (!$model->save()) {
			return false;
		}
		return $model->save();
	}

	public function getModel(): CostType {
		if ($this->model === null) {
			$this->model = new CostType();
		}
		return $this->model;
	}

	public function getOptions(): CostTypeOptions {
		if ($this->options === null) {
			$this->options = $this->getModel()->getTypeOptions();
		}
		return $this->options;
	}

}
