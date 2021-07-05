<?php

namespace common\modules\reminder\widgets;

use common\modules\reminder\models\ReminderForm;
use yii\base\Widget;

class ReminderFormWidget extends Widget {

	public array $options = [
		'id' => 'reminder-form',
	];

	public ReminderForm $model;

	public function run(): string {
		return $this->render('form', [
			'model' => $this->model,
			'options' => $this->options,
		]);
	}
}
