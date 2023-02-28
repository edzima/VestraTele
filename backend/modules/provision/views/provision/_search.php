<?php

use common\models\provision\ProvisionSearch;
use common\widgets\DateWidget;
use common\widgets\LastCurrentNextMonthNav;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ProvisionSearch */
/* @var $form yii\widgets\ActiveForm */
/* @var $action string */
?>

<div class="provision-search">

	<?php $form = ActiveForm::begin([
		'action' => [$action],
		'method' => 'get',
	]); ?>

	<div class="row">


		<?= $form->field($model, 'payStatus', ['options' => ['class' => 'col-md-2']])->dropDownList(ProvisionSearch::getPayStatusNames()) ?>


		<?= $form->field($model, 'dateFrom', ['options' => ['class' => 'col-md-2']])
			->widget(DateWidget::class)
			->hint($model->isUnpaid()
				? Yii::t('settlement', 'Deadline at')
				: Yii::t('settlement', 'Pay at')
			)
		?>

		<?= $form->field($model, 'dateTo', ['options' => ['class' => 'col-md-2']])
			->widget(DateWidget::class)
			->hint($model->isUnpaid()
				? Yii::t('settlement', 'Deadline at')
				: Yii::t('settlement', 'Pay at')
			)
		?>

		<div class="col-md-6">
			<div class="pull-right">
				<?= LastCurrentNextMonthNav::widget([
					'model' => $model,
					'route' => [$action],
					'dateFromAttribute' => 'dateFrom',
					'dateToAttribute' => 'dateTo',
				]) ?>
			</div>
		</div>

	</div>

	<div class="form-group row">


		<?= $form->field($model, 'to_user_id', ['options' => ['class' => 'col-md-3']])
			->widget(Select2::class, [
					'data' => $model->getToUsersList(),
					'options' => [
						'placeholder' => 'Agent',
					],
					'pluginOptions' => [
						'allowClear' => true,
					],
				]
			) ?>

		<?= $form->field($model, 'from_user_id', ['options' => ['class' => 'col-md-3']])
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


		<?= $form->field($model, 'hide_on_report', ['options' => ['class' => 'col-md-2']])->checkbox() ?>


	</div>


	<div class="form-group">
		<?= Html::submitButton('Szukaj', ['class' => 'btn btn-primary']) ?>
		<?= Html::a('Reset', $action, ['class' => 'btn btn-outline-secondary']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
