<?php

use common\models\provision\ProvisionSearch;
use common\widgets\DateTimeWidget;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ProvisionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="provision-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<div class="form-group row">


		<?= $form->field($model, 'dateFrom', ['options' => ['class' => 'col-md-4']])
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

		<?= $form->field($model, 'dateTo', ['options' => ['class' => 'col-md-4']])
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
		<?= $form->field($model, 'to_user_id', ['options' => ['class' => 'col-md-4']])
			->widget(Select2::class, [
					'data' => $model->getToUsersList(),
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
