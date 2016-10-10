<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Location */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="location-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'WOJ')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'POW')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'GMI')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'RODZ_GMI')->textInput() ?>

    <?= $form->field($model, 'RM')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'MZ')->dropDownList([ 'tak' => 'Tak', 'nie' => 'Nie', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'NAZWA')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'SYM')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'SYMPOD')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'STAN_NA')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
