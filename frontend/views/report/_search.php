<?php

use common\models\provision\ProvisionReportSearch;
use common\widgets\DateTimeWidget;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ProvisionReportSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="provision-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<div class="form-group row">
		<?= $form->field($model, 'payStatus', ['options' => ['class' => 'col-md-2']])->dropDownList(ProvisionReportSearch::getPayStatusNames()) ?>


		<?= $form->field($model, 'dateFrom', ['options' => ['class' => 'col-md-2']])
			->widget(DateTimeWidget::class,
				[
					'phpDatetimeFormat' => 'yyyy-MM-dd',
				]) ?>

		<?= $form->field($model, 'dateTo', ['options' => ['class' => 'col-md-2']])
			->widget(DateTimeWidget::class,
				[
					'phpDatetimeFormat' => 'yyyy-MM-dd',
				]) ?>
		<?= $form->field($model, 'from_user_id', ['options' => ['class' => 'col-md-6']])
			->widget(Select2::class, [
					'data' => $model->getFromUserList(),
					'options' => [
						'placeholder' => 'Agent',
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
