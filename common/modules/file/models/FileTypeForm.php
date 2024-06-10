<?php

namespace common\modules\file\models;

use yii\base\Model;
use yii\db\QueryInterface;

class FileTypeForm extends Model {

	private ?FileType $model = null;

	public $name;
	public $is_active;
	public $visibility;

	private ?ValidatorOptions $options = null;

	private ?VisibilityOptions $visibilityOptions = null;

	public function attributeLabels(): array {
		return FileType::instance()->attributeLabels();
	}

	public function rules(): array {
		return [
			[['name', 'visibility', 'is_active'], 'required'],
			[
				'name',
				'unique',
				'targetClass' => FileType::class,
				'filter' => function (QueryInterface $query) {
					if (!$this->getModel()->isNewRecord) {
						$query->andWhere(['not', ['id' => $this->getModel()->id]]);
					}
				},
			],
			[['is_active'], 'boolean'],
			[['visibility'], 'string'],
		];
	}

	public static function getVisibilityNames(): array {
		return FileType::getVisibilityNames();
	}

	public function setModel(FileType $model): void {
		$this->model = $model;
		$this->name = $model->name;
		$this->is_active = $model->is_active;
		$this->visibility = $model->visibility;
		$this->options = $model->getValidatorOptions();
		$this->visibilityOptions = $model->getVisibilityOptions();
	}

	public function getModel(): FileType {
		if ($this->model === null) {
			$this->model = new FileType();
		}
		return $this->model;
	}

	public function getOptions(): ValidatorOptions {
		if ($this->options === null) {
			$this->options = new ValidatorOptions();
		}
		return $this->options;
	}

	public function getVisibility(): VisibilityOptions {
		if ($this->visibilityOptions === null) {
			$this->visibilityOptions = new VisibilityOptions();
		}
		return $this->visibilityOptions;
	}

	public function load($data, $formName = null) {
		return parent::load($data, $formName)
			&& $this->getOptions()->load($data, $formName)
			&& $this->getVisibility()->load($data, $formName);
	}

	public function validate($attributeNames = null, $clearErrors = true) {
		return parent::validate($attributeNames, $clearErrors)
			&& $this->getOptions()->validate($attributeNames, $clearErrors)
			&& $this->getVisibility()->validate($attributeNames, $clearErrors);
	}

	public function save(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		$model = $this->getModel();
		$model->name = $this->name;
		$model->visibility = $this->visibility;
		$model->is_active = $this->is_active;
		$model->validator_config = $this->getOptions()->toJson();
		$model->visibility_attributes = $this->getVisibility()->toJson();
		return $model->save();
	}

}
