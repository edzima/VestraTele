<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\Wojewodztwa;
/* @var $this yii\web\View */
/* @var $model common\models\Powiat */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="powiat-form">

    <?php $form = ActiveForm::begin(); ?>


   	<?=$form->field($model, 'wojewodztwo_id', ['options'=>['class'=>'col-md-4 form-group']])->widget(Select2::classname(), [
			'data' => ArrayHelper::map(Wojewodztwa::find()->all(), 'id', 'name'),
			'options' => [
				'placeholder' => '--Wybierz województwo--',
			],
			'pluginOptions' => [
				'allowClear' => true,
			]
		]
	)?>

    <?= $form->field($model, 'name', ['options'=>['class'=>'col-md-8 form-group']])->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Dodaj' : 'Zapisz', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
