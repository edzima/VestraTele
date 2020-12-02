<?php

namespace backend\modules\provision\models;

use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueType;
use common\models\issue\IssueUser;
use common\models\provision\ProvisionType;
use Yii;
use yii\base\Model;

class ProvisionTypeForm extends Model {

	public string $name = '';
	public bool $is_percentage = true;
	public string $value = '';

	public bool $only_with_tele = false;
	public bool $is_default = false;
	public $roles = [];
	public $issueTypesIds = [];
	public $calculationTypes = [];

	private ?ProvisionType $model = null;
	
	public function rules(): array {
		return [
			[['name', 'value', 'is_percentage'], 'required'],
			['name', 'string', 'max' => 255],
			[
				'value', 'number', 'min' => 0, 'max' => 100, 'when' => function (): bool {
				return $this->is_percentage;
			},
			],
			[
				'value', 'number', 'min' => 0, 'max' => 10000, 'when' => function (): bool {
				return !$this->is_percentage;
			},
			],
			[['only_with_tele', 'is_default', 'is_percentage'], 'boolean'],
			['calculationTypes', 'in', 'range' => array_keys(static::getCalculationTypesNames()), 'allowArray' => true],
			['issueTypesIds', 'in', 'range' => array_keys(static::getIssueTypesNames()), 'allowArray' => true],
			['roles', 'in', 'range' => array_keys(static::getRolesNames()), 'allowArray' => true],
		];
	}

	public function attributeLabels(): array {
		return array_merge($this->getModel()->attributeLabels(), [
			'roles' => Yii::t('common', 'Roles'),
			'issueTypesIds' => Yii::t('common', 'Issue Types'),
			'calculationTypes' => Yii::t('common', 'Calculation Types'),
		]);
	}

	public function setModel(ProvisionType $model): void {
		$this->model = $model;
		$this->name = $model->name;
		$this->is_percentage = $model->is_percentage;
		$this->value = $model->value;
		$this->only_with_tele = $model->only_with_tele;
		$this->is_default = $model->is_default;
		$this->roles = $model->getRoles();
		$this->issueTypesIds = $model->getIssueTypesIds();
		$this->calculationTypes = $model->getCalculationTypes();
	}

	public function getModel(): ProvisionType {
		if ($this->model === null) {
			$this->model = new ProvisionType();
		}
		return $this->model;
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		$model = $this->getModel();
		$model->name = $this->name;
		$model->is_percentage = $this->is_percentage;
		$model->value = $this->value;
		$model->only_with_tele = $this->only_with_tele;
		$model->is_default = $this->is_default;
		$model->setRoles(is_array($this->roles) ? $this->roles : []);
		$model->setIssueTypesIds(is_array($this->issueTypesIds) ? $this->issueTypesIds : []);
		$model->setCalculationTypes(is_array($this->calculationTypes) ? $this->calculationTypes : []);
		return $model->save();
	}

	public static function getRolesNames(): array {
		return IssueUser::getTypesNames();
	}

	public static function getIssueTypesNames(): array {
		return IssueType::getTypesNames();
	}

	public static function getCalculationTypesNames(): array {
		return IssuePayCalculation::getTypesNames();
	}

}
