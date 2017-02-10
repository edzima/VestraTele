<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TaskSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="task-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'tele_id') ?>

    <?= $form->field($model, 'agent_id') ?>

    <?= $form->field($model, 'victim_name') ?>

    <?= $form->field($model, 'phone') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'accident_id') ?>

    <?php // echo $form->field($model, 'woj') ?>

    <?php // echo $form->field($model, 'powiat') ?>

    <?php // echo $form->field($model, 'gmina') ?>

    <?php // echo $form->field($model, 'city') ?>

    <?php // echo $form->field($model, 'qualified_name') ?>

    <?php // echo $form->field($model, 'details') ?>

    <?php // echo $form->field($model, 'meeting') ?>

    <?php // echo $form->field($model, 'date') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
