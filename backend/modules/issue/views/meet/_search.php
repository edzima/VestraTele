<?php

use common\models\issue\IssueMeetSearch;
use common\widgets\address\AddressSearchWidget;
use common\widgets\DateTimeWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model IssueMeetSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="issue-meet-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<div class="row">
		<?= $form->field($model, 'created_at_from', [
			'options' => [
				'class' => 'col-md-3',
			],
		])->widget(DateTimeWidget::class)
		?>

		<?= $form->field($model, 'created_at_to', [
			'options' => [
				'class' => 'col-md-3',
			],
		])->widget(DateTimeWidget::class)
		?>


		<?= $form->field($model, 'date_at_from', [
			'options' => [
				'class' => 'col-md-3',
			],
		])->widget(DateTimeWidget::class)
		?>

		<?= $form->field($model, 'date_at_to', [
			'options' => [
				'class' => 'col-md-3',
			],
		])->widget(DateTimeWidget::class)
		?>

	</div>

	<?= AddressSearchWidget::widget([
		'form' => $form,
		'model' => $model->getAddressSearch(),
	]) ?>


	<div class="form-group">
		<?= Html::submitButton('Szukaj', ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Reset', Yii::$app->controller->action->id, ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>



