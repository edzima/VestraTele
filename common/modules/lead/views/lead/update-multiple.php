<?php

use common\helpers\Html;
use common\modules\lead\models\forms\LeadMultipleUpdate;
use common\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\web\View;

/* @var $this View */
/* @var $model LeadMultipleUpdate */

$this->title = Yii::t('lead', 'Update Leads: {count}', ['count' => count($model->ids)]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('lead', 'Leads'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="lead-update-multiple">

	<h1><?= Html::encode($this->title) ?></h1>

	<?php $form = ActiveForm::begin([
		'id' => 'lead-update-multiple-form',
	]); ?>

	<?= Html::hiddenInput('leadsIds', implode(',', $model->ids)) ?>

	<div class="row">

		<?= $form->field($model->getStatusModel(), 'status_id', [
			'options' => [
				'class' => 'col-md-6 col-lg-3',
			],
		])->widget(Select2::class, [
			'data' => $model->getStatusModel()::getStatusNames(),
			'pluginOptions' => [
				'placeholder' => $model->getStatusModel()->getAttributeLabel('status_id'),
				'allowClear' => true,
			],
		])
		?>

		<?= $form->field($model->getSourceModel(), 'source_id', [
			'options' => [
				'class' => 'col-md-6 col-lg-4',
			],
		])->widget(Select2::class, [
			'data' => $model->getSourceModel()::getSourcesNames(),
			'pluginOptions' => [
				'placeholder' => $model->getSourceModel()->getAttributeLabel('source_id'),
				'allowClear' => true,
			],
		])
		?>
	</div>

	<div class="row">

		<?= $form->field($model->getUsersModel(), 'userId', [
			'options' => [
				'class' => 'col-md-6 col-lg-4',
			],
		])->widget(Select2::class, [
			'data' => $model->getUsersModel()::getUsersNames(),
			'pluginOptions' => [
				'placeholder' => $model->getUsersModel()->getAttributeLabel('userId'),
				'allowClear' => true,
			],
		]) ?>

		<?= $form->field($model->getUsersModel(), 'type', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->widget(Select2::class, [
			'data' => $model->getUsersModel()->getTypesNames(),
		]) ?>

		<?= $form->field($model->getUsersModel(), 'sendEmail', [
			'options' => [
				'class' => 'col-md-2 col-lg-1',
			],
		])->checkbox() ?>
	</div>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
