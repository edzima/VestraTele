<?php

namespace common\modules\calendar\widgets;

use common\modules\calendar\models\FilterOptions;
use yii\base\Widget;
use yii\widgets\ActiveForm;

class FilterForm extends Widget {

	public ActiveForm $form;
	public FilterOptions $model;

	public function run(): string {
		return $this->render('filter-form', [
			'form' => $this->form,
			'model' => $this->model,
		]);
	}
}
