<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

use yii\widgets\ActiveForm;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use kartik\icons\Icon;

use trntv\yii\datetime\DateTimeWidget;
/* @var $this yii\web\View */
/* @var $model common\models\Task */
/* @var $woj common\models\Task */
/* @var $accident common\models\Task */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-form">

    <?php $form = ActiveForm::begin(); ?>
		
	<i class="fa fa-user-secret" aria-hidden="true"></i>

	<?php 
	
	echo $form->field($model, 'agent_id')->dropDownList($agent);?>

    <?= $form->field($model, 'victim_name')->textInput(['maxlength' => true]) ?>
	
	<?= $form->field($model, 'qualified_name')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'phone')->widget(\yii\widgets\MaskedInput::className(), [
			'mask' => '999-999-9999',
		]) 
	?>

	<?= $form->field($model, 'accident_id')->dropDownList($accident)?>
	
	
	<?php
	//wojewodztwo
	echo $form->field($model, 'woj')->widget(Select2::classname(), [
			'data' => $woj,
			'options' => [
				'placeholder' => '--Wybierz województwo--',
				'id' => 'cat-id',
			],
		]
	);
	
	//powiat
	echo $form->field($model, 'powiat')->widget(DepDrop::classname(), [
		'type'=>DepDrop::TYPE_SELECT2,
		'options'=>['id'=>'subcat-id'],
		'pluginOptions'=>[
			'depends'=>['cat-id'],
			'placeholder'=>'Powiat...',
			'url'=>Url::to(['/task/powiat']),
			'loading' =>'wyszukiwanie...'
		]
	]);

	// gmina
	echo $form->field($model, 'gmina')->widget(DepDrop::classname(), [
		'type'=>DepDrop::TYPE_SELECT2,
		'pluginOptions'=>[
			'depends'=>['cat-id', 'subcat-id'],
			'placeholder'=>'Gmina...',
			'url'=>Url::to(['/task/gmina']),
		]
	]);
	
	// miasto
	echo $form->field($model, 'city')->widget(DepDrop::classname(), [
		'type'=>DepDrop::TYPE_SELECT2,
		'pluginOptions'=>[
			'depends'=>['cat-id', 'subcat-id'],
			'placeholder'=>'Miejscowość...',
			'url'=>Url::to(['/task/city']),
		]
	]);

	?>


	<?= $form->field($model, 'details')->textArea(['rows'=>4]) ?>
	
	<?=  $form->field($model, 'meeting')->checkBox()?>
	
	<?= $form->field($model, 'date')->widget(
        DateTimeWidget::className(),
        [   'phpDatetimeFormat' => 'dd-MM-yyyy HH:mm',
            'clientOptions' => [
		
				'allowInputToggle' => true,
				'sideBySide' => true,
				'widgetPositioning' => [
				   'horizontal' => 'auto',
				   'vertical' => 'auto'
				],
			]
        ]
    ) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Aktualizuj', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
