<?php

namespace common\widgets\address;

use common\models\AddressSearch;
use yii\base\Widget;
use yii\widgets\ActiveForm;

class AddressSearchWidget extends Widget {

	public AddressSearch $model;
	public ActiveForm $form;

	public function run(): string {
		return $this->render('search', [
			'form' => $this->form,
			'model' => $this->model,
		]);
	}
}
