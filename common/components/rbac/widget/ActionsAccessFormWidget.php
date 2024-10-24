<?php

namespace common\components\rbac\widget;

use common\components\rbac\form\ActionsAccessForm;
use yii\base\Widget;
use yii\widgets\ActiveForm;

class ActionsAccessFormWidget extends Widget {

	public ActionsAccessForm $model;

	public ?ActiveForm $form = null;

	public function run(): string {
		return $this->render('form', [
			'model' => $this->model,
			'form' => $this->form,
		]);
	}
}
