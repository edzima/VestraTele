<?php

namespace backend\modules\settlement\models;

use common\models\issue\IssueType;
use common\models\settlement\SettlementType;
use common\models\settlement\SettlementTypeOptions;
use Yii;
use yii\base\Model;
use yii\db\QueryInterface;

class SettlementTypeForm extends Model {

	public $name;
	public $is_active;
	public $issueTypesIds;
	public ?int $visibility_status = SettlementType::VISIBILITY_ONLY_OWNER;

	private ?SettlementType $model = null;
	private ?SettlementTypeOptions $options = null;

	public function rules(): array {
		return [
			[['name', 'is_active'], 'required'],
			[['visibility_status'], 'integer'],
			['issueTypesIds', 'each', 'rule' => ['integer']],
			[
				'name', 'unique',
				'targetClass' => SettlementType::class,
				'filter' => function (QueryInterface $query) {
					if (!$this->getModel()->isNewRecord) {
						$query->andWhere(['not', ['id' => $this->getModel()->id]]);
					}
				},
			],
			['visibility_status', 'in', 'range' => array_keys($this->visibilityNames())],
		];
	}

	public function attributeLabels(): array {
		return array_merge(SettlementType::instance()->attributeLabels(), [
			'issueTypesIds' => Yii::t('settlement', 'Issue Types'),
			'visibility_status' => Yii::t('settlement', 'Visibility'),
		]);
	}

	public function setModel(SettlementType $model): void {
		$this->model = $model;
		$this->name = $model->name;
		$this->is_active = $model->is_active;
		$this->visibility_status = $model->visibility_status;
		$this->issueTypesIds = $model->getIssueTypesIds();
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
		$model->visibility_status = $this->visibility_status;
		$model->setTypeOptions($this->getOptions());
		$model->options = $this->getOptions()->toJson();
		if (!$model->save()) {
			return false;
		}
		Yii::warning($this->getIssueTypesIds());
		$model->linkIssueTypes($this->getIssueTypesIds());
		return $model->save();
	}

	protected function getIssueTypesIds(): array {
		if (empty($this->issueTypesIds)) {
			return [];
		}
		return (array) $this->issueTypesIds;
	}

	public function getModel(): SettlementType {
		if ($this->model === null) {
			$this->model = new SettlementType();
		}
		return $this->model;
	}

	public function getIssueTypesNames(): array {
		return IssueType::getTypesNames();
	}

	public function getOptions(): SettlementTypeOptions {
		if ($this->options === null) {
			$this->options = $this->getModel()->getTypeOptions();
		}
		return $this->options;
	}

	public function visibilityNames(): array {
		return SettlementType::visibilityNames();
	}
}
