<?php

namespace common\components\rbac\form;

use common\components\rbac\ModelAccessManager;
use Yii;
use yii\base\Model;

class ModelActionsForm extends Model {

	public array $appsActions = [];

	private ModelAccessManager $_access;

	public $formConfig = [
		'class' => ModelRbacForm::class,
	];

	public function setAccess(ModelAccessManager $model): void {
		$this->_access = $model;
		if (empty($this->appsActions)) {
			$this->appsActions = $model->appsActions;
		}
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

	private function createForm(string $action, string $app): ModelRbacForm {
		$access = clone $this->_access;
		$access->setAction($action)
			->setApp($app);
		$config = $this->formConfig;
		if (!isset($config['class'])) {
			$config['class'] = ModelRbacForm::class;
		}
		return Yii::createObject($config, [$access]);
	}
}
