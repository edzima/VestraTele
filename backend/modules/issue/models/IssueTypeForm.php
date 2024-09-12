<?php

namespace backend\modules\issue\models;

use common\models\issue\IssueType;
use Yii;
use yii\base\Model;
use yii\db\QueryInterface;

class IssueTypeForm extends Model {

	public string $name = '';
	public string $short_name = '';
	public ?bool $with_additional_date = null;
	public ?bool $default_show_linked_notes = null;
	public ?bool $is_main = null;
	public $parent_id;
	public $main_type_id;
	public $lead_source_id;

	public $authManager = 'authManager';

	private ?IssueType $model = null;

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['name', 'short_name'], 'required'],
			[['with_additional_date', 'default_show_linked_notes', 'is_main'], 'boolean'],
			[['name', 'short_name'], 'string', 'max' => 255],
			['lead_source_id', 'integer', 'min' => 1],
			[
				['name'], 'unique', 'targetClass' => IssueType::class, 'filter' => function (QueryInterface $query) {
				if (!$this->getModel()->isNewRecord) {
					$query->andWhere(['not', ['id' => $this->getModel()->id]]);
				}
			},
			],
			[
				['short_name'], 'unique', 'targetClass' => IssueType::class, 'filter' => function (QueryInterface $query) {
				if (!$this->getModel()->isNewRecord) {
					$query->andWhere(['not', ['id' => $this->getModel()->id]]);
				}
			},
			],
			['parent_id', 'in', 'range' => array_keys($this->getParentsData())],
			['parent_id', 'detectLoop'],
		];
	}

	public function detectLoop(): void {
		if ($this->parent_id && !$this->getModel()->isNewRecord) {
			if ($this->getModel()->detectLoop($this->parent_id)) {
				$this->addError('parent_id', Yii::t('backend', 'Detect loop'));
			}
		}
	}

	public function getModel(): IssueType {
		if ($this->model === null) {
			$this->model = new IssueType();
		}
		return $this->model;
	}

	public function setModel(IssueType $model): void {
		$this->model = $model;
		$this->name = $model->name;
		$this->short_name = $model->short_name;
		$this->with_additional_date = $model->with_additional_date;
		$this->parent_id = $model->parent_id;
		$this->default_show_linked_notes = $model->default_show_linked_notes;
		$this->lead_source_id = $model->lead_source_id;
		$this->is_main = $model->is_main;
	}

	public function getParentsData(): array {
		$names = IssueType::getTypesNames();
		if ($this->getModel()->id) {
			unset($names[$this->getModel()->id]);
		}
		return $names;
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'name' => Yii::t('common', 'Name'),
			'short_name' => Yii::t('common', 'Shortname'),
			'with_additional_date' => Yii::t('common', 'With additional Date'),
			'parent_id' => Yii::t('issue', 'Type Parent'),
			'default_show_linked_notes' => Yii::t('issue', 'Default show Linked Notes'),
			'lead_source_id' => Yii::t('issue', 'Lead Source for Created Issues'),
			'is_main' => Yii::t('issue', 'Is main Type'),
		];
	}

	public function save(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}

		$model = $this->getModel();
		$model->name = $this->name;
		$model->short_name = $this->short_name;
		$model->with_additional_date = $this->with_additional_date;
		$model->parent_id = $this->parent_id;
		$model->default_show_linked_notes = $this->default_show_linked_notes;
		$model->lead_source_id = $this->lead_source_id;
		$model->is_main = $this->is_main;
		if (!$model->save()) {
			return false;
		}

		return true;
	}

}
