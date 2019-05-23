<?php

use backend\modules\issue\models\PayForm;
use common\models\issue\IssuePay;
use common\widgets\DateTimeWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model PayForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="issue-pay-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'date')
		->widget(DateTimeWidget::class,
			[
				'phpDatetimeFormat' => 'yyyy-MM-dd',
				'clientOptions' => [

					'allowInputToggle' => true,
					'sideBySide' => true,
					'widgetPositioning' => [
						'horizontal' => 'auto',
						'vertical' => 'auto',
					],
				],
			]) ?>

	<?= $form->field($model, 'type')->dropDownList(IssuePay::getTypesNames()) ?>


	<?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model, 'last')->checkbox() ?>


	<div class="form-group">
		<?= Html::submitButton('Zapisz', ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
