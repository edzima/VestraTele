<?php

namespace common\modules\lead\widgets;

use common\modules\lead\models\forms\ReportForm;
use yii\base\Widget;
use yii\widgets\ActiveForm;

class ReportFormWidget extends Widget {

	public ReportForm $model;
	public ActiveForm $form;

	public array $formOptions = [];

	public function run(): string {
		return $this->render('report-form', [
			'form' => $this->form,
			'model' => $this->model,
		]);
	}
}
