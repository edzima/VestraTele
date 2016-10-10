<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\LocationSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="location-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'WOJ') ?>

    <?= $form->field($model, 'POW') ?>

    <?= $form->field($model, 'GMI') ?>

    <?= $form->field($model, 'RODZ_GMI') ?>

    <?= $form->field($model, 'RM') ?>

    <?php // echo $form->field($model, 'MZ') ?>

    <?php // echo $form->field($model, 'NAZWA') ?>

    <?php // echo $form->field($model, 'SYM') ?>

    <?php // echo $form->field($model, 'SYMPOD') ?>

    <?php // echo $form->field($model, 'STAN_NA') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
