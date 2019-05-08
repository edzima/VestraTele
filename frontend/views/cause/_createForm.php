<?php

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

    <?= $form->field($model, 'victim_name',
        [
            'template' => '<div class="input-group"><span class="input-group-addon"><i class="fa fa-blind fa-lg"></i> Poszkodowany</span>{input}</div>'
        ])
        ->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'category_id',
        [
            'template' => '<div class="input-group"><span class="input-group-addon"><i class="fa fa-line-chart"></i> Etap</span>{input}</div>',

        ])
        ->dropDownList($category) ?>



    <?= $form->field($model, 'date',
        [
            'template' => '<div class="input-group"><span class="input-group-addon"><i class="fa fa-calendar"></i> Rozpoczęcie etapu</span>{input}</div>',

        ])->textInput(['readonly' => false]) ?>


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
                   /* console.log('success');
                    $('#calendar').fullCalendar('renderEvent', {
                        id: data.id,
                        title: $('#cause-victim_name').val(),
                        start: new Date($('#cause-date').val()),
                        url: data.url
       
                        },
                    true);
                    */
                    $('#calendar').fullCalendar('refetchEvents');
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
