<?php

use common\modules\court\models\LawsuitIssueForm;
use common\widgets\DateTimeWidget;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var LawsuitIssueForm $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="court-hearing-form">

	<?php $form = ActiveForm::begin(); ?>


	<div class="row">
		<div class="col-md-4 col-lg-3">
			<?= $form->field($model, 'court_id')->widget(Select2::class, [
				'data' => $model->getCourtsNames(),
				'pluginOptions' => [
					'placeholder' => Yii::t('court', 'Court'),
				],
			]) ?>
		</div>
		<div class="col-md-3">
			<?= $form->field($model, 'signature_act')->textInput(['maxlength' => true]) ?>

		</div>
	</div>


	<div class="row">
		<div class="col-md-3 col-lg-2">
			<?= $form->field($model, 'due_at')->widget(DateTimeWidget::class) ?>
		</div>
		<div class="col-md-2 col-lg-1">
			<?= $form->field($model, 'room')->textInput(['maxlength' => true]) ?>
		</div>

		<div class="col-md-2 col-lg-1">

			<?= $form->field($model, 'location')
				->dropDownList(LawsuitIssueForm::getLocationNames(), [
					'prompt' => '---',
				]) ?>
		</div>

		<div class="col-md-2">

			<?= $form->field($model, 'presence_of_the_claimant')
				->dropDownList(LawsuitIssueForm::getPresenceOfTheClaimantNames(), [
					'prompt' => '---',
				]) ?>
		</div>

	</div>


	<div class="row">
		<div class="col-md-6">
			<?= $form->field($model, 'details')->textArea() ?>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<?= !empty($model->getLinkedIssuesNames())
				? $form->field($model, 'issuesIds')
					->widget(Select2::class, [
						'data' => $model->getLinkedIssuesNames(),
						'options' => [
							'multiple' => true,
						],
					])
				: ''
			?>
		</div>
	</div>

	<div class="form-group">
		<?= Html::submitButton(Yii::t('court', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
