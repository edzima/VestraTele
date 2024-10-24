<?php

namespace common\components\rbac\form;

use common\components\rbac\ModelAccessManager;
use Yii;
use yii\base\Model;

class ActionsAccessForm extends Model {

	public array $appsActions = [];

	private ModelAccessManager $_access;

	public $formConfig = [
		'class' => SingleActionAccessForm::class,
	];

	public function setAccess(ModelAccessManager $model): void {
		$this->_access = $model;
		if (empty($this->appsActions)) {
			$this->appsActions = $model->getAppsActions();
		}
	}

	private array $models = [];

	/**
	 * @return SingleActionAccessForm[]
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
		return SingleActionAccessForm::loadMultiple($this->getModels(), $data, $formName);
	}

	public function validate($attributeNames = null, $clearErrors = true) {
		return SingleActionAccessForm::validateMultiple($this->getModels(), $attributeNames);
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

	private function createForm(string $action, string $app): SingleActionAccessForm {
		$access = clone $this->_access;
		$access->setAction($action)
			->setApp($app);
		$config = $this->formConfig;
		if (!isset($config['class'])) {
			$config['class'] = SingleActionAccessForm::class;
		}
		return Yii::createObject($config, [$access]);
	}
}
