<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

use common\models\Wojewodztwa;
use yii\widgets\ActiveForm;

use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\City */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="city-form">

    <?php $form = ActiveForm::begin(); ?>

	
	<?php
	//wojewodztwo
	echo $form->field($city, 'wojewodztwo_id', ['options'=>['class'=>'col-md-4 form-group']])->widget(Select2::classname(), [
			'data' => ArrayHelper::map(Wojewodztwa::find()->all(), 'id', 'name'),
			'options' => [
				'placeholder' => '--Wybierz województwo--',
				'id' => 'cat-id',
			],
			'pluginOptions' => [
				'allowClear' => true,
			]
		]
	);
	
	//powiat
	echo $form->field($city, 'powiat_id',['options'=>['class'=>'col-md-4 form-group']])->widget(DepDrop::classname(), [
		'type'=>DepDrop::TYPE_SELECT2,
		'options'=>['id'=>'subcat-id'],
		'pluginOptions'=>[
			'depends'=>['cat-id'],
			'placeholder'=>'Powiat...',
			'url'=>Url::to(['/city/powiat']),
			'loading' =>'wyszukiwanie...'
		]
	]);
	?>
	
    <?= $form->field($city, 'name',['options'=>['class'=>'col-md-4 form-group']])->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($city->isNewRecord ? 'Dodaj' : 'Zapisz', ['class' => $city->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
