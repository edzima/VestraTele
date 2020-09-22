<?php

use common\widgets\DateTimeWidget;
use frontend\models\SummonForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model SummonForm */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="summon-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">

		<?= $form->field($model, 'status', [
			'options' => [
				'class' => 'col-md-3',
			],
		])->dropDownList(SummonForm::getStatusesNames()) ?>

		<?= $form->field($model, 'realize_at', [
			'options' => [
				'class' => 'col-md-2',
			],
		])
			->widget(DateTimeWidget::class) ?>

		<?= $model->getModel()->isRealized() ? $form->field($model, 'realized_at', [
			'options' => [
				'class' => 'col-md-2',
			],
		])
			->widget(DateTimeWidget::class) : '' ?>

	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('common', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
