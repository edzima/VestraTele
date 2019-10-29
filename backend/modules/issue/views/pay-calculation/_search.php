<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \backend\modules\issue\models\searches\IssuePayCalculationSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="issue-pay-calculation-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'issue_id') ?>

    <?= $form->field($model, 'status') ?>

    <?= $form->field($model, 'value') ?>

    <?= $form->field($model, 'pay_type') ?>

    <?= $form->field($model, 'details') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
