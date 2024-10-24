<?php

namespace common\components\rbac\widget;

use common\components\rbac\form\SingleActionAccessForm;
use yii\base\Widget;
use yii\widgets\ActiveForm;

class SingleActionAccessFormWidget extends Widget {

	public SingleActionAccessForm $model;

	public ?ActiveForm $form = null;

	public function run(): string {
		return $this->render('single-form', [
			'model' => $this->model,
			'form' => $this->form,
		]);
	}
}
