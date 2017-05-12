<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CauseSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cause-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'victim_name') ?>

    <?= $form->field($model, 'author_id') ?>

    <?= $form->field($model, 'created_at') ?>

    <?= $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'category_id') ?>

    <?php // echo $form->field($model, 'is_finished') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('frontend', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('frontend', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
