<?php

use common\modules\lead\models\searches\LeadSearch;
use common\widgets\address\AddressSearchWidget;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadSearch */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="lead-search">

	<?php $form = ActiveForm::begin([
		'action' => ['index'],
		'method' => 'get',
	]); ?>

	<div class="row">

		<?= $form->field($model, 'email', ['options' => ['class' => 'col-md-3']]) ?>

		<?= $form->field($model, 'provider', ['options' => ['class' => 'col-md-3']])->widget(Select2::class, [
			'data' => LeadSearch::getProvidersNames(),
			'pluginOptions' => [
				'placeholder' => $model->getAttributeLabel('provider'),
				'allowClear' => true,
			],
		]) ?>


	</div>

	<?php if ($model->scenario !== LeadSearch::SCENARIO_USER): ?>

		<div class="row">

			<?= $form->field($model, 'user_id', ['options' => ['class' => 'col-md-3']])->widget(Select2::class, [
				'data' => LeadSearch::getUsersNames(),
				'pluginOptions' => [
					'placeholder' => $model->getAttributeLabel('user_id'),
					'allowClear' => true,
				],
			])
			?>

			<?= $form->field($model, 'user_type', ['options' => ['class' => 'col-md-3']])->widget(Select2::class, [
				'data' => LeadSearch::getUserTypesNames(),
				'pluginOptions' => [
					'placeholder' => $model->getAttributeLabel('user_type'),
					'allowClear' => true,
				],
			])
			?>

			<?= $form->field($model, 'withoutUser', ['options' => ['class' => 'col-md-2']])->checkbox() ?>


		</div>

	<?php endif; ?>

	<div class="row">

		<?= $form->field($model, 'closedQuestions', ['options' => ['class' => 'col-md-6']])->widget(Select2::class, [
			'data' => LeadSearch::getClosedQuestionsNames(),
			'options' => [
				'multiple' => true,
			],
			'pluginOptions' => [
				'placeholder' => $model->getAttributeLabel('closedQuestions'),
				'allowClear' => true,
			],
		])
		?>

		<?= $form->field($model, 'withoutReport', ['options' => ['class' => 'col-md-2']])->checkbox() ?>

		<?= $form->field($model, 'duplicatePhone', ['options' => ['class' => 'col-md-1']])->checkbox() ?>

		<?= $form->field($model, 'duplicateEmail', ['options' => ['class' => 'col-md-1']])->checkbox() ?>


	</div>


	<?= AddressSearchWidget::widget([
		'form' => $form,
		'model' => $model->getAddressSearch(),
	]) ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Search'), ['class' => 'btn btn-primary']) ?>

		<?= Html::a(Yii::t('backend', 'Reset'), 'index', ['class' => 'btn btn-default']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
