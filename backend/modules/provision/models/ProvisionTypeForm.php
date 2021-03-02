<?php

namespace backend\modules\provision\models;

use common\models\provision\IssueProvisionType;
use common\models\provision\ProvisionType;
use Yii;
use yii\base\Model;
use yii\db\QueryInterface;

class ProvisionTypeForm extends Model {

	public string $name = '';
	public bool $is_active = true;
	public bool $is_percentage = true;
	public string $value = '';

	public bool $only_with_tele = false;
	public bool $is_default = false;
	public bool $with_hierarchy = true;
	public $issueUserType;
	public $issueTypesIds = [];
	public $issueStagesIds = [];
	public $calculationTypes = [];

	public ?string $from_at = null;
	public ?string $to_at = null;

	private ?IssueProvisionType $model = null;

	public function rules(): array {
		return [
			[['name', 'value', 'is_percentage', 'issueUserType', 'is_active', 'with_hierarchy'], 'required'],
			['name', 'string', 'max' => 50],
			[
				'name', 'unique', 'targetClass' => ProvisionType::class,
				'filter' => function (QueryInterface $query): void {
					if (!$this->getModel()->isNewRecord) {
						$query->andWhere(['not', 'id' => $this->getModel()->id]);
					}
				},
			],
			[['from_at', 'to_at'], 'date', 'format' => 'Y-m-d'],
			['value', 'number', 'min' => 0],
			[
				'value', 'number', 'max' => 100,
				'when' => function (): bool {
					return $this->is_percentage;
				},
			],
			[['only_with_tele', 'is_default', 'is_percentage', 'is_active', 'with_hierarchy'], 'boolean'],
			['calculationTypes', 'in', 'range' => array_keys(static::getCalculationTypesNames()), 'allowArray' => true],
			['issueTypesIds', 'in', 'range' => array_keys(static::getIssueTypesNames()), 'allowArray' => true],
			['issueStagesIds', 'in', 'range' => array_keys(static::getIssueStagesNames()), 'allowArray' => true],
			['issueUserType', 'in', 'range' => array_keys(static::getIssueUserTypesNames())],
			['calculationTypes', 'each', 'rule' => ['integer']],
		];
	}

	public function attributeLabels(): array {
		return array_merge($this->getModel()->attributeLabels(), [
			'calculationTypes' => Yii::t('settlement', 'Settlement type'),
			'issueStagesIds' => Yii::t('common', 'Issue Stages'),
			'issueTypesIds' => Yii::t('common', 'Issue Types'),
			'issueUserType' => Yii::t('common', 'Issue user type'),
		]);
	}

	public function setModel(ProvisionType $model): void {
		$this->model = $model;
		$this->name = $model->name;
		$this->is_percentage = $model->is_percentage;
		$this->value = $model->value;
		$this->only_with_tele = $model->only_with_tele;
		$this->is_default = $model->is_default;
		$this->is_active = $model->is_active;
		$this->from_at = $model->from_at;
		$this->issueUserType = $model->getIssueUserType();
		$this->issueTypesIds = $model->getIssueTypesIds();
		$this->calculationTypes = $model->getCalculationTypes();
		$this->with_hierarchy = $model->getWithHierarchy();
	}

	public function getModel(): IssueProvisionType {
		if ($this->model === null) {
			$this->model = new IssueProvisionType();
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
		$model->is_active = $this->is_active;
		$model->value = $this->value;
		$model->only_with_tele = $this->only_with_tele;
		$model->is_default = $this->is_default;
		$model->from_at = $this->from_at;
		$model->to_at = $this->to_at;
		$model->setWithHierarchy($this->with_hierarchy);
		$model->setIssueUserTypes($this->issueUserType);
		$model->setIssueStagesIds(is_array($this->issueStagesIds) ? $this->issueStagesIds : []);
		$model->setIssueTypesIds(is_array($this->issueTypesIds) ? $this->issueTypesIds : []);
		$model->setCalculationTypes(is_array($this->calculationTypes) ? $this->calculationTypes : []);
		return $model->save();
	}

	public static function getCalculationTypesNames(): array {
		return IssueProvisionType::calculationTypesNames();
	}

	public static function getIssueStagesNames(): array {
		return IssueProvisionType::issueStagesNames();
	}

	public static function getIssueTypesNames(): array {
		return IssueProvisionType::issueTypesNames();
	}

	public static function getIssueUserTypesNames(): array {
		return IssueProvisionType::issueUserTypesNames();
	}

}
