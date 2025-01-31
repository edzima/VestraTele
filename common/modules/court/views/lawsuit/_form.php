<?php

use common\models\issue\IssueInterface;
use common\modules\court\models\LawsuitIssueForm;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var LawsuitIssueForm $model */
/** @var yii\widgets\ActiveForm $form */
/** @var IssueInterface|null $issue */

?>

<div class="court-hearing-form">

	<?php $form = ActiveForm::begin(); ?>

	<?= $form->field($model, 'is_appeal')->checkbox() ?>

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
			<?= $issue !== null && $model->getAlreadyExistedLawsuit()
				? Html::a(
					Yii::t('court', 'Link {issue} to {lawsuit}', [
						'issue' => $issue->getIssueName(),
						'lawsuit' => $model->signature_act,
					]),
					[
						'link-issue',
						'id' => $model->getAlreadyExistedLawsuit()->id,
						'issueId' => $issue->getIssueId(),
					], [
					'data-method' => 'POST',
				])
				: ''
			?>
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
