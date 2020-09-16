<?php

namespace common\widgets\address;

use common\models\Address;
use yii\base\Widget;
use yii\widgets\ActiveForm;

class AddressFormWidget extends Widget {

	public ActiveForm $form;
	public Address $model;

	public function run(): string {
		return $this->render('form', [
			'form' => $this->form,
			'model' => $this->model,
		]);
	}
}
