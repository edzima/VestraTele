<?php

use common\widgets\DateTimeWidget;
use frontend\models\IssueMeetSearch;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model IssueMeetSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="issue-meet-search">

	<?php $form = ActiveForm::begin([
		'action' => [Yii::$app->controller->action->id],
		'method' => 'get',
	]); ?>

	<div class="row">
		<?= $form->field($model, 'created_at_from', [
			'options' => [
				'class' => 'col-md-3',
			],
		])
			->widget(DateTimeWidget::class, [
				'phpDatetimeFormat' => 'yyyy-MM-dd HH:mm',
			]) ?>

		<?= $form->field($model, 'created_at_to', [
			'options' => [
				'class' => 'col-md-3',
			],
		])
			->widget(DateTimeWidget::class, [
				'phpDatetimeFormat' => 'yyyy-MM-dd HH:mm',
			]) ?>


		<?= $form->field($model, 'date_at_from', [
			'options' => [
				'class' => 'col-md-3',
			],
		])
			->widget(DateTimeWidget::class, [
				'phpDatetimeFormat' => 'yyyy-MM-dd HH:mm',
			]) ?>

		<?= $form->field($model, 'date_at_to', [
			'options' => [
				'class' => 'col-md-3',
			],
		])
			->widget(DateTimeWidget::class, [
				'phpDatetimeFormat' => 'yyyy-MM-dd HH:mm',
			]) ?>

	</div>


	<div class="form-group">
		<?= Html::submitButton('Szukaj', ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Reset', Yii::$app->controller->action->id, ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
