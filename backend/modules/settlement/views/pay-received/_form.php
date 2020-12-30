<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\settlement\PayReceived */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pay-received-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'pay_id')->textInput() ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'date_at')->textInput() ?>

    <?= $form->field($model, 'transfer_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('settlement', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
