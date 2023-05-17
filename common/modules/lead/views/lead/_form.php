<?php

use common\models\user\User;
use common\modules\lead\models\forms\LeadForm;
use common\widgets\DateTimeWidget;
use common\widgets\PhoneInput;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-form">

	<?php $form = ActiveForm::begin([
		'id' => 'lead-form',
	]); ?>

	<div class="row">

		<?= Yii::$app->user->can(User::PERMISSION_LEAD_STATUS)
			? $form->field($model, 'status_id', ['options' => ['class' => 'col-md-3 col-lg-2']])
				->widget(Select2::class, [
					'data' => LeadForm::getStatusNames(),
				])
			: ''
		?>


		<?= $form->field($model, 'source_id', [
			'options' => [
				'class' => 'col-md-4 col-lg-3',
			],
		])
			->widget(Select2::class, [
					'data' => $model->getSourcesNames(),
					'options' => [
						'placeholder' => $model->getAttributeLabel('source_id'),
					],
				]
			)
		?>


		<?= $model->scenario !== LeadForm::SCENARIO_OWNER
			? $form->field($model, 'provider', [
				'options' => [
					'class' => 'col-md-2 col-lg-1',
				],
			])
				->widget(Select2::class, [
						'data' => LeadForm::getProvidersNames(),
						'options' => [
							'placeholder' => $model->getAttributeLabel('provider'),
						],
					]
				)
			: ''
		?>

		<?php
		//		$form->field($model, 'campaign_id', [
		//			'options' => [
		//				'class' => 'col-md-3 col-lg-2',
		//			],
		//		])
		//			->widget(Select2::class, [
		//					'data' => $model->getCampaignsNames(),
		//					'options' => [
		//						'placeholder' => $model->getAttributeLabel('campaign_id'),
		//					],
		//				]
		//			)
		?>


		<?= $form->field($model, 'date_at', ['options' => ['class' => 'col-md-3 col-lg-2']])->widget(DateTimeWidget::class, [
			'phpDatetimeFormat' => 'yyyy-MM-dd HH:mm:ss',
			'options' => [
				'readonly' => !empty($model->date_at),
			],
		]) ?>

	</div>


	<div class="row">

		<?= $form->field($model, 'name', ['options' => ['class' => 'col-md-4 col-lg-3']])->textInput(['maxlength' => true]) ?>

		<?= $form->field($model, 'phone', ['options' => ['class' => 'col-md-2']])->widget(PhoneInput::class) ?>

		<?= $form->field($model, 'email', ['options' => ['class' => 'col-md-2']])->textInput(['maxlength' => true]) ?>

	</div>


	<div class="row">
		<?= $model->scenario !== LeadForm::SCENARIO_OWNER
			? $form->field($model, 'owner_id', [
				'options' => [
					'class' => 'col-md-3 col-lg-2',
				],
			])->widget(Select2::class, [
				'data' => LeadForm::getUsersNames(),
				'pluginOptions' => [
					'placeholder' => $model->getAttributeLabel('owner_id'),
					'allowClear' => true,
				],
			])
			: ''
		?>
	</div>


	<div class="row">

		<?= $form->field($model, 'details', ['options' => ['class' => 'col-md-5']])->textarea(['maxlength' => true]) ?>

	</div>

	<?php //form->field($model, 'data')->textarea(['rows' => 6]) ?>


	<div class="form-group">
		<?= Html::submitButton(Yii::t('lead', 'Save'), ['class' => 'btn btn-success']) ?>
	</div>

	<?php ActiveForm::end(); ?>

</div>
