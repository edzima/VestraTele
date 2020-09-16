<?php

namespace backend\modules\user\widgets;

use common\models\user\UserProfile;
use yii\base\Widget;
use yii\widgets\ActiveForm;

class UserProfileFormWidget extends Widget {

	public UserProfile $model;
	public ActiveForm $form;

	public function run(): string {
		return $this->render('form', [
			'model' => $this->model,
			'form' => $this->form,
		]);
	}
}
