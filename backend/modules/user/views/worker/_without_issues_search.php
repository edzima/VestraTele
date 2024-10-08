<?php

use backend\modules\user\models\search\WorkersWithoutIssuesSearch;
use common\widgets\DateTimeWidget;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model WorkersWithoutIssuesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-search">

	<?php $form = ActiveForm::begin([
		'action' => ['without-issues'],
		'method' => 'get',
	]); ?>

	<div class="row">
		<?= $form->field($model, 'createdAtFrom', ['options' => ['class' => 'col-md-3']])
			->widget(DateTimeWidget::class) ?>

		<?= $form->field($model, 'createdAtTo', ['options' => ['class' => 'col-md-3']])
			->widget(DateTimeWidget::class) ?>
	</div>

	<div class="row">


		<?= $form->field($model, 'role', ['options' => ['class' => 'col-md-12']])
			->widget(Select2::class, [
					'data' => WorkersWithoutIssuesSearch::getRolesNames(),
					'options' => [
						'multiple' => true,
						'placeholder' => $model->getAttributeLabel('role'),
					],
					'pluginOptions' => [
						'allowClear' => true,
					],
				]
			) ?>


	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('common', 'Search'), ['class' => 'btn btn-primary']) ?>
		<?= Html::a(Yii::t('common', 'Reset'), 'without-issues', ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
