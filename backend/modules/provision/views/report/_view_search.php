<?php

use common\models\provision\ToUserGroupProvisionSearch;
use common\widgets\DateWidget;
use common\widgets\LastCurrentNextMonthNav;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ToUserGroupProvisionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="provision-search no-print">

	<?php $form = ActiveForm::begin([
		'action' => ['view', 'id' => $model->to_user_id],
		'method' => 'get',
	]); ?>

	<div class="row">
		<?= $form->field($model, 'dateFrom', ['options' => ['class' => 'col-md-2 col-lg-1']])
			->widget(DateWidget::class) ?>

		<?= $form->field($model, 'dateTo', ['options' => ['class' => 'col-md-2 col-lg-1']])
			->widget(DateWidget::class) ?>


		<?= $form->field($model, 'withoutEmpty', ['options' => ['class' => 'col-md-1']])->checkbox() ?>

		<?= $form->field($model, 'excludedFromUsers', ['options' => ['class' => 'col-md-3 col-lg-4']])
			->widget(Select2::class, [
					'data' => $model->getToUsersList(),
					'options' => [
						'placeholder' => Yii::t('provision', 'User'),
						'multiple' => true,
					],
					'pluginOptions' => [
						'allowClear' => true,
					],
				]
			) ?>

		<div class="col-md-4 col-lg-5">
			<div class="pull-right">
				<?= LastCurrentNextMonthNav::widget([
					'model' => $model,
					'route' => ['view', 'id' => $model->to_user_id],
					'dateFromAttribute' => 'dateFrom',
					'dateToAttribute' => 'dateTo',
					'dateFromParamName' => 'dateFrom',
					'dateToParamName' => 'dateTo',
				]) ?>
			</div>

		</div>
	</div>


	<div class="form-group">
		<?= Html::submitButton('Szukaj', ['class' => 'btn btn-primary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
