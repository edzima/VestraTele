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

    <?= $form->field($model, 'name',[
            'options'=>[
                'class'=>'col-md-6'
            ]
    ])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model,'period',[
        'options'=>[
            'class'=>'col-md-3'
        ]
    ])->textInput(['type' => 'number', 'min'=>0]) ?>
    <div class="col-md-3 form-group">
        <?php
        echo '<label class="control-label">Kolor tła</label>';
        echo ColorInput::widget([
            'model'=>$model,

            'attribute' =>'color',
            'options' => [ 'class' =>'col-md-2','readonly' => true]
        ]);
        ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('frontend', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>


    <?php ActiveForm::end(); ?>

</div>
