<?php

use common\widgets\DateTimeWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $model common\models\ScoreSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="score-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

	<?= $form->field($model, 'start_at',['options'=>['class'=>'form-group col-md-6']])->widget(
        DateTimeWidget::class,
        [
            'phpDatetimeFormat' => 'yyyy-MM-dd',
			'clientOptions' => [
		
				'allowInputToggle' => true,
				'sideBySide' => true,
				'widgetPositioning' => [
				   'horizontal' => 'auto',
				   'vertical' => 'auto'
				],
			]
        ]
    )->label('Od:') ?>
	
	
	<?= $form->field($model, 'finish_at',['options'=>['class'=>'form-group col-md-6']])->widget(
		DateTimeWidget::class,
        [
            'phpDatetimeFormat' => 'yyyy-MM-dd',
			'clientOptions' => [
		
				'allowInputToggle' => true,
				'sideBySide' => true,
				'widgetPositioning' => [
				   'horizontal' => 'auto',
				   'vertical' => 'auto'
				],
			]
        ]
    )->label('Do:') ?>

    <?php // echo $form->field($model, 'date') ?>

    <div class="form-group">
        <?= Html::submitButton('Pokaż', ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Reset', ['index'], ['class' => 'btn btn-default'])?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
