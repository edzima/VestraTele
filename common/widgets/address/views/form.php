<?php

use common\models\Address;
use edzima\teryt\controllers\SimcController;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;
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
	])->widget(Select2::class, [
		'options' => ['placeholder' => Yii::t('address', 'Search for a city ...')],
		'initValueText' => $model->city_id ? $model->city->nameWithRegionAndDistrict : '',
		'pluginOptions' => [
			'allowClear' => true,
			'minimumInputLength' => SimcController::MIN_QUERY_LENGTH,
			'language' => [
				'errorLoading' => new JsExpression("function () { return '"
					. Yii::t('address', 'Waiting for results...')
					. "'; }"),
			],
			'ajax' => [
				'url' => Url::to(['/teryt/simc/city-list']),
				'dataType' => 'json',
				'data' => new JsExpression('function(params) { return {q:params.term}; }'),
			],
			'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
			'templateResult' => new JsExpression('function(city) { return city.text; }'),
			'templateSelection' => new JsExpression('function (city) { return city.text; }'),
		],
	]) ?>


	<?= $form->field($model, 'info',
		[
			'options' => ['class' => 'form-group col-sm-12 col-lg-5'],
		])->textInput(['maxlength' => true]) ?>


</div>



