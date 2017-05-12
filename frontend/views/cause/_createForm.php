<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use demogorgorn\ajax\AjaxSubmitButton;


/* @var $this yii\web\View */
/* @var $model common\models\Cause */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cause-form">

    <?php $form = ActiveForm::begin([
        'options' => [
            'id' => 'cause-form'
        ]
    ]); ?>

    <?= $form->field($model, 'victim_name')->textInput(['maxlength' => true]) ?>



    <?= $form->field($model, 'category_id')->dropDownList($category) ?>


    <?= $form->field($model, 'date')->textInput(['readonly' => false]) ?>


    <div class="form-group">
    <?php

    AjaxSubmitButton::begin([
        'label' => Yii::t('frontend', 'Save'),
        'useWithActiveForm' => 'cause-form',
        'ajaxOptions' => [
            'type' => 'POST',
            'success' => new \yii\web\JsExpression("function(data) {

                if (data.status == true)
                {
                    console.log('success');
                    $('#calendar').fullCalendar('renderEvent', {
                        id: data.id,
                        title: $('#cause-victim_name').val(),
                        start: new Date($('#cause-date').val()),
                        url: data.url
       
                        },
                    true);
                    $('#modal').modal('toggle');
                
                }
                }"),
        ],
        'options' => ['class' => 'btn btn-success', 'type' => 'submit', 'id' => 'cause-submit'],
    ]);

    AjaxSubmitButton::end();

    ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
