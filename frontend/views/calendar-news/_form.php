<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CalendarNews */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="calendar-news-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'news')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'agent_id')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
