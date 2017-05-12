<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use  kartik\color\ColorInput;

/* @var $this yii\web\View */
/* @var $model common\models\CauseCategory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cause-category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'period')->textInput() ?>

    <?php
    echo '<label class="control-label">Kolor tła</label>';
    echo ColorInput::widget([
        'model'=>$model,
        'attribute' =>'color',
        'options' => ['readonly' => true]
    ]);
    ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('frontend', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>


    <?php ActiveForm::end(); ?>

</div>
