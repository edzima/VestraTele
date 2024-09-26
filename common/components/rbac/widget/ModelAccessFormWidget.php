<?php

namespace common\components\rbac\widget;

use common\components\rbac\form\ModelActionsForm;
use yii\base\Widget;
use yii\widgets\ActiveForm;

class ModelAccessFormWidget extends Widget {

	public ModelActionsForm $model;

	public ?ActiveForm $form = null;

	public function run(): string {
		return $this->render('form', [
			'model' => $this->model,
			'form' => $this->form,
		]);
	}
}
