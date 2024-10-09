<?php

namespace common\components\rbac\form;

use common\components\rbac\ModelAccessManager;
use yii\base\Model;

class ModelActionsForm extends Model {

	public array $appsActions = [
		'@frontendUrl' => [
			'issue.view',
		],
		'@backendUrl' => [
			'issue.view',
		],
	];

	private ModelAccessManager $_access;

	public array $excludesFrontendActions = [];

	public function setAccess(ModelAccessManager $model): void {
		$this->_access = $model;
	}

	private array $models = [];

	/**
	 * @return ModelRbacForm[]
	 */
	public function getModels(): array {
		if (empty($this->models)) {
			$models = [];
			foreach ($this->appsActions as $app => $actions) {
				foreach ($actions as $action) {
					$models[] = $this->createForm($action, $app);
				}
			}
			$this->models = $models;
		}
		return $this->models;
	}

	public function load($data, $formName = null) {
		return ModelRbacForm::loadMultiple($this->getModels(), $data, $formName);
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

	private function createForm(string $action, ?string $app) {
		return new ModelRbacForm([
			'action' => $action,
			'app' => $app,
			'access' => $this->_access,
		]);
	}
}
