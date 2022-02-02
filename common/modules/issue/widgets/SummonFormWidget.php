<?php

namespace common\modules\issue\widgets;

use common\models\issue\form\SummonForm;
use yii\base\Widget;

class SummonFormWidget extends Widget {

	public SummonForm $model;

	public function run(): string {
		return $this->render('summon-form', [
			'model' => $this->model,
		]);
	}
}
