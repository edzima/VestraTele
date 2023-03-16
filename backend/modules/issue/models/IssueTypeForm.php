<?php

namespace backend\modules\issue\models;

use common\helpers\ArrayHelper;
use common\models\issue\IssueType;
use Yii;
use yii\base\Model;
use yii\db\QueryInterface;

class IssueTypeForm extends Model {

	public string $name = '';
	public string $short_name = '';
	public string $vat = '';
	public ?bool $with_additional_date = null;
	public ?bool $default_show_linked_notes = null;
	public $parent_id;

	private ?IssueType $model = null;

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['name', 'short_name', 'vat'], 'required'],
			[['with_additional_date', 'default_show_linked_notes'], 'boolean'],
			[['name', 'short_name'], 'string', 'max' => 255],
			['vat', 'number', 'min' => 0, 'max' => 100],
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
		];
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
		$this->vat = $model->vat;
		$this->default_show_linked_notes = $model->default_show_linked_notes;
	}

	public function getParentsData(): array {
		$types = array_filter(IssueType::getTypes(), static function (IssueType $type): bool {
			return empty($type->parent_id);
		});
		$names = ArrayHelper::map($types, 'id', 'name');
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
			'vat' => 'VAT (%)',
			'with_additional_date' => Yii::t('common', 'With additional Date'),
			'parent_id' => Yii::t('issue', 'Type Parent'),
			'default_show_linked_notes' => Yii::t('issue', 'Default show Linked Notes'),
		];
	}

	public function save(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}

		$model = $this->getModel();
		$model->name = $this->name;
		$model->short_name = $this->short_name;
		$model->vat = $this->vat;
		$model->with_additional_date = $this->with_additional_date;
		$model->parent_id = $this->parent_id;
		$model->default_show_linked_notes = $this->default_show_linked_notes;
		return $model->save();
	}

}
