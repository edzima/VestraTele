<?php

use common\models\provision\search\ReportSearch;
use common\widgets\DateTimeWidget;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ReportSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="provision-report-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<div class="form-group row">


		<?= $form->field($model, 'dateFrom', ['options' => ['class' => 'col-md-4']])
			->widget(DateTimeWidget::class)
		?>

		<?= $form->field($model, 'dateTo', ['options' => ['class' => 'col-md-4']])
			->widget(DateTimeWidget::class)
		?>
		<?= $form->field($model, 'user_id', ['options' => ['class' => 'col-md-4']])
			->widget(Select2::class, [
					'data' => $model->getUsersList(),
					'options' => [
						'placeholder' => Yii::t('provision', 'User'),
					],
					'pluginOptions' => [
						'allowClear' => true,
					],
				]
			) ?>

	</div>


	<div class="form-group">
		<?= Html::submitButton('Szukaj', ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Reset', 'index', ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
