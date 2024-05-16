<?php

use backend\modules\issue\models\search\TagTypeSearch;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model TagTypeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="issue-tag-type-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'background') ?>

    <?= $form->field($model, 'color') ?>

    <?= $form->field($model, 'css-class') ?>

    <?php // echo $form->field($model, 'view_issue_position') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('backend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('backend', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
