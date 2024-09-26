<?php

namespace common\components\rbac\form;

use common\components\rbac\ModelAccess;
use yii\base\Model;

class ModelActionsForm extends Model {

	private ModelAccess $_access;

	public array $excludesFrontendActions = [];

	public function setAccess(ModelAccess $model) {
		$this->_access = $model;
	}

	private array $models = [];

	public function load($data, $formName = null) {
		return ModelRbacForm::loadMultiple($this->getModels(), $data, $formName);
	}

	/**
	 * @return ModelRbacForm[]
	 */
	public function getModels(): array {
		if (empty($this->models)) {
			$models = [];
			foreach ($this->_access->getActions() as $action) {
				$models[$action] = $this->createForm($action);
			}
			$this->models = $models;
		}
		return $this->models;
	}

	public function validate($attributeNames = null, $clearErrors = true) {
		return ModelRbacForm::validateMultiple($this->getModels(), $attributeNames);
	}

	public function save(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		foreach ($this->getModels() as $model) {
			$model->save(false);
		}
		return true;
	}

	private function createForm(string $action) {
		$this->_access->setAction($action);
		return new ModelRbacForm([
			'action' => $action,
			'access' => $this->_access,
		]);
	}
}
