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

<div class="provision-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<div class="row">
		<?= $form->field($model, 'dateFrom', ['options' => ['class' => 'col-md-2']])
			->widget(DateWidget::class) ?>

		<?= $form->field($model, 'dateTo', ['options' => ['class' => 'col-md-2']])
			->widget(DateWidget::class) ?>

		<?= $form->field($model, 'to_user_id', ['options' => ['class' => 'col-md-3']])
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
		<div class="col-md-5">
			<div class="pull-right">
				<?= LastCurrentNextMonthNav::widget([
					'model' => $model,
					'dateFromAttribute' => 'dateFrom',
					'dateToAttribute' => 'dateTo',
				]) ?>
			</div>

		</div>
	</div>


	<div class="form-group">
		<?= Html::submitButton('Szukaj', ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Reset', 'index', ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
