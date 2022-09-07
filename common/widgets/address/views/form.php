<?php

use common\models\Address;
use common\widgets\address\CitySimcInputWidget;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model Address; */
/* @var $form ActiveForm */

?>

<div class="row">

	<?= $form->field($model, 'postal_code',
		[
			'options' => ['class' => 'form-group col-sm-2 col-lg-2'],
		])->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'city_id', [
		'options' => ['class' => 'form-group col-sm-10 col-lg-5'],
	])->widget(CitySimcInputWidget::class) ?>


	<?= $form->field($model, 'info',
		[
			'options' => ['class' => 'form-group col-sm-12 col-lg-5'],
		])->textInput(['maxlength' => true]) ?>


</div>



