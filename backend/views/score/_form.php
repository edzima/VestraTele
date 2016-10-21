<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use trntv\yii\datetime\DateTimeWidget;
/* @var $this yii\web\View */
/* @var $model common\models\Score */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="score-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'task_id')->textInput() ?>

	<?= $form->field($model, 'connexion')->dropDownList($connexion)?>

    <?= $form->field($model, 'score')->textInput() ?>
	
	<?= $form->field($model, 'name')->textInput() ?>

    	<?= $form->field($model, 'date')->widget(
        DateTimeWidget::className(),
        [   'phpDatetimeFormat' => 'yyyy-MM-dd',
            'clientOptions' => [
		
				'allowInputToggle' => true,
				'sideBySide' => true,
				'widgetPositioning' => [
				   'horizontal' => 'auto',
				   'vertical' => 'auto'
				],
			]
        ]
    ) ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>