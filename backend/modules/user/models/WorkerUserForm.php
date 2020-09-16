<?php

namespace backend\modules\user\models;

use common\models\user\User;
use common\models\user\Worker;

class WorkerUserForm extends UserForm {

	public $parent_id;

	public function rules(): array {
		return array_merge(parent::rules(), [

			[['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['parent_id' => 'id']],
			['parent_id', 'in', 'range' => array_keys($this->getParents())],
		]);
	}

	public function attributeLabels(): array {
		return parent::attributeLabels()
			+ [
				'parent_id' => \Yii::t('backend', 'Parent'),
			];
	}

	public function getModel(): Worker {
		return parent::getModel();
	}

	protected function createModel(): Worker {
		return new Worker();
	}

	public function setModel(User $model): void {
		parent::setModel($model);
		$this->parent_id = $model->boss;
	}

	protected function beforeSaveModel(User $model): void {
		parent::beforeSaveModel($model);
		$model->boss = $this->parent_id;
	}

	public function getParents(): array {
		$list = Worker::getSelectList([Worker::ROLE_AGENT]);
		if (isset($list[$this->getModel()->id])) {
			unset($list[$this->getModel()->id]);
		}
		return $list;
	}

}
