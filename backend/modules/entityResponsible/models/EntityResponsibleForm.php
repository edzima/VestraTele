<?php

namespace backend\modules\entityResponsible\models;

use common\models\Address;
use common\models\entityResponsible\EntityResponsible;
use yii\base\Model;
use yii\db\QueryInterface;

class EntityResponsibleForm extends Model {

	public string $name = '';
	public ?string $details = null;
	public ?bool $is_for_summon = false;

	private ?EntityResponsible $model = null;
	private ?Address $address = null;

	public function rules(): array {
		return [
			[['name'], 'required'],
			[['name', 'details'], 'string'],
			[['is_for_summon'], 'boolean'],
			[['details', 'is_for_summon'], 'default', 'value' => null],
			[
				'name', 'unique',
				'targetClass' => EntityResponsible::class,
				'filter' => function (QueryInterface $query) {
					if (!$this->getModel()->isNewRecord) {
						$query->andWhere(['not', ['id' => $this->getModel()->id]]);
					}
				},

			],
		];
	}

	public function attributeLabels() {
		return EntityResponsible::instance()->attributeLabels();
	}

	public function load($data, $formName = null) {
		return parent::load($data, $formName)
			&& $this->getAddress()->load($data, $formName);
	}

	public function validate($attributeNames = null, $clearErrors = true) {
		return parent::validate($attributeNames, $clearErrors)
			&& $this->getAddress()->validate($attributeNames, $clearErrors);
	}

	public function setModel(EntityResponsible $model): void {
		$this->model = $model;
		$this->name = $model->name;
		$this->details = $model->details;
		$this->is_for_summon = $model->is_for_summon;
		$this->address = $model->address;
	}

	public function save(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}

		$model = $this->getModel();
		$model->name = $this->name;
		$model->details = $this->details;
		$model->is_for_summon = $this->is_for_summon;
		if (!$model->save()) {
			return false;
		}
		$this->saveAddress();
		return true;
	}

	public function getModel(): EntityResponsible {
		if ($this->model === null) {
			$this->model = new EntityResponsible();
		}
		return $this->model;
	}

	public function saveAddress(): bool {
		$model = $this->getAddress();
		if ($model->isEmpty()) {
			$this->getModel()->unlinkAddress($model);
			return true;
		}
		if (!$model->save(false)) {
			return false;
		}
		if ($this->getModel()->address === null) {
			$this->getModel()->linkAddress($model);
		}

		return true;
	}

	public function getAddress(): Address {
		if ($this->address === null) {
			$this->address = $this->getModel()->address ?? new Address();
		}
		$this->address->scenario = Address::SCENARIO_NOT_REQUIRED;
		return $this->address;
	}
}
