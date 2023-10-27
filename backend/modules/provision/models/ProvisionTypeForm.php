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

	public bool $is_default = false;
	public bool $with_hierarchy = true;
	public bool $isDateFromSettlement = false;
	public $issueUserType;
	public $issueTypesIds = [];
	public $issueStagesIds = [];
	public $settlementTypes = [];
	public $issueRequiredUserTypes = [];
	public $issueExcludedUserTypes = [];

	public ?string $from_at = null;
	public ?string $to_at = null;

	public ?string $minSettlementValue = null;
	public ?string $maxSettlementValue = null;

	public $baseTypeId;

	private ?IssueProvisionType $model = null;

	public static function getTypesNames(): array {
		return ProvisionType::getTypesNames(false);
	}

	public function rules(): array {
		return [
			[['name', 'value', 'is_percentage', 'issueUserType', 'is_active', 'with_hierarchy'], 'required'],
			['name', 'string', 'max' => 255],
			[
				'name', 'unique', 'targetClass' => IssueProvisionType::class,
				'filter' => function (QueryInterface $query): void {
					if (!$this->getModel()->isNewRecord) {
						$query->andWhere(['not', 'id' => $this->getModel()->id]);
					}
				},
			],
			[['from_at', 'to_at'], 'date', 'format' => 'Y-m-d'],
			[
				'to_at', 'compare', 'compareAttribute' => 'from_at', 'operator' => '>=',
				'enableClientValidation' => false,
			],
			[['value', 'minSettlementValue', 'maxSettlementValue'], 'number', 'min' => 0],
			[
				'value', 'number', 'max' => 100,
				'when' => function (): bool {
					return $this->is_percentage;
				},
				'enableClientValidation' => false,
			],
			[['baseTypeId'], 'integer'],
			[['is_default', 'is_percentage', 'is_active', 'with_hierarchy', 'isDateFromSettlement'], 'boolean'],
			['baseTypeId', 'default', 'value' => null],
			['baseTypeId', 'in', 'range' => array_keys(static::getTypesNames())],
			['settlementTypes', 'in', 'range' => array_keys(static::getSettlementTypesNames()), 'allowArray' => true],
			['issueTypesIds', 'in', 'range' => array_keys(static::getIssueTypesNames()), 'allowArray' => true],
			['issueStagesIds', 'in', 'range' => array_keys(static::getIssueStagesNames()), 'allowArray' => true],
			[['issueRequiredUserTypes', 'issueExcludedUserTypes'], 'in', 'range' => array_keys(static::getIssueUserTypesNames()), 'allowArray' => true],
			['issueUserType', 'in', 'range' => array_keys(static::getIssueUserTypesNames())],
			['settlementTypes', 'each', 'rule' => ['integer']],
		];
	}

	public function attributeLabels(): array {
		return array_merge($this->getModel()->attributeLabels(), [
			'settlementTypes' => Yii::t('settlement', 'Settlement type'),
			'issueStagesIds' => Yii::t('common', 'Issue Stages'),
			'issueTypesIds' => Yii::t('common', 'Issue Types'),
			'issueUserType' => Yii::t('provision', 'For whom'),
			'issueRequiredUserTypes' => Yii::t('provision', 'Required issue user types'),
			'issueExcludedUserTypes' => Yii::t('provision', 'Excluded issue user types'),
			'with_hierarchy' => Yii::t('provision', 'With hierarchy'),
			'isDateFromSettlement' => Yii::t('provision', 'Date from Settlement'),
			'minSettlementValue' => Yii::t('provision', 'Min Settlement Value'),
			'maxSettlementValue' => Yii::t('provision', 'Max Settlement Value'),
		]);
	}

	public function setModel(IssueProvisionType $model): void {
		$this->model = $model;
		$this->name = $model->name;
		$this->is_percentage = $model->is_percentage;
		$this->value = $model->value;
		$this->is_default = $model->is_default;
		$this->is_active = $model->is_active;
		$this->from_at = $model->from_at;
		$this->to_at = $model->to_at;
		$this->issueUserType = $model->getIssueUserType();
		$this->issueTypesIds = $model->getIssueTypesIds();
		$this->issueRequiredUserTypes = $model->getIssueRequiredUserTypes();
		$this->issueExcludedUserTypes = $model->getIssueExcludedUserTypes();
		$this->settlementTypes = $model->getSettlementTypes();
		$this->with_hierarchy = $model->getWithHierarchy();
		$this->baseTypeId = $model->getBaseTypeId();
		$this->isDateFromSettlement = $model->getIsForDateFromSettlement();
		$this->minSettlementValue = $model->getMinSettlementValue();
		$this->maxSettlementValue = $model->getMaxSettlementValue();
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
		$model->is_default = $this->is_default;
		$model->from_at = $this->from_at;
		$model->to_at = $this->to_at;
		$model->setBaseTypeId($this->baseTypeId);
		$model->setWithHierarchy($this->with_hierarchy);
		$model->setIsForDateFromSettlement($this->isDateFromSettlement);
		$model->setIssueUserTypes($this->issueUserType);
		$model->setIssueStagesIds(is_array($this->issueStagesIds) ? $this->issueStagesIds : []);
		$model->setIssueTypesIds(is_array($this->issueTypesIds) ? $this->issueTypesIds : []);
		$model->setIssueRequiredUserTypes(is_array($this->issueRequiredUserTypes) ? $this->issueRequiredUserTypes : []);
		$model->setIssueExcludedUserTypes(is_array($this->issueExcludedUserTypes) ? $this->issueExcludedUserTypes : []);
		$model->setSettlementTypes(is_array($this->settlementTypes) ? $this->settlementTypes : []);
		$model->setMinSettlementValue($this->minSettlementValue);
		$model->setMaxSettlementValue($this->maxSettlementValue);
		return $model->save();
	}

	public static function getSettlementTypesNames(): array {
		return IssueProvisionType::settlementTypesNames();
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
