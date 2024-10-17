<?php

namespace common\components\rbac\form;

use common\components\rbac\ModelAccessManager;
use yii\base\Model;

class ModelActionsForm extends Model {

	public array $appsActions = [
		ModelAccessManager::APP_FRONTEND => [
			ModelAccessManager::ACTION_VIEW,
		],
		ModelAccessManager::APP_BACKEND => [
			ModelAccessManager::ACTION_VIEW,
		],
	];

	private ModelAccessManager $_access;

	public function setAccess(ModelAccessManager $model): void {
		$this->_access = $model;
		$appsActions = [];
		foreach ($model->availableApps as $app) {
			foreach ($model->getActions() as $action) {
				$appsActions[$app][] = $action;
			}
		}
		$this->appsActions = $appsActions;
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

	private function createForm(string $action, string $app) {
		$access = clone $this->_access;
		$access->setAction($action)
			->setApp($app);
		$model = new ModelRbacForm($access);
		return $model;
	}
}
