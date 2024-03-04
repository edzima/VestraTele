<?php

namespace common\widgets;

use common\models\forms\JsonModel;
use yii\base\Widget;

class JsonFormWidget extends Widget {

	public ?ActiveForm $form = null;

	public JsonModel $model;

	public array $formOptions = [];

	public ?string $viewTitle;

	public function init(): void {
		if ($this->viewTitle) {
			$this->view->title = $this->viewTitle;
		}
		parent::init();
	}

	public function run(): string {
		return $this->render('json-form', [
			'model' => $this->model,
			'form' => $this->form,
			'formOptions' => $this->formOptions,
		]);
	}
}
