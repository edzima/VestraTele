<?php

use backend\modules\issue\models\IssueClaimForm;
use common\models\issue\IssueClaim;
use common\widgets\ActiveForm;
use common\widgets\DateWidget;
use kartik\number\NumberControl;
use kartik\select2\Select2;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model IssueClaimForm */
/* @var $form yii\widgets\ActiveForm|null */
/* @var $onlyField bool */

?>

<div class="issue-claim-form">

	<?php if (!$onlyField): ?>
		<?php $form = ActiveForm::begin(); ?>
	<?php endif; ?>


	<div class="row">

		<?= !empty($model->getLinkedIssuesNames())
			? $form->field($model, 'linkedIssuesIds', [
				'options' => [
					'class' => 'col-md-12',
				],
			])
				->widget(Select2::class, [
					'data' => $model->getLinkedIssuesNames(),
					'options' => [
						'multiple' => true,
					],
				])
				->hint(Yii::t('issue', 'Claim also in Linked Issues.'))
			: ''
		?>

		<?= $model->isTypeScenario()
			? $form->field($model, 'type', [
				'options' => [
					'class' => 'col-md-2',
				],
			])->dropDownList(IssueClaimForm::getTypesNames())
			: ''
		?>

		<?= $form->field($model, 'entity_responsible_id', [
			'options' => [
				'class' => 'col-md-2',
			],
		])->widget(Select2::class, [
			'data' => IssueClaim::getEntityResponsibleNames(),
		])
		?>

		<?= $form->field($model, 'date', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->widget(DateWidget::class) ?>




		<?= $form->field($model, 'trying_value', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->widget(NumberControl::class) ?>

		<?= $form->field($model, 'percent_value', [
			'options' => [
				'class' => 'col-md-2 col-lg-1',
			],
		])->widget(NumberControl::class) ?>


		<?= $form->field($model, 'obtained_value', [
			'options' => [
				'class' => 'col-md-3 col-lg-2',
			],
		])->widget(NumberControl::class) ?>


	</div>

	<div class="row">
		<?= $form->field($model, 'details', [
			'options' => [
				'class' => 'col-md-4',
			],
		])->textarea(['maxlength' => true]) ?>

	</div>


	<?php if (!$onlyField): ?>
		<div class="form-group">
			<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-success']) ?>
		</div>

		<?php ActiveForm::end(); ?>

	<?php endif; ?>

</div>
